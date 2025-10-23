<?php

namespace App\Livewire\Employee;

use App\Models\Team;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.employee.home')]
class EmployeeForm extends Component
{
    // Form fields
    public ?int $userId = null;
    public string $name = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';
    public string $role = 'employee';
    
    // UI State
    public bool $isEditMode = false;
    public bool $showPassword = false;
    
    /**
     * Available roles for team members
     */
    public array $availableRoles = [
        'super_admin' => 'Super Admin',
        'ceo' => 'CEO/Director',
        'franchise_owner' => 'Franchise Owner',
        'regional_manager' => 'Regional Manager',
        'manager' => 'Manager',
        'supervisor' => 'Supervisor',
        'employee' => 'Employee',
        'auditor' => 'Auditor',
    ];

    /**
     * Mount component
     */
    public function mount(?int $userId = null): void
    {
        $this->userId = $userId;
        $this->isEditMode = $userId !== null;

        if ($this->isEditMode) {
            $this->loadUser($userId);
        }
    }

    /**
     * Load user data for editing
     */
    protected function loadUser(int $userId): void
    {
        $team = $this->currentTeam;
        
        if (!$team) {
            session()->flash('error', __('app.no_team_selected'));
            $this->redirect(route('root.employee.index'));
            return;
        }

        $user = $team->users()->find($userId);

        if (!$user) {
            session()->flash('error', __('app.employee_not_found'));
            $this->redirect(route('root.employee.index'));
            return;
        }

        $this->name = $user->name;
        $this->email = $user->email;
        $this->role = $user->membership?->role ?? 'employee';
    }

    /**
     * Get current team
     */
    #[Computed]
    public function currentTeam(): ?Team
    {
        return Auth::user()?->currentTeam;
    }

    /**
     * Validation rules
     */
    protected function rules(): array
    {
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->ignore($this->userId),
            ],
            'role' => ['required', 'string', Rule::in(array_keys($this->availableRoles))],
        ];

        // Password is required only for new users
        if (!$this->isEditMode) {
            $rules['password'] = ['required', 'string', 'min:8', 'confirmed'];
        } elseif (!empty($this->password)) {
            // Password is optional for updates, but must be confirmed if provided
            $rules['password'] = ['string', 'min:8', 'confirmed'];
        }

        return $rules;
    }

    /**
     * Custom validation messages
     */
    protected function messages(): array
    {
        return [
            'name.required' => __('validation.required', ['attribute' => __('app.name')]),
            'name.max' => __('validation.max.string', ['attribute' => __('app.name'), 'max' => 255]),
            'email.required' => __('validation.required', ['attribute' => __('app.email')]),
            'email.email' => __('validation.email', ['attribute' => __('app.email')]),
            'email.unique' => __('validation.unique', ['attribute' => __('app.email')]),
            'password.required' => __('validation.required', ['attribute' => __('app.password')]),
            'password.min' => __('validation.min.string', ['attribute' => __('app.password'), 'min' => 8]),
            'password.confirmed' => __('validation.confirmed', ['attribute' => __('app.password')]),
            'role.required' => __('validation.required', ['attribute' => __('app.role')]),
            'role.in' => __('validation.in', ['attribute' => __('app.role')]),
        ];
    }

    /**
     * Save employee (create or update)
     */
    public function save(): void
    {
        $team = $this->currentTeam;

        if (!$team) {
            session()->flash('error', __('app.no_team_selected'));
            return;
        }

        // Authorization check
        if (!Gate::forUser(Auth::user())->check('addTeamMember', $team)) {
            session()->flash('error', __('app.unauthorized_action'));
            return;
        }

        // Validate
        $validated = $this->validate();

        try {
            if ($this->isEditMode) {
                $this->updateEmployee($team, $validated);
            } else {
                $this->createEmployee($team, $validated);
            }
        } catch (\Exception $e) {
            session()->flash('error', __('app.error_occurred') . ': ' . $e->getMessage());
            return;
        }
    }

    /**
     * Create new employee
     */
    protected function createEmployee(Team $team, array $validated): void
    {
        // Create user
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        // Add user to team
        $team->users()->attach($user->id, [
            'role' => $validated['role'],
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        session()->flash('success', __('app.employee_created_successfully'));
        $this->redirect(route('root.employee.index'), navigate: true);
    }

    /**
     * Update existing employee
     */
    protected function updateEmployee(Team $team, array $validated): void
    {
        $user = User::findOrFail($this->userId);

        // Update user data
        $userData = [
            'name' => $validated['name'],
            'email' => $validated['email'],
        ];

        // Update password if provided
        if (!empty($validated['password'])) {
            $userData['password'] = Hash::make($validated['password']);
        }

        $user->update($userData);

        // Update role in team
        $team->users()->updateExistingPivot($user->id, [
            'role' => $validated['role'],
            'updated_at' => now(),
        ]);

        session()->flash('success', __('app.employee_updated_successfully'));
        $this->redirect(route('root.employee.index'), navigate: true);
    }

    /**
     * Cancel and go back
     */
    public function cancel(): void
    {
        $this->redirect(route('root.employee.index'), navigate: true);
    }

    /**
     * Toggle password visibility
     */
    public function togglePasswordVisibility(): void
    {
        $this->showPassword = !$this->showPassword;
    }

    /**
     * Render component
     */
    public function render(): View
    {
        return view('livewire.employee.employee-form');
    }
}

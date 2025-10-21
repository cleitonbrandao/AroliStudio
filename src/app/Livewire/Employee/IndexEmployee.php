<?php

namespace App\Livewire\Employee;

use App\Models\Team;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.employee.home')]
class IndexEmployee extends Component
{
    use WithPagination;

    public string $search = '';

    /**
     * Get current team
     */
    #[Computed]
    public function currentTeam(): ?Team
    {
        return Auth::user()?->currentTeam;
    }

    /**
     * Check if user can manage team members
     */
    #[Computed]
    public function canManageMembers(): bool
    {
        $team = $this->currentTeam;
        
        if (!$team) {
            return false;
        }

        /** @var User $user */
        $user = Auth::user();
        
        return $user->canManageTeam($team);
    }

    /**
     * Get team members with search filter
     */
    #[Computed]
    public function teamMembers()
    {
        $team = $this->currentTeam;

        if (!$team || !$this->canManageMembers) {
            return collect([]);
        }

        // Load users with their role from team_user pivot table
        return $team->users()
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%')
                        ->orWhere('email', 'like', '%' . $this->search . '%');
                });
            })
            ->withPivot('role')
            ->orderBy('name')
            ->paginate(10);
    }

    /**
     * Update search and reset pagination
     */
    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    /**
     * Get role label for translation
     */
    public function getRoleLabel(string $role): string
    {
        return match ($role) {
            'owner' => __('app.role_owner'),
            'admin' => __('app.role_admin'),
            'member' => __('app.role_member'),
            default => $role,
        };
    }

    /**
     * Get member role in current team
     */
    public function getMemberRole(User $member): string
    {
        // Jetstream uses 'membership' as pivot alias, not 'pivot'
        // See: vendor/laravel/jetstream/src/Team.php -> users() method
        return $member->membership->role ?? 'member';
    }

    /**
     * Render component
     */
    public function render(): View
    {
        return view('livewire.employee.index-employee', [
            'members' => $this->teamMembers,
        ]);
    }
}

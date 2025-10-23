<?php

namespace App\Livewire\Employee;

use App\Actions\Jetstream\RemoveTeamMember;
use App\Models\Team;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Laravel\Jetstream\Jetstream;
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
        // Try to get translation from team-invitations roles
        $translationKey = "team-invitations.roles.{$role}";
        $translated = __($translationKey);
        
        // If translation exists and is different from the key, return it
        if ($translated !== $translationKey) {
            return $translated;
        }
        
        // Fallback for legacy roles
        return match ($role) {
            'owner' => __('app.role_owner'),
            'admin' => __('app.role_admin'),
            'member' => __('app.role_member'),
            default => ucfirst(str_replace('_', ' ', $role)),
        };
    }

    /**
     * Get member role in current team
     */
    public function getMemberRole(User $member): string
    {
        // Jetstream uses 'membership' as pivot alias, not 'pivot'
        // See: vendor/laravel/jetstream/src/Team.php -> users() method
        return $member->membership?->role ?? 'member';
    }

    /**
     * Remove member from team using Jetstream's RemoveTeamMember action
     * Same flow as /teams/{id} page
     */
    public function removeMember(int $userId, RemoveTeamMember $remover): void
    {
        $team = $this->currentTeam;

        if (!$team) {
            $this->dispatch('show-toast', [
                'type' => 'error',
                'message' => __('app.no_team_selected')
            ]);
            return;
        }

        // Check if user can manage team members
        if (!$this->canManageMembers) {
            $this->dispatch('show-toast', [
                'type' => 'error',
                'message' => __('app.unauthorized_action')
            ]);
            return;
        }

        // Prevent user from removing themselves
        if ($userId === Auth::id()) {
            $this->dispatch('show-toast', [
                'type' => 'error',
                'message' => __('app.cannot_remove_yourself')
            ]);
            return;
        }

        try {
            // Find the user to be removed
            $teamMember = Jetstream::findUserByIdOrFail($userId);
            
            // Check if user exists in team
            if (!$team->users()->where('users.id', $userId)->exists()) {
                $this->dispatch('show-toast', [
                    'type' => 'error',
                    'message' => __('app.employee_not_found')
                ]);
                return;
            }

            // Use Jetstream's RemoveTeamMember action (same as /teams/{id})
            $remover->remove(
                Auth::user(),
                $team,
                $teamMember
            );

            // Reset pagination if current page is now empty
            $this->resetPage();

            $this->dispatch('show-toast', [
                'type' => 'success',
                'message' => __('app.employee_removed_successfully')
            ]);

        } catch (\Exception $e) {
            $this->dispatch('show-toast', [
                'type' => 'error',
                'message' => __('app.error_occurred')
            ]);
        }
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

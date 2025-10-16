<?php

namespace App\Livewire\Dashboard;

use Livewire\Component;
use App\Models\User;
use App\Models\Team;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;

use Illuminate\Support\Facades\Log;

#[Layout('layouts.dashboard.home')]
class HierarchyManager extends Component
{
    public $companies;
    public $selectedCompany = null;
    public $members = [];
    public $roles = [
        'ceo' => 'CEO',
        'regional_manager' => 'Gerente Regional',
        'franchisee' => 'Franqueado',
        'employee' => 'Funcionário',
    ];

    public function mount()
{
    $user = Auth::user(); // Garante que os teams estão atualizados
    $companies = $user->teams;

    Log::info('Debug HierarchyManager', [
        'user_id' => $user->id,
        'companies' => $companies->pluck('name','id')->toArray(),
    ]);

    if ($companies->count() > 0) {
        $this->companies = $companies;
        $this->selectedCompany = $companies->first()->id;
        $this->loadMembers();
    } else {
        $this->companies = collect();
        Log::info('Usuário sem empresas ou acesso ao dashboard/hierarchy', ['user_id' => $user->id, 'email' => $user->email]);
    }
}

    public function updatedSelectedCompany()
    {
        $this->loadMembers();
    }

    public function loadMembers()
    {
    $team = Team::find($this->selectedCompany);
    $this->members = $team ? $team->users()->withPivot('role')->get() : [];
    }

    public function hierarchy()
    {
        return view('livewire.dashboard.hierarchy-manager');
    }
}

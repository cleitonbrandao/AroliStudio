<?php

namespace App\Livewire\Dashboard;

use Livewire\Component;
use App\Models\User;
use App\Models\Team;
use Illuminate\Support\Facades\Auth;
use App\Models\Company;

use Illuminate\Support\Facades\Log;

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
        $user = Auth::user();
        $this->companies = $user->companies;
        if ($this->companies->count() > 0) {
            $this->selectedCompany = $this->companies->first()->id;
            $this->loadMembers();
        } else {
            Log::info('Usuário sem empresas ou acesso ao dashboard/hierarchy', ['user_id' => $user->id, 'email' => $user->email]);
        }
    }

    public function updatedSelectedCompany()
    {
        $this->loadMembers();
    }

    public function loadMembers()
    {
        $company = Company::find($this->selectedCompany);
        $this->members = $company ? $company->users()->withPivot('role')->get() : [];
    }

    public function hierarchy()
    {
        return view('livewire.dashboard.hierarchy-manager');
    }
}

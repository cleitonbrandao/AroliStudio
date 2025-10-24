<?php

namespace App\Livewire\Companies;

use App\Models\Team;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Index extends Component
{
    public $companies = [];

    public function mount()
    {
        $this->loadCompanies();
    }

    public function loadCompanies()
    {
        $user = Auth::user();
        $this->companies = $user->allTeams()
            ->map(function ($company) use ($user) {
                $role = $user->teamRole($company);
                return [
                    'id' => $company->id,
                    'name' => $company->name,
                    'slug' => $company->slug ?? '',
                    'role' => $role ? $role->key : 'member',
                    'is_owner' => $role ? $role->key === 'owner' : false,
                    'created_at' => $company->created_at,
                ];
            });
    }

    public function switchCompany($companyId)
    {
        $user = Auth::user();
        $company = Team::find($companyId);
        
        if ($company && $user->belongsToTeam($company)) {
            session(['active_company_id' => $companyId]);
            
            // Definir como current team
            $user->forceFill([
                'current_team_id' => $companyId,
            ])->save();
            
            $this->dispatch('company-switched', $company->name);
            return redirect()->route('root.dashboard.hierarchy');
        }
        
        session()->flash('error', 'Empresa não encontrada ou você não tem acesso.');
    }

    public function render()
    {
        return view('livewire.companies.index');
    }
}

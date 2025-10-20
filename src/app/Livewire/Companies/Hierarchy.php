<?php

namespace App\Livewire\Companies;

use App\Services\FranchiseService;
use Livewire\Component;

class Hierarchy extends Component
{
    public $user;
    public $companies = [];
    public $selectedCompany = null;

    public function mount()
    {
        $this->user = auth()->user();
        $this->loadUserCompanies();
    }

    public function loadUserCompanies()
    {
        $this->companies = $this->franchiseService->getUserCompaniesWithRoles($this->user);
    }

    public function selectCompany($companyId)
    {
        $this->selectedCompany = collect($this->companies)
            ->firstWhere('company.id', $companyId);
    }

    public function getFranchiseServiceProperty()
    {
        return app(FranchiseService::class);
    }

    public function render()
    {
        return view('livewire.companies.hierarchy', [
            'franchiseExample' => $this->franchiseService->getFranchiseExample(),
        ]);
    }
}

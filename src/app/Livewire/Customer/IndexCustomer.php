<?php

namespace App\Livewire\Customer;

use App\Models\Costumer;
use App\Services\CustomerService;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class IndexCustomer extends Component
{
    use WithPagination;

    #[Url(as: 'q')]
    public string $search = '';
    
    public int $perPage = 15;
    
    protected CustomerService $customerService;

    /**
     * Boot method para injetar service
     */
    public function boot(CustomerService $customerService): void
    {
        $this->customerService = $customerService;
    }

    /**
     * Atualizar busca e resetar paginação
     */
    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    /**
     * Navegar para criação de novo customer
     */
    public function create(): void
    {
        $this->redirect(route('customers.create'), navigate: true);
    }

    /**
     * Navegar para edição de customer
     */
    public function edit(int $customerId): void
    {
        $this->redirect(route('customers.edit', $customerId), navigate: true);
    }

    /**
     * Render component
     */
    public function render(): View
    {
        $team = Auth::user()?->currentTeam;
        
        $customers = $this->customerService->list(
            search: $this->search,
            perPage: $this->perPage,
            team: $team
        );

        $statistics = $this->customerService->getStatistics($team);

        return view('livewire.customer.index-customer', [
            'customers' => $customers,
            'statistics' => $statistics,
        ]);
    }
}

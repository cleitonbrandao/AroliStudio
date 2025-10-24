<?php

namespace App\Livewire\Customer;

use App\Models\Customer;
use App\Services\CustomerService;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class CustomersTable extends Component
{
    use WithPagination;

    // Busca e Filtros
    #[Url(as: 'q')]
    public string $search = '';
    
    #[Url(as: 'status')]
    public string $filterStatus = 'all'; // all, active, incomplete
    
    #[Url(as: 'sort')]
    public string $sortField = 'created_at';
    
    #[Url(as: 'dir')]
    public string $sortDirection = 'desc';
    
    public int $perPage = 15;
    
    // Modal de Confirmação
    public bool $showDeleteModal = false;
    public ?int $customerToDelete = null;
    
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
     * Atualizar filtro de status e resetar paginação
     */
    public function updatedFilterStatus(): void
    {
        $this->resetPage();
    }

    /**
     * Ordenar tabela por campo
     */
    public function sortBy(string $field): void
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }

    /**
     * Limpar todos os filtros
     */
    public function clearFilters(): void
    {
        $this->search = '';
        $this->filterStatus = 'all';
        $this->sortField = 'created_at';
        $this->sortDirection = 'desc';
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
     * Confirmar exclusão de customer
     */
    public function confirmDelete(int $customerId): void
    {
        $this->customerToDelete = $customerId;
        $this->showDeleteModal = true;
    }

    /**
     * Cancelar exclusão
     */
    public function cancelDelete(): void
    {
        $this->customerToDelete = null;
        $this->showDeleteModal = false;
    }

    /**
     * Excluir customer
     */
    public function delete(): void
    {
        if (!$this->customerToDelete) {
            return;
        }

        try {
            $customer = Customer::auth()->findOrFail($this->customerToDelete);
            $this->customerService->delete($customer);

            session()->flash('success', 'Cliente excluído com sucesso!');
            
            $this->cancelDelete();
            $this->resetPage();
        } catch (\Exception $e) {
            session()->flash('error', 'Erro ao excluir cliente: ' . $e->getMessage());
        }
    }

    /**
     * Render component
     */
    public function render(): View
    {
        $team = Auth::user()?->currentTeam;
        
        // Buscar customers com filtros
        $query = Customer::with('people', 'team')
            ->auth();

        // Aplicar busca
        if ($this->search) {
            $query->search($this->search);
        }

        // Aplicar filtro de status
        if ($this->filterStatus === 'active') {
            $query->whereNotNull('cpf')->whereNotNull('email');
        } elseif ($this->filterStatus === 'incomplete') {
            $query->where(function ($q) {
                $q->whereNull('cpf')->orWhereNull('email');
            });
        }

        // Aplicar ordenação
        $query->orderBy($this->sortField, $this->sortDirection);

        $customers = $query->paginate($this->perPage);

        // Estatísticas
        $statistics = $this->customerService->getStatistics($team);

        return view('livewire.customer.customers-table', [
            'customers' => $customers,
            'statistics' => $statistics,
        ]);
    }
}

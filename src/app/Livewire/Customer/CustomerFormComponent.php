<?php

namespace App\Livewire\Customer;

use App\Livewire\Forms\Customer\CustomerForm;
use App\Models\Costumer;
use App\Services\CustomerService;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.customer.home')]
class CustomerFormComponent extends Component
{
    public CustomerForm $form;
    
    public ?int $customerId = null;
    public bool $isEditMode = false;
    public bool $showDeleteConfirm = false;
    
    protected CustomerService $customerService;

    /**
     * Boot method para injetar service
     */
    public function boot(CustomerService $customerService): void
    {
        $this->customerService = $customerService;
    }

    /**
     * Mount component
     */
    public function mount(?int $customerId = null): void
    {
        $this->customerId = $customerId;
        
        if ($customerId) {
            $this->isEditMode = true;
            $this->form->setCustomer($customerId);
        }
    }

    /**
     * Salvar ou atualizar customer
     */
    public function save(): void
    {
        // Validar formulário
        $this->form->validate();
        
        try {
            $team = Auth::user()->currentTeam;
            
            if (!$team) {
                session()->flash('error', 'Você precisa estar vinculado a um time para realizar esta ação.');
                return;
            }
            
            $data = $this->form->getData();
            
            if ($this->isEditMode && $this->form->customer) {
                // Atualizar customer existente
                $customer = $this->customerService->update($this->form->customer, $data);
                
                session()->flash('success', 'Cliente atualizado com sucesso!');
                
                // Recarregar dados
                $this->form->setCustomer($customer->id);
                
            } else {
                // Criar novo customer
                $customer = $this->customerService->create($data, $team);
                
                session()->flash('success', 'Cliente cadastrado com sucesso!');
                
                // Redirecionar para edição ou lista
                $this->redirect(route('root.customers.list'), navigate: true);
            }
            
        } catch (\Exception $e) {
            session()->flash('error', 'Erro ao salvar cliente: ' . $e->getMessage());
        }
    }

    /**
     * Confirmar exclusão
     */
    public function confirmDelete(): void
    {
        $this->showDeleteConfirm = true;
    }

    /**
     * Cancelar exclusão
     */
    public function cancelDelete(): void
    {
        $this->showDeleteConfirm = false;
    }

    /**
     * Deletar customer
     */
    public function delete(): void
    {
        if (!$this->isEditMode || !$this->form->customer) {
            session()->flash('error', 'Cliente não encontrado.');
            return;
        }
        
        try {
            // Deletar customer (sem deletar a pessoa)
            $this->customerService->delete($this->form->customer, false);
            
            session()->flash('success', 'Cliente excluído com sucesso!');
            
            // Redirecionar para lista
            $this->redirect(route('customers.index'), navigate: true);
            
        } catch (\Exception $e) {
            session()->flash('error', 'Erro ao excluir cliente: ' . $e->getMessage());
            $this->showDeleteConfirm = false;
        }
    }

    /**
     * Cancelar e voltar para lista
     */
    public function cancel(): void
    {
        $this->redirect(route('root.customers.list'), navigate: true);
    }

    /**
     * Limpar formulário
     */
    public function clear(): void
    {
        $this->form->reset();
        $this->isEditMode = false;
        $this->customerId = null;
        $this->showDeleteConfirm = false;
        
        session()->flash('info', 'Formulário limpo.');
    }

    /**
     * Render component
     */
    public function render(): View
    {
        return view('livewire.customer.customer-form-component');
    }
}

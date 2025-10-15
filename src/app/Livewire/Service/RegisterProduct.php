<?php

namespace App\Livewire\Service;

use App\Livewire\Forms\Service\ProductForm;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.service.home')]
class RegisterProduct extends Component
{
    public ProductForm $form;
    
    public function mount()
    {
        // Inicializa o team_id quando o componente é montado
        $user = Auth::user();
        
        if (!$user || !$user->currentTeam) {
            session()->flash('error', 'Você precisa estar associado a uma equipe para criar produtos.');
            return $this->redirect(route('dashboard'), navigate: true);
        }
        
        $this->form->team_id = $user->currentTeam->id;
    }
    
    public function save()
    {
        $this->form->store();
        
        session()->flash('success', 'Produto cadastrado com sucesso!');
        
        return $this->redirect(route('root.negotiable'), navigate: true);
    }
    
    public function render(): View
    {
        return view('livewire.service.form-product');
    }
}
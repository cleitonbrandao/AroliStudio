<?php

namespace App\Livewire\Service;

use App\Livewire\Forms\Service\ServiceForm;
use App\Models\Service;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('layouts.service.home')]
class RegisterService extends Component
{
    public ServiceForm $form;

    /**
     * Save service
     */
    public function save()
    {
        $this->form->validate();

        try {
            $service = Service::create([
                'name' => $this->form->name,
                'service_time' => $this->form->service_time,
                'price' => $this->form->price,
                'cost_price' => $this->form->cost_price,
                'description' => $this->form->description,
                'team_id' => Auth::user()->currentTeam->id,
            ]);

            session()->flash('success', __('Serviço cadastrado com sucesso!'));
            
            // Reset form
            $this->form->reset();
            
            // Redirect to services list
            return $this->redirect(route('root.negotiable'), navigate: true);
            
        } catch (\Exception $e) {
            session()->flash('error', __('Erro ao cadastrar serviço: ') . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.service.register-service');
    }
}

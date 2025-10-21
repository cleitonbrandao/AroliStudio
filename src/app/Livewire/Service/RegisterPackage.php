<?php

namespace App\Livewire\Service;

use App\Livewire\Forms\Service\PackageForm;
use App\Models\Package;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Arr;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;

#[Layout('layouts.service.home')]
class RegisterPackage extends Component
{
    public PackageForm $form;

    /**
     * Listen for items updates from child components
     */
    #[On('items-updated')]
    public function updateItems($items)
    {
        $this->form->items = $items;
    }

    /**
     * Save package
     */
    public function save()
    {
        // Valida campos básicos
        $this->form->validate();

        // Valida se tem pelo menos um item
        if (!$this->form->hasItems()) {
            session()->flash('error', __('Pacote deve conter ao menos um item!'));
            return;
        }

        try {
            // Cria o pacote
            $package = Package::create([
                'name' => $this->form->name,
                'price' => $this->form->price,
                'description' => $this->form->description,
                'team_id' => Auth::user()->currentTeam->id,
            ]);

            // Anexa os itens relacionados
            if (Arr::exists($this->form->items, 'products') && !empty($this->form->items['products'])) {
                $package->products()->attach($this->form->items['products']);
            }
            
            if (Arr::exists($this->form->items, 'services') && !empty($this->form->items['services'])) {
                $package->services()->attach($this->form->items['services']);
            }
            
            if (Arr::exists($this->form->items, 'packages') && !empty($this->form->items['packages'])) {
                $package->groups()->attach($this->form->items['packages']);
            }

            // Limpa cache
            Cache::forget('packages_items');

            session()->flash('success', __('Pacote cadastrado com sucesso!'));
            
            // Reset form
            $this->form->reset();
            
            // Dispatch event para limpar items no componente filho
            $this->dispatch('package-saved');
            
            // Redirect to packages list
            return $this->redirect(route('root.negotiable'), navigate: true);
            
        } catch (\Exception $e) {
            session()->flash('error', __('Erro ao cadastrar pacote: ') . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.service.register-package');
    }
}

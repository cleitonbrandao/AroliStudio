<?php

namespace App\Livewire\Components\Service;

use App\Enums\Blades;
use App\Livewire\Forms\Service\ProductForm;
use App\Models\Product;
use Illuminate\View\View;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Locked;
use Livewire\Attributes\On;
use LivewireUI\Modal\ModalComponent;

class ProductsForm extends ModalComponent
{
    public ?ProductForm $form;
    public $productId = null;

    public function mount(?int $productId = null)
    {
        $this->productId = $productId;
        if ($productId) {
            $this->form->setProduct(Product::findOrFail($productId));
        }
    }

    public function submit(): void
    {
        if ($this->productId) {
            $this->form->update();
            session()->flash('status', 'Produto Atualizado com Sucesso.');
        } else {
            $this->form->store();
            session()->flash('status', 'Produto Cadastrado com Sucesso.');
        }

        // Se estiver em modal, fecha. Se for página, redireciona.
        if (method_exists($this, 'closeModal')) {
            $this->closeModal();
        } else {
            redirect()->route('product.create'); // ajuste para rota desejada
        }
    }

    public function render(): View
    {
        // Se não estiver em modal, usa o layout de página
        if (!$this->isModal()) {
            return view(Blades::FORM_PRODUCTS)
                ->layout('layouts.service.home', [
                    'title' => 'Cadastrar Produto',
                    'navLinks' => [
                        ['text' => 'Lista', 'route' => 'root.negotiable', 'active' => 'root.negotiable'],
                        ['text' => 'Cadastrar - Produtos', 'route' => 'root.form.product', 'active' => 'root.form.product'],
                        ['text' => 'Cadastrar - Serviços', 'route' => 'root.form.service', 'active' => 'root.form.service'],
                        ['text' => 'Cadastrar - Pacotes', 'route' => 'root.form.package', 'active' => 'root.form.package'],
                    ]
                ]);
        }
        return view(Blades::FORM_PRODUCTS);
    }

    protected function isModal(): bool
    {
        // Detecta se está rodando como modal (wire-elements-modal)
        return request()->hasHeader('X-Livewire-Modal');
    }
}

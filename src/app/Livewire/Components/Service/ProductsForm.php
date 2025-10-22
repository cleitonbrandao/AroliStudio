<?php

namespace App\Livewire\Components\Service;

use App\Enums\Blade;
use App\Enums\Route;
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
            $this->form->setProduct($productId);
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
        }
    }

    public function render(): View
    {
        // Se não estiver em modal, usa o layout de página
        if (!$this->isModal()) {
            return view(Blade::LIVEWIRE_SERVICE_FORM_PRODUCTS)
                ->layout(Blade::LAYOUTS_SERVICE_HOME, [
                    'title' => 'Cadastrar Produto',
                    'navLinks' => [
                        ['text' => 'Lista', 'route' => Route::WEB_ROOT_NEGOTIABLE, 'active' => Route::WEB_ROOT_NEGOTIABLE],
                        ['text' => 'Cadastrar - Produtos', 'route' => Route::WEB_ROOT_FORM_PRODUCT, 'active' => Route::WEB_ROOT_FORM_PRODUCT],
                        ['text' => 'Cadastrar - Serviços', 'route' => Route::WEB_ROOT_FORM_SERVICE, 'active' => Route::WEB_ROOT_FORM_SERVICE],
                        ['text' => 'Cadastrar - Pacotes', 'route' => Route::WEB_ROOT_FORM_PACKAGE, 'active' => Route::WEB_ROOT_FORM_PACKAGE],
                    ]
                ]);
        }
        return view(Blade::LIVEWIRE_SERVICE_FORM_PRODUCTS);
    }

    protected function isModal(): bool
    {
        // Detecta se está rodando como modal (wire-elements-modal)
        return request()->hasHeader('X-Livewire-Modal');
    }
}

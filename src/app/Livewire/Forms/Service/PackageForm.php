<?php

namespace App\Livewire\Forms\Service;

use Livewire\Attributes\Validate;
use Livewire\Form;

class PackageForm extends Form
{
    #[Validate('required|string|max:255|unique:packages,name')]
    public string $name = '';

    #[Validate('nullable|numeric|min:0')]
    public string $price = '';

    #[Validate('nullable|string|max:1000')]
    public ?string $description = null;

    // Items serão gerenciados pelo componente ItemsPackage
    public array $items = [
        'services' => [],
        'products' => [],
        'packages' => [],
    ];

    /**
     * Reset form fields
     */
    public function reset(...$properties): void
    {
        $this->name = '';
        $this->price = '';
        $this->description = null;
        $this->items = [
            'services' => [],
            'products' => [],
            'packages' => [],
        ];
        
        parent::reset(...$properties);
    }

    /**
     * Check if package has items
     */
    public function hasItems(): bool
    {
        return !empty($this->items['services']) 
            || !empty($this->items['products']) 
            || !empty($this->items['packages']);
    }
}

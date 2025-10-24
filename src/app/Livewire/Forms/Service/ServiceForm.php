<?php

namespace App\Livewire\Forms\Service;

use Livewire\Attributes\Validate;
use Livewire\Form;

class ServiceForm extends Form
{
    #[Validate('required|string|max:255')]
    public string $name = '';

    #[Validate('nullable|date_format:H:i')]
    public ?string $service_time = null;

    #[Validate('required|numeric|min:0')]
    public string $price = '';

    #[Validate('required|numeric|min:0')]
    public string $cost_price = '';

    #[Validate('nullable|string|max:1000')]
    public ?string $description = null;

    /**
     * Reset form fields
     */
    public function reset(...$properties): void
    {
        $this->name = '';
        $this->service_time = null;
        $this->price = '';
        $this->cost_price = '';
        $this->description = null;
        
        parent::reset(...$properties);
    }
}

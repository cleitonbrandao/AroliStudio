<?php

namespace App\Http\Requests;

use App\Models\Package;
use App\Models\Product;
use App\Models\Service;
use Illuminate\Foundation\Http\FormRequest;

class StorePackageRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }
    public function messages()
    {
        return [
            'package.name.unique' => 'Pacote já Cadastrado!',
            'items.required' => 'Pacote Deve Conter ao menos um Item!'
        ];
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'package' => ['array'],
            'package.name' => ['unique:App\Models\Package,name', 'required', 'string'],
            'package.price' => ['nullable', 'numeric'],
            'package.description' => ['nullable', 'string'],
            'items' => ['required'],
            'items.services' => ['array'],
            'items.services.service.id' => ['nullable', 'integer', Service::class],
            'items.products' => ['array'],
            'items.products.product.id' => ['nullable', 'integer', Product::class],
            'items.packages' => ['array'],
            'items.packages.package.id' => ['nullable', 'integer', Package::class]

        ];
    }
}

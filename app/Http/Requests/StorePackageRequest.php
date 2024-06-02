<?php

namespace App\Http\Requests;

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
            'package.name.unique' => 'Pacote já Cadastrado!'
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
            'service' => ['array'],
            'service.id' => ['nullable'],
            'product' => ['array'],
            'product.id' => ['nullable']
        ];
    }
}

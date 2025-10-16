<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreServiceRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }
    public function messages(): array
    {
        return [
            'name.unique' => 'Serviço já Cadastrado!'
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
            'name' => ['unique:App\Models\Service,name', 'required', 'string'],
            'service_time' => ['nullable'],
            'price' => ['nullable', 'numeric'],
            'cost_price' => ['nullable', 'numeric'],
            'description' => ['nullable', 'string']
        ];
    }
}

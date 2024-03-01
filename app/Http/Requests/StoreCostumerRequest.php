<?php

namespace App\Http\Requests;

use App\Rules\CpfRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreCostumerRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'person' => ['array'],
            'person.name' => ['required', 'string'],
            'person.last_name' => ['required', 'string'],
            'person.phone' => ['nullable', 'string'],
            'person.photo' => ['nullable', 'string'],
            'costumer' => ['array'],
            'costumer.cpf' => ['unique:App\Models\Costumer,cpf', 'nullable', 'not_regex:/^(.)\1*$/', 'digits:11',  new CpfRule],
            'costumer.birthday' => ['nullable', 'date'],
            'costumer.email' => ['nullable', 'email'],
        ];
    }
}

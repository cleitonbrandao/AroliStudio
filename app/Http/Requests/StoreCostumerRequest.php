<?php

namespace App\Http\Requests;

use App\Entities\Data;
use App\Rules\CpfRule;
use App\Rules\DataRule;
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
    public function messages()
    {
        return [
            'costumer.cpf.unique' => 'CPF já Cadastrado!.',
            'costumer.email.unique' => 'Email já Cadastrado!.'
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
            'person' => ['array'],
            'person.name' => ['required', 'string'],
            'person.last_name' => ['required', 'string'],
            'person.phone' => ['nullable', 'string'],
            'person.photo' => ['nullable', 'string'],
            'costumer' => ['array'],
            'costumer.cpf' => ['unique:App\Models\Costumer,cpf', 'nullable', 'not_regex:/^(.)\1*$/', 'digits:11',  new CpfRule],
            'costumer.birthday' => ['required', 'date_format:d/m/Y'],
            'costumer.email' => ['unique:App\Models\Costumer,email', 'nullable', 'email'],
        ];
    }
}

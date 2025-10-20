<?php

namespace App\Http\Requests;

use App\Rules\CnpjRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreEnterpriseRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'enterprise' => ['array'],
            'enterprise.nome_fantasia' => ['string', 'nullabled'],
            'enterprise.razao_social' => ['string', 'required'],
            'enterprise.cnpj' => ['required', 'unique:App\Models\Enterprise,cnpj', 'not_regex:/^(.)\1*$/', 'digits:14', new CnpjRule],
            'enterprise.inscricao_estatual' => ['string', 'nullable'],
            'enterprise.bussines_email' => ['unique:App\Models\Enterprise,email', 'nullable', 'email']
        ];
    }
}

<?php

namespace App\Http\Requests;

use App\Services\CustomerService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCustomerRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Usuário precisa estar autenticado e ter um team
        return $this->user() && $this->user()->currentTeam;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $teamId = $this->user()?->currentTeam?->id;

        return [
            // Dados da pessoa
            'name' => ['required', 'string', 'max:45'],
            'last_name' => ['nullable', 'string', 'max:60'],
            'phone' => ['nullable', 'string', 'max:20'],
            'photo' => ['nullable', 'string', 'max:255'],

            // Dados do customer
            'cpf' => [
                'nullable',
                'digits:11',
                Rule::unique('costumers', 'cpf')
                    ->where('team_id', $teamId)
                    ->whereNotNull('cpf'),
            ],
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('costumers', 'email')
                    ->where('team_id', $teamId),
            ],
            'birthday' => ['nullable', 'date', 'before:today'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'O nome é obrigatório.',
            'name.max' => 'O nome não pode ter mais de 45 caracteres.',
            
            'last_name.max' => 'O sobrenome não pode ter mais de 60 caracteres.',
            
            'phone.max' => 'O telefone não pode ter mais de 20 caracteres.',
            
            'cpf.digits' => 'O CPF deve conter exatamente 11 dígitos.',
            'cpf.unique' => 'Este CPF já está cadastrado.',
            
            'email.required' => 'O email é obrigatório.',
            'email.email' => 'O email deve ser válido.',
            'email.unique' => 'Este email já está cadastrado.',
            
            'birthday.date' => 'A data de nascimento deve ser uma data válida.',
            'birthday.before' => 'A data de nascimento deve ser anterior a hoje.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'name' => 'nome',
            'last_name' => 'sobrenome',
            'phone' => 'telefone',
            'photo' => 'foto',
            'cpf' => 'CPF',
            'email' => 'e-mail',
            'birthday' => 'data de nascimento',
        ];
    }
}

<?php

namespace App\Livewire\Forms\Customer;

use App\Models\Costumer;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Validate;
use Livewire\Form;

class CustomerForm extends Form
{
    public ?Costumer $customer = null;
    
    // Dados da Pessoa (People)
    #[Validate('required|string|max:45')]
    public string $name = '';
    
    #[Validate('nullable|string|max:60')]
    public ?string $last_name = null;
    
    #[Validate('nullable|string|max:20')]
    public ?string $phone = null;
    
    #[Validate('nullable|string|max:255')]
    public ?string $photo = null;
    
    // Dados do Customer (Costumer)
    public ?string $cpf = null;
    
    #[Validate('required|email|max:255')]
    public string $email = '';
    
    public ?string $birthday = null;
    
    #[Locked] // Previne manipulação via DevTools/Livewire Inspector
    public ?int $team_id = null;

    /**
     * Regras de validação dinâmicas
     */
    public function rules(): array
    {
        $teamId = Auth::user()?->currentTeam?->id;
        $customerId = $this->customer?->id;

        return [
            'name' => ['required', 'string', 'max:45'],
            'last_name' => ['nullable', 'string', 'max:60'],
            'phone' => ['nullable', 'string', 'max:20'],
            'photo' => ['nullable', 'string', 'max:255'],
            
            'cpf' => [
                'nullable',
                'string',
                'regex:/^\d{3}\.\d{3}\.\d{3}-\d{2}$/',
                function ($attribute, $value, $fail) use ($teamId, $customerId) {
                    if (empty($value)) {
                        return;
                    }
                    
                    // Remove formatação para verificar unicidade
                    $cleanCpf = preg_replace('/[^0-9]/', '', $value);
                    
                    // Verifica se tem 11 dígitos
                    if (strlen($cleanCpf) !== 11) {
                        $fail('O CPF deve conter exatamente 11 dígitos.');
                        return;
                    }
                    
                    // Verifica unicidade no banco (usando CPF limpo)
                    $exists = \App\Models\Costumer::where('team_id', $teamId)
                        ->where('id', '!=', $customerId)
                        ->whereNotNull('cpf')
                        ->get()
                        ->contains(function ($customer) use ($cleanCpf) {
                            $dbCpf = preg_replace('/[^0-9]/', '', $customer->cpf);
                            return $dbCpf === $cleanCpf;
                        });
                    
                    if ($exists) {
                        $fail('Este CPF já está cadastrado.');
                    }
                },
            ],
            
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('customers', 'email')
                    ->where('team_id', $teamId)
                    ->ignore($customerId),
            ],
            
            'birthday' => ['nullable', 'date', 'before:today'],
        ];
    }

    /**
     * Mensagens de validação personalizadas
     */
    public function messages(): array
    {
        return [
            'name.required' => 'O nome é obrigatório.',
            'name.max' => 'O nome não pode ter mais de 45 caracteres.',
            
            'last_name.max' => 'O sobrenome não pode ter mais de 60 caracteres.',
            
            'phone.max' => 'O telefone não pode ter mais de 20 caracteres.',
            
            'cpf.regex' => 'O CPF deve estar no formato: 000.000.000-00',
            
            'email.required' => 'O email é obrigatório.',
            'email.email' => 'O email deve ser válido.',
            'email.unique' => 'Este email já está cadastrado.',
            
            'birthday.date' => 'A data de nascimento deve ser uma data válida.',
            'birthday.before' => 'A data de nascimento deve ser anterior a hoje.',
        ];
    }

    /**
     * Atributos customizados para mensagens de erro
     */
    public function validationAttributes(): array
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

    /**
     * Carregar dados de um customer existente
     */
    public function setCustomer(int $customerId): void
    {
        $this->customer = Costumer::with('people')->auth()->findOrFail($customerId);
        
        // Carregar dados da pessoa
        if ($this->customer->people) {
            $this->name = $this->customer->people->name;
            $this->last_name = $this->customer->people->last_name;
            $this->phone = $this->customer->people->phone;
            $this->photo = $this->customer->people->photo;
        }
        
        // Carregar dados do customer
        // O CPF já vem formatado pelo cast CpfMaskaredWithDataBase
        $this->cpf = $this->customer->cpf;
        $this->email = $this->customer->email;
        // Pega o valor original do banco (Y-m-d) sem passar pelo cast
        $this->birthday = $this->customer->getAttributes()['birthday'] ?? null;
        $this->team_id = $this->customer->team_id;
    }

    /**
     * Obter dados formatados para criação/atualização
     */
    public function getData(): array
    {
        return [
            'name' => $this->name,
            'last_name' => $this->last_name,
            'phone' => $this->phone,
            'photo' => $this->photo,
            // CPF já vem formatado do input, o cast CpfMaskaredWithDataBase vai limpar antes de salvar
            'cpf' => $this->cpf,
            'email' => $this->email,
            'birthday' => $this->birthday,
        ];
    }

    /**
     * Resetar formulário
     */
    public function reset(...$properties): void
    {
        $this->customer = null;
        $this->name = '';
        $this->last_name = null;
        $this->phone = null;
        $this->photo = null;
        $this->cpf = null;
        $this->email = '';
        $this->birthday = null;
        $this->team_id = null;
        
        parent::reset(...$properties);
    }

    /**
     * Verificar se está em modo de edição
     */
    public function isEditMode(): bool
    {
        return $this->customer !== null;
    }
}

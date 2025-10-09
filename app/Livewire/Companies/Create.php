<?php

namespace App\Livewire\Companies;

use App\Models\Company;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class Create extends Component
{
    public $name = '';

    protected $rules = [
        'name' => 'required|string|max:255',
    ];

    protected $messages = [
        'name.required' => 'O nome da empresa é obrigatório.',
        'name.max' => 'O nome da empresa não pode ter mais de 255 caracteres.',
    ];

    public function createCompany()
    {
        $this->validate();

        try {
            DB::transaction(function () {
                // Criar empresa usando Jetstream
                $company = Auth::user()->ownedTeams()->create([
                    'name' => $this->name,
                ]);

                // Adicionar usuário como owner (automático no Jetstream)
                $company->users()->attach(Auth::user(), ['role' => 'owner']);

                // Definir como empresa ativa e current team
                session(['active_company_id' => $company->id]);
                Auth::user()->forceFill([
                    'current_team_id' => $company->id,
                ])->save();

                session()->flash('success', 'Empresa criada com sucesso!');
                
                return redirect()->route('companies.index');
            });
        } catch (\Exception $e) {
            session()->flash('error', 'Erro ao criar empresa: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.companies.create');
    }
}

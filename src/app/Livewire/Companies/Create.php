<?php
namespace App\Livewire\Companies;

use App\Actions\Jetstream\CreateTeam;
use App\Enums\Blade;
use App\Enums\Route;
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
                $user = Auth::user();
                $team = app(CreateTeam::class)->create($user, [
                    'name' => $this->name,
                    'personal_team' => false,
                ]);

                // Definir como empresa ativa e current team
                session(['active_company_id' => $team->id]);
                // Recarregar o usuário para garantir instância Eloquent
                $freshUser = \App\Models\User::find($user->id);
                $freshUser->current_team_id = $team->id;
                $freshUser->save();

                session()->flash('success', 'Empresa criada com sucesso!');
                return redirect()->route(Route::WEB_COMPANIES_INDEX);
            });
        } catch (\Exception $e) {
            session()->flash('error', 'Erro ao criar empresa: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view(Blade::LIVEWIRE_COMPANIES_CREATE);
    }
}

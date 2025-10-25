<?php

namespace App\Filament\Team\Pages;

use App\Models\Team;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Pages\Tenancy\RegisterTenant;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Auth;

class RegisterTeam extends RegisterTenant
{
    public static function getLabel(): string
    {
        return __('Register Team');
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label(__('Team Name'))
                    ->required()
                    ->maxLength(255),
                Toggle::make('personal_team')
                    ->label(__('Personal Team'))
                    ->default(false),
            ]);
    }

    protected function handleRegistration(array $data): Team
    {
        $team = Team::create( $data);

        $team->members()->create([
            'user_id' => Auth::id(),
        ]);

        return $team;
    }
}

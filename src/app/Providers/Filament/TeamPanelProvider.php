<?php

namespace App\Providers\Filament;

use App\Filament\Team\Pages\EditTeamProfile;
use App\Filament\Team\Pages\RegisterTeam;
use App\Models\Team;
use Filament\Actions\Action;
use Filament\Auth\MultiFactor\App\AppAuthentication;
use Filament\Auth\MultiFactor\Email\EmailAuthentication;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets\AccountWidget;
use Filament\Widgets\FilamentInfoWidget;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class TeamPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('team')
            ->path('team')
            ->login()
            ->colors([
                'primary' => Color::Amber,
                'secondary' => Color::Emerald,
            ])
            ->discoverResources(in: app_path('Filament/Team/Resources'), for: 'App\Filament\Team\Resources')
            ->discoverPages(in: app_path('Filament/Team/Pages'), for: 'App\Filament\Team\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Team/Widgets'), for: 'App\Filament\Team\Widgets')
            ->widgets([
                AccountWidget::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ])
            ->tenant(Team::class)
            ->tenantRegistration(RegisterTeam::class)
            ->tenantProfile(EditTeamProfile::class)
            ->searchableTenantMenu()
            ->tenantMenuItems([
                'register' => fn(Action $action) => $action->label(__('Register new team')),
                'profile' => fn (Action $action) => $action->label(__('Edit team profile')),
            ])
            ->profile()
            ->multiFactorAuthentication(providers: [
                AppAuthentication::make()
                    ->recoverable()
                    ->regenerableRecoveryCodes(false)
                    ->brandName(config('app.name')),
                EmailAuthentication::make()
                    ->codeExpiryMinutes(5),
            ], isRequired: true);
    }
}

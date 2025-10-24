<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Team Invitations Language Lines (Spanish)
    |--------------------------------------------------------------------------
    |
    | The following language lines are used in team invitation views and
    | controllers throughout the application.
    |
    */

    'You have been invited!' => '¡Has sido invitado!',
    'You received an invitation to join the team' => 'Has recibido una invitación para unirte al equipo',
    'As' => 'Como',
    'This invitation was sent to' => 'Esta invitación fue enviada a',
    'Log in' => 'Iniciar sesión',
    'Register' => 'Registrarse',
    'After logging in or creating your account, you will be automatically added to the team.' => 'Después de iniciar sesión o crear tu cuenta, serás automáticamente agregado al equipo.',
    
    // Controller messages
    'You are now part of the :team team!' => '¡Ahora eres parte del equipo :team!',
    'This invitation was sent to :email, but you are logged in as :current_email.' => 'Esta invitación fue enviada a :email, pero has iniciado sesión como :current_email.',
    'Please log in or create an account to accept the invitation to :team.' => 'Por favor, inicia sesión o crea una cuenta para aceptar la invitación a :team.',
    
    // Roles
    'roles' => [
        'super_admin' => 'Super Administrador',
        'ceo' => 'CEO/Director',
        'franchise_owner' => 'Propietario de Franquicia',
        'regional_manager' => 'Gerente Regional',
        'manager' => 'Gerente',
        'supervisor' => 'Supervisor',
        'employee' => 'Empleado',
        'auditor' => 'Auditor',
        // Fallback para roles genéricos
        'admin' => 'Administrador',
        'editor' => 'Editor',
        'member' => 'Miembro',
    ],
];

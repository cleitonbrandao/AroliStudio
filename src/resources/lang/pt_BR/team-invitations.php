<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Team Invitations Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines are used in team invitation views and
    | controllers throughout the application.
    |
    */

    'You have been invited!' => 'Você foi convidado!',
    'You received an invitation to join the team' => 'Você recebeu um convite para fazer parte do time',
    'As' => 'Como',
    'This invitation was sent to' => 'Este convite foi enviado para',
    'Log in' => 'Fazer Login',
    'Register' => 'Criar Conta',
    'After logging in or creating your account, you will be automatically added to the team.' => 'Após fazer login ou criar sua conta, você será automaticamente adicionado ao time.',
    
    // Controller messages
    'You are now part of the :team team!' => 'Você agora faz parte do time :team!',
    'This invitation was sent to :email, but you are logged in as :current_email.' => 'Este convite foi enviado para :email, mas você está logado como :current_email.',
    'Please log in or create an account to accept the invitation to :team.' => 'Por favor, faça login ou crie uma conta para aceitar o convite para :team.',
    
    // Roles
    'roles' => [
        'super_admin' => 'Super Administrador',
        'ceo' => 'CEO/Diretor',
        'franchise_owner' => 'Franqueado',
        'regional_manager' => 'Gerente Regional',
        'manager' => 'Gerente',
        'supervisor' => 'Supervisor',
        'employee' => 'Funcionário',
        'auditor' => 'Auditor',
        // Fallback para roles genéricas
        'admin' => 'Administrador',
        'editor' => 'Editor',
        'member' => 'Membro',
    ],
];

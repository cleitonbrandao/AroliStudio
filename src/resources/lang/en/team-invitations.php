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

    'You have been invited!' => 'You have been invited!',
    'You received an invitation to join the team' => 'You received an invitation to join the team',
    'As' => 'As',
    'This invitation was sent to' => 'This invitation was sent to',
    'Log in' => 'Log in',
    'Register' => 'Register',
    'After logging in or creating your account, you will be automatically added to the team.' => 'After logging in or creating your account, you will be automatically added to the team.',
    
    // Controller messages
    'You are now part of the :team team!' => 'You are now part of the :team team!',
    'This invitation was sent to :email, but you are logged in as :current_email.' => 'This invitation was sent to :email, but you are logged in as :current_email.',
    'Please log in or create an account to accept the invitation to :team.' => 'Please log in or create an account to accept the invitation to :team.',
    
    // Roles
    'roles' => [
        'super_admin' => 'Super Administrator',
        'ceo' => 'CEO/Director',
        'franchise_owner' => 'Franchise Owner',
        'regional_manager' => 'Regional Manager',
        'manager' => 'Manager',
        'supervisor' => 'Supervisor',
        'employee' => 'Employee',
        'auditor' => 'Auditor',
        // Fallback for generic roles
        'admin' => 'Administrator',
        'editor' => 'Editor',
        'member' => 'Member',
    ],
];

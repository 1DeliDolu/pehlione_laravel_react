<?php

use App\Enums\Role;

return [
    /*
    |--------------------------------------------------------------------------
    | Section Restriction Map
    |--------------------------------------------------------------------------
    |
    | Map documentation section slugs to the roles that may access them.
    | Slugs correspond to the folder names found under the `.docs` directory
    | after Laravel applies `Str::slug` semantics. If a section is not listed
    | here it remains visible to every authenticated user.
    */
    'restricted_sections' => [
        'kunden' => [Role::KUNDEN->value, Role::ADMIN->value],
        'marketing' => [Role::MARKETING->value, Role::ADMIN->value],
        'lager' => [Role::LAGER->value, Role::ADMIN->value],
        'vertrieb' => [Role::VERTRIEB->value, Role::ADMIN->value],
        'admin' => [Role::ADMIN->value],
    ],

    /*
    |--------------------------------------------------------------------------
    | Supervisor Roles
    |--------------------------------------------------------------------------
    |
    | These roles receive access to every restricted section automatically.
    | Keep the values in sync with the Role enum cases.
    */
    'supervisor_roles' => [
        Role::ADMIN->value,
    ],
];


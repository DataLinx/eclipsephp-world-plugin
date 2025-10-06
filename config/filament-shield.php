<?php

use Eclipse\World\Filament\Clusters\World\Resources\CountryResource;
use Eclipse\World\Filament\Clusters\World\Resources\CurrencyResource;
use Eclipse\World\Filament\Clusters\World\Resources\PostResource;
use Eclipse\World\Filament\Clusters\World\Resources\RegionResource;
use Eclipse\World\Filament\Clusters\World\Resources\TariffCodeResource;

return [
    'shield_resource' => [
        'slug' => 'shield/roles',
        'show_model_path' => true,
        'cluster' => null,
        'tabs' => [
            'pages' => true,
            'widgets' => true,
            'resources' => true,
            'custom_permissions' => true,
        ],
    ],

    'tenant_model' => \Eclipse\Core\Models\Site::class,

    'auth_provider_model' => \Eclipse\Core\Models\User::class,

    'super_admin' => [
        'enabled' => true,
        'name' => 'super_admin',
        'define_via_gate' => false,
        'intercept_gate' => 'before',
    ],

    'panel_user' => [
        'enabled' => true,
        'name' => 'panel_user',
    ],

    'permissions' => [
        'separator' => '_',
        'case' => 'lower_snake',
        'generate' => true,
    ],

    'policies' => [
        'path' => app_path('Policies'),
        'merge' => true,
        'generate' => true,
        'methods' => [
            'viewAny', 'view', 'create', 'update', 'restore', 'restoreAny',
            'replicate', 'reorder', 'delete', 'deleteAny', 'forceDelete', 'forceDeleteAny',
        ],
        'single_parameter_methods' => [
            'viewAny', 'create', 'deleteAny', 'forceDeleteAny', 'restoreAny', 'reorder',
        ],
    ],

    'localization' => [
        'enabled' => false,
        'key' => 'filament-shield::filament-shield',
    ],

    'resources' => [
        'subject' => 'model',
        'manage' => [
            CountryResource::class => [
                'viewAny',
                'view',
                'create',
                'update',
                'restore',
                'restoreAny',
                'delete',
                'deleteAny',
                'forceDelete',
                'forceDeleteAny',
            ],
            RegionResource::class => [
                'viewAny',
                'view',
                'create',
                'update',
                'restore',
                'restoreAny',
                'delete',
                'deleteAny',
                'forceDelete',
                'forceDeleteAny',
            ],
            CurrencyResource::class => [
                'viewAny',
                'view',
                'create',
                'update',
                'restore',
                'restoreAny',
                'delete',
                'deleteAny',
                'forceDelete',
                'forceDeleteAny',
            ],
            TariffCodeResource::class => [
                'viewAny',
                'view',
                'create',
                'update',
                'restore',
                'restoreAny',
                'delete',
                'deleteAny',
                'forceDelete',
                'forceDeleteAny',
            ],
            PostResource::class => [
                'viewAny',
                'view',
                'create',
                'update',
                'restore',
                'restoreAny',
                'delete',
                'deleteAny',
                'forceDelete',
                'forceDeleteAny',
            ],
        ],
        'exclude' => [],
    ],

    'pages' => [
        'subject' => 'class',
        'prefix' => 'view',
        'exclude' => [
        ],
    ],

    'widgets' => [
        'subject' => 'class',
        'prefix' => 'view',
        'exclude' => [
        ],
    ],

    'custom_permissions' => [
    ],

    'discovery' => [
        'discover_all_resources' => false,
        'discover_all_widgets' => false,
        'discover_all_pages' => false,
    ],

    'register_role_policy' => true,
];

<?php

return [
    'nav_label' => 'Regions',
    'breadcrumb' => 'Regions',
    'plural' => 'Regions',

    'form' => [
        'name' => [
            'label' => 'Name',
        ],
        'code' => [
            'label' => 'Code',
        ],
        'is_special' => [
            'label' => 'Special Region',
        ],
        'parent' => [
            'label' => 'Parent Region',
        ],
    ],

    'table' => [
        'name' => [
            'label' => 'Name',
        ],
        'code' => [
            'label' => 'Code',
        ],
        'is_special' => [
            'label' => 'Special',
        ],
        'parent' => [
            'label' => 'Parent',
        ],
    ],

    'actions' => [
        'edit' => [
            'label' => 'Edit',
            'heading' => 'Edit Region',
        ],
        'delete' => [
            'label' => 'Delete',
            'heading' => 'Delete Region',
        ],
        'restore' => [
            'label' => 'Restore',
            'heading' => 'Restore Region',
        ],
        'force_delete' => [
            'label' => 'Force Delete',
            'heading' => 'Permanently Delete Region',
            'description' => 'This will permanently delete ":name". This action cannot be undone.',
        ],
    ],
];

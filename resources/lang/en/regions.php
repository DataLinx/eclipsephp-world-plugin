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
            'helper' => 'Optional unique code for the region (e.g., EU, ASEAN)',
        ],
        'parent' => [
            'label' => 'Parent Region',
            'helper' => 'Select a parent region to create a sub-region',
        ],
        'is_special' => [
            'label' => 'Special Region',
            'helper' => 'Special regions are custom regions like EU or EEA that countries can join/leave',
        ],
    ],

    'table' => [
        'name' => [
            'label' => 'Name',
        ],
        'code' => [
            'label' => 'Code',
        ],
        'parent' => [
            'label' => 'Parent Region',
        ],
        'is_special' => [
            'label' => 'Special',
        ],
        'countries_count' => [
            'label' => 'Countries',
        ],
        'children_count' => [
            'label' => 'Sub-regions',
        ],
    ],

    'filters' => [
        'type' => [
            'label' => 'Type',
            'geographical' => 'Geographical',
            'special' => 'Special',
        ],
        'parent' => [
            'label' => 'Parent Region',
        ],
    ],

    'actions' => [
        'create' => [
            'label' => 'New Region',
            'heading' => 'Create Region',
        ],
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
            'heading' => 'Force Delete Region',
            'description' => 'Are you sure you want to permanently delete ":name"? This action cannot be undone.',
        ],
    ],
];

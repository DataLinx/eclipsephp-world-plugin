<?php

return [

    'nav_label' => 'Tariff Codes',
    'breadcrumb' => 'Tariff Codes',
    'plural' => 'Tariff Codes',

    'table' => [
        'year' => [
            'label' => 'Year',
        ],
        'code' => [
            'label' => 'Code',
        ],
        'name' => [
            'label' => 'Name',
        ],
    ],

    'actions' => [
        'create' => [
            'label' => 'New tariff code',
            'heading' => 'New Tariff Code',
        ],
        'edit' => [
            'label' => 'Edit',
            'heading' => 'Edit Tariff Code',
        ],
        'delete' => [
            'label' => 'Delete',
            'heading' => 'Delete Tariff Code',
        ],
        'restore' => [
            'label' => 'Restore',
            'heading' => 'Restore Tariff Code',
        ],
        'force_delete' => [
            'label' => 'Permanent delete',
            'heading' => 'Permanent Deletion of Tariff Code',
            'description' => 'Are you sure you want to delete the tariff code :name? This action cannot be undone.',
        ],
    ],

    'form' => [
        'year' => [
            'label' => 'Year',
        ],
        'code' => [
            'label' => 'Code',
        ],
        'name' => [
            'label' => 'Name',
        ],
        'measure_unit' => [
            'label' => 'Measure unit',
        ],
    ],

    'validation' => [
        'code' => [
            'unique' => 'A tariff code with this code already exists.',
        ],
    ],

    'import' => [
        'action_label' => 'Import Tariff Codes',
        'modal_heading' => 'Import CN tariff codes',
        'locales_label' => 'Locales',
        'job_name' => 'Import Tariff Codes',
    ],

    'notifications' => [
        'queued' => [
            'title' => 'Tariff Codes Import queued',
        ],
        'completed' => [
            'title' => 'Tariff Codes Import completed',
        ],
        'failed' => [
            'title' => 'Tariff Codes Import failed',
        ],
    ],
];

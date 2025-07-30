<?php

return [

    'nav_label' => 'Currencies',
    'breadcrumb' => 'Currencies',
    'plural' => 'Currencies',

    'table' => [
        'id' => [
            'label' => 'Code',
        ],
        'name' => [
            'label' => 'Name',
        ],
        'is_active' => [
            'label' => 'Active',
        ],
    ],

    'actions' => [
        'create' => [
            'label' => 'New currency',
            'heading' => 'New Currency',
        ],
        'edit' => [
            'label' => 'Edit',
            'heading' => 'Edit Currency',
        ],
        'delete' => [
            'label' => 'Delete',
            'heading' => 'Delete Currency',
        ],
        'restore' => [
            'label' => 'Restore',
            'heading' => 'Restore Currency',
        ],
        'force_delete' => [
            'label' => 'Permanent delete',
            'heading' => 'Permanent Deletion of Currency',
            'description' => 'Are you sure you want to delete the currency :name? This action cannot be undone.',
        ],
    ],

    'form' => [
        'id' => [
            'label' => 'Code',
            'helper' => 'Three-letter currency code (ISO 4217)',
        ],
        'name' => [
            'label' => 'Name',
        ],
        'is_active' => [
            'label' => 'Active',
        ],
    ],

    'import' => [
        'action_label' => 'Import Currencies',
        'modal_heading' => 'Import Currencies',
        'success_title' => 'Import Currencies',
        'success_message' => 'The import currencies job has been queued.',
    ],

    'notifications' => [
        'success' => [
            'title' => 'Currencies Import Completed',
            'message' => 'All currencies have been successfully imported and updated.',
        ],
        'failed' => [
            'title' => 'Currencies Import Failed',
            'message' => 'Failed to import currencies data.',
        ],
    ],
];

<?php

return [
    'nav_label' => 'Posts',
    'breadcrumb' => 'Posts',
    'plural' => 'Posts',

    'table' => [
        'country' => [
            'label' => 'Country',
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
            'label' => 'New post',
            'heading' => 'Create Post',
        ],
        'edit' => [
            'label' => 'Edit',
            'heading' => 'Edit Post',
        ],
        'delete' => [
            'label' => 'Delete',
            'heading' => 'Delete Post',
        ],
        'restore' => [
            'label' => 'Restore',
            'heading' => 'Restore Post',
        ],
        'force_delete' => [
            'label' => 'Permanent delete',
            'heading' => 'Permanent Deletion of Post',
            'description' => 'Are you sure you want to delete the post :name? This action cannot be undone.',
        ],
    ],

    'form' => [
        'country_id' => [
            'label' => 'Country',
        ],
        'code' => [
            'label' => 'Code',
        ],
        'name' => [
            'label' => 'Name',
        ],
    ],

    'filter' => [
        'country' => [
            'label' => 'Country',
        ],
    ],

    'validation' => [
        'unique_country_code' => 'A post with this code already exists for the selected country.',
    ],

    'import' => [
        'action_label' => 'Import Posts',
        'modal_heading' => 'Import Posts',
        'country_label' => 'Select Country',
        'country_helper' => 'Choose the country for which you want to import postal data',
        'success_title' => 'Import Posts',
        'success_message' => 'The import posts job has been queued for :country.',
        'countries' => [
            'SI' => 'Slovenia',
            'HR' => 'Croatia',
        ],
    ],
];

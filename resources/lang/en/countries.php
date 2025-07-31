<?php

return [

    'nav_label' => 'Countries',
    'breadcrumb' => 'Countries',
    'plural' => 'Countries',

    'table' => [
        'id' => [
            'label' => 'ID',
        ],
        'name' => [
            'label' => 'Name',
        ],
        'flag' => [
            'label' => 'Flag',
        ],
        'alpha3_id' => [
            'label' => 'Alpha-3 Code',
        ],
        'num_code' => [
            'label' => 'Num. Code',
        ],
        'region' => [
            'label' => 'Region',
        ],
        'special_regions' => [
            'label' => 'Special Regions',
        ],
    ],

    'actions' => [
        'create' => [
            'label' => 'New country',
            'heading' => 'New Country',
        ],
        'edit' => [
            'label' => 'Edit',
            'heading' => 'Edit Country',
        ],
        'delete' => [
            'label' => 'Delete',
            'heading' => 'Delete Country',
        ],
        'restore' => [
            'label' => 'Restore',
            'heading' => 'Restore Country',
        ],
        'force_delete' => [
            'label' => 'Permanent delete',
            'heading' => 'Permanent Deletion of Country',
            'description' => 'Are you sure you want to delete the country :name? This action cannot be undone.',
        ],
    ],

    'form' => [
        'id' => [
            'label' => 'ID',
            'helper' => 'Two-letter code (ISO-3166 Alpha-2)',
        ],
        'name' => [
            'label' => 'Name',
        ],
        'flag' => [
            'label' => 'Flag',
        ],
        'alpha3_id' => [
            'label' => 'Alpha-3 code',
            'helper' => 'Three-letter code (ISO-3166 Alpha-3)',
        ],
        'num_code' => [
            'label' => 'Numeric code',
            'helper' => 'Numeric code (ISO-3166)',
        ],
        'region' => [
            'label' => 'Geographical Region',
            'helper' => 'The geographical region this country belongs to',
        ],
        'special_regions' => [
            'label' => 'Special Regions',
            'helper' => 'Special regions this country belongs to (e.g., European Union)',
            'add_button' => 'Add Special Region',
            'region_label' => 'Region',
            'start_date_label' => 'Start Date',
            'end_date_label' => 'End Date',
        ],
    ],

    'import' => [
        'action_label' => 'Import Countries',
        'modal_heading' => 'Import Countries',
        'success_title' => 'Import Countries',
        'success_message' => 'The import countries job has been queued.',
    ],

    'notifications' => [
        'success' => [
            'title' => 'Countries Import Completed',
            'message' => 'All countries have been successfully imported and updated.',
        ],
        'failed' => [
            'title' => 'Countries Import Failed',
            'message' => 'Failed to import countries data.',
        ],
    ],

    'filters' => [
        'geographical_region' => [
            'label' => 'Geographical Region',
        ],
        'special_region' => [
            'label' => 'Special Region',
        ],
    ],

    'validation' => [
        'duplicate_special_region_membership' => 'This country is already a member of :region.',
        'unknown_region' => 'this region',
    ],
];

<?php

return [

    'nav_label' => 'Carinske oznake',
    'breadcrumb' => 'Carinske oznake',
    'plural' => 'Carinske oznake',

    'table' => [
        'year' => [
            'label' => 'Leto',
        ],
        'code' => [
            'label' => 'Koda',
        ],
        'name' => [
            'label' => 'Naziv',
        ],
    ],

    'actions' => [
        'create' => [
            'label' => 'Nova carinska oznaka',
            'heading' => 'Nova carinska oznaka',
        ],
        'edit' => [
            'label' => 'Uredi',
            'heading' => 'Uredi carinsko oznako',
        ],
        'delete' => [
            'label' => 'Izbriši',
            'heading' => 'Izbriši carinsko oznako',
        ],
        'restore' => [
            'label' => 'Obnovi',
            'heading' => 'Obnovi carinsko oznako',
        ],
        'force_delete' => [
            'label' => 'Trajni izbris',
            'heading' => 'Trajni izbris carinske oznake',
            'description' => 'Ali ste prepričani, da želite izbrisati carinsko oznako :name? Tega dejanja ni mogoče razveljaviti.',
        ],
    ],

    'form' => [
        'year' => [
            'label' => 'Leto',
        ],
        'code' => [
            'label' => 'Koda',
        ],
        'name' => [
            'label' => 'Naziv',
        ],
        'measure_unit' => [
            'label' => 'Merska enota',
        ],
    ],

    'validation' => [
        'code' => [
            'unique' => 'Carinska oznaka s to kodo že obstaja.',
        ],
    ],

    'import' => [
        'action_label' => 'Uvozi carinske oznake',
        'modal_heading' => 'Uvoz CN carinskih oznak',
        'locales_label' => 'Jeziki',
        'job_name' => 'Uvoz carinskih oznak',
    ],

    'notifications' => [
        'queued' => [
            'title' => 'Uvoz carinskih oznak v čakalni vrsti',
        ],
        'completed' => [
            'title' => 'Uvoz carinskih oznak zaključen',
        ],
        'failed' => [
            'title' => 'Uvoz carinskih oznak ni uspel',
        ],
    ],
];

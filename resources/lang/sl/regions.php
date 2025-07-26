<?php

return [
    'nav_label' => 'Regije',
    'breadcrumb' => 'Regije',
    'plural' => 'Regije',

    'form' => [
        'name' => [
            'label' => 'Ime',
        ],
        'code' => [
            'label' => 'Koda',
            'helper' => 'Neobvezna edinstvena koda za regijo (npr. EU, ASEAN)',
        ],
        'parent' => [
            'label' => 'Nadrejena regija',
            'helper' => 'Izberite nadrejeno regijo za ustvarjanje podregije',
        ],
        'is_special' => [
            'label' => 'Posebna regija',
            'helper' => 'Posebne regije so prilagojene regije kot EU ali EEA, katerim se lahko države pridružijo/zapustijo',
        ],
    ],

    'table' => [
        'name' => [
            'label' => 'Ime',
        ],
        'code' => [
            'label' => 'Koda',
        ],
        'parent' => [
            'label' => 'Nadrejena regija',
        ],
        'is_special' => [
            'label' => 'Posebna',
        ],
        'countries_count' => [
            'label' => 'Države',
        ],
        'children_count' => [
            'label' => 'Podregije',
        ],
    ],

    'filters' => [
        'type' => [
            'label' => 'Tip',
            'geographical' => 'Geografska',
            'special' => 'Posebna',
        ],
        'parent' => [
            'label' => 'Nadrejena regija',
        ],
    ],

    'actions' => [
        'create' => [
            'label' => 'Nova regija',
            'heading' => 'Ustvari regijo',
        ],
        'edit' => [
            'label' => 'Uredi',
            'heading' => 'Uredi regijo',
        ],
        'delete' => [
            'label' => 'Izbriši',
            'heading' => 'Izbriši regijo',
        ],
        'restore' => [
            'label' => 'Obnovi',
            'heading' => 'Obnovi regijo',
        ],
        'force_delete' => [
            'label' => 'Trajno izbriši',
            'heading' => 'Trajno izbriši regijo',
            'description' => 'Ali ste prepričani, da želite trajno izbrisati ":name"? Tega dejanja ni mogoče razveljaviti.',
        ],
    ],
];

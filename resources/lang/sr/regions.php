<?php

return [
    'nav_label' => 'Regije',
    'breadcrumb' => 'Regije',
    'plural' => 'Regije',

    'form' => [
        'name' => [
            'label' => 'Naziv',
        ],
        'code' => [
            'label' => 'Kod',
            'helper' => 'Opcioni jedinstveni kod za regiju (npr. EU, ASEAN)',
        ],
        'parent' => [
            'label' => 'Nadređena regija',
            'helper' => 'Izaberite nadređenu regiju za stvaranje podregije',
        ],
        'is_special' => [
            'label' => 'Posebna regija',
            'helper' => 'Posebne regije su prilagođene regije poput EU ili EEA kojima se zemlje mogu pridružiti/napustiti',
        ],
    ],

    'table' => [
        'name' => [
            'label' => 'Naziv',
        ],
        'code' => [
            'label' => 'Kod',
        ],
        'parent' => [
            'label' => 'Nadređena regija',
        ],
        'is_special' => [
            'label' => 'Posebna',
        ],
        'countries_count' => [
            'label' => 'Zemlje',
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
            'label' => 'Nadređena regija',
        ],
    ],

    'actions' => [
        'create' => [
            'label' => 'Nova regija',
            'heading' => 'Stvori regiju',
        ],
        'edit' => [
            'label' => 'Uredi',
            'heading' => 'Uredi regiju',
        ],
        'delete' => [
            'label' => 'Obriši',
            'heading' => 'Obriši regiju',
        ],
        'restore' => [
            'label' => 'Vrati',
            'heading' => 'Vrati regiju',
        ],
        'force_delete' => [
            'label' => 'Trajno obriši',
            'heading' => 'Trajno obriši regiju',
            'description' => 'Jeste li sigurni da želite trajno obrisati ":name"? Ova radnja se ne može poništiti.',
        ],
    ],
];

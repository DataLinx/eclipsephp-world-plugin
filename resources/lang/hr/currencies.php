<?php

return [

    'nav_label' => 'Valute',
    'breadcrumb' => 'Valute',
    'plural' => 'Valute',

    'table' => [
        'id' => [
            'label' => 'Kod',
        ],
        'name' => [
            'label' => 'Naziv',
        ],
        'is_active' => [
            'label' => 'Aktivna',
        ],
    ],

    'actions' => [
        'create' => [
            'label' => 'Nova valuta',
            'heading' => 'Nova valuta',
        ],
        'edit' => [
            'label' => 'Uredi',
            'heading' => 'Uredi valutu',
        ],
        'delete' => [
            'label' => 'Obriši',
            'heading' => 'Obriši valutu',
        ],
        'restore' => [
            'label' => 'Vrati',
            'heading' => 'Vrati valutu',
        ],
        'force_delete' => [
            'label' => 'Trajno obriši',
            'heading' => 'Trajno brisanje valute',
            'description' => 'Jeste li sigurni da želite obrisati valutu :name? Ova radnja se ne može poništiti.',
        ],
    ],

    'form' => [
        'id' => [
            'label' => 'Kod',
            'helper' => 'Troslovni kod valute (ISO 4217)',
        ],
        'name' => [
            'label' => 'Naziv',
        ],
        'is_active' => [
            'label' => 'Aktivna',
        ],
    ],

    'import' => [
        'action_label' => 'Uvezi valute',
        'modal_heading' => 'Uvezi valute',
        'success_title' => 'Uvezi valute',
        'success_message' => 'Posao uvoza valuta je dodan u red za izvršavanje.',
    ],

    'notifications' => [
        'success' => [
            'title' => 'Uvoz valuta je završen',
            'message' => 'Sve valute su uspješno uvezene i ažurirane.',
        ],
        'failed' => [
            'title' => 'Uvoz valuta nije uspješan',
            'message' => 'Uvoz podataka o valutama nije uspješan.',
        ],
    ],
];

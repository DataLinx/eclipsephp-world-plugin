<?php

return [

    'nav_label' => 'Države',
    'breadcrumb' => 'Države',
    'plural' => 'Države',

    'table' => [
        'id' => [
            'label' => 'ID',
        ],
        'name' => [
            'label' => 'Ime',
        ],
        'flag' => [
            'label' => 'Zastava',
        ],
        'alpha3_id' => [
            'label' => 'A3 šifra',
        ],
        'num_code' => [
            'label' => 'Brojčana šifra',
        ],
        'region' => [
            'label' => 'Regija',
        ],
        'special_regions' => [
            'label' => 'Posebne regije',
        ],
    ],

    'actions' => [
        'create' => [
            'label' => 'Nova država',
            'heading' => 'Nova država',
        ],
        'edit' => [
            'label' => 'Uredi',
            'heading' => 'Uređivanje države',
        ],
        'delete' => [
            'label' => 'Izbriši',
            'heading' => 'Brisanje države',
        ],
        'restore' => [
            'label' => 'Obnovi',
            'heading' => 'Obnova države',
        ],
        'force_delete' => [
            'label' => 'Trajno izbriši',
            'heading' => 'Trajno brisanje države',
            'description' => 'Jeste li sigurni da želite izbrisati državu :name? Zapis više neće biti moguće obnoviti.',
        ],
    ],

    'form' => [
        'id' => [
            'label' => 'ID',
            'helper' => 'Dvoslovna oznaka (ISO-3166 Alpha-2)',
        ],
        'name' => [
            'label' => 'Ime',
        ],
        'flag' => [
            'label' => 'Zastava',
        ],
        'alpha3_id' => [
            'label' => 'A3 šifra',
            'helper' => 'Troslovna oznaka (ISO-3166 Alpha-3)',
        ],
        'num_code' => [
            'label' => 'Brojčana šifra',
            'helper' => 'Numerička oznaka (ISO-3166)',
        ],
        'region' => [
            'label' => 'Geografska regija',
            'helper' => 'Geografska regija kojoj ova zemlja pripada',
        ],
        'special_regions' => [
            'label' => 'Posebne regije',
            'helper' => 'Posebne regije kojima ova zemlja pripada (npr. Europska unija)',
        ],
    ],

    'import' => [
        'action_label' => 'Uvezi države',
        'modal_heading' => 'Uvoz država',
        'success_title' => 'Uvoz država',
        'success_message' => 'Zadatak za uvoz država je dodan u red čekanja.',
    ],

    'notifications' => [
        'success' => [
            'title' => 'Uvoz država završen',
            'message' => 'Sve države su uspješno uvežene i ažurirane.',
        ],
        'failed' => [
            'title' => 'Uvoz država neuspješan',
            'message' => 'Neuspješan uvoz podataka država.',
        ],
    ],

    'filters' => [
        'region' => [
            'label' => 'Regija',
        ],
    ],
];

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
            'label' => 'Num. šifra',
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
            'heading' => 'Urejanje države',
        ],
        'delete' => [
            'label' => 'Izbriši',
            'heading' => 'Brisanje države',
        ],
        'restore' => [
            'label' => 'Obnovi',
            'heading' => 'Obnovitev države',
        ],
        'force_delete' => [
            'label' => 'Trajno izbriši',
            'heading' => 'Trajen izbris države',
            'description' => 'Ste prepričani, da želite izbrisati državo :name? Zapisa ne bo možno več obnoviti.',
        ],
    ],

    'form' => [
        'id' => [
            'label' => 'ID',
            'helper' => 'Dvočrkovna šifra (ISO-3166 Alpha-2)',
        ],
        'name' => [
            'label' => 'Ime',
        ],
        'flag' => [
            'label' => 'Zastava',
        ],
        'alpha3_id' => [
            'label' => 'A3 šifra',
            'helper' => 'Tričrkovna šifra (ISO-3166 Alpha-3)',
        ],
        'num_code' => [
            'label' => 'Num. šifra',
            'helper' => 'Numerična šifra (ISO-3166)',
        ],
        'region' => [
            'label' => 'Geografska regija',
            'helper' => 'Geografska regija, ki ji ta država pripada',
        ],
        'special_regions' => [
            'label' => 'Posebne regije',
            'helper' => 'Posebne regije, ki jim ta država pripada (npr. Evropska unija)',
            'add_button' => 'Dodaj posebno regijo',
            'region_label' => 'Regija',
            'start_date_label' => 'Datum začetka',
            'end_date_label' => 'Datum konca',
        ],
    ],

    'import' => [
        'action_label' => 'Uvozi države',
        'modal_heading' => 'Uvoz držav',
        'job_name' => 'Uvoz držav',
    ],

    'notifications' => [
        'queued' => [
            'title' => 'Uvoz držav postavljen v vrsto',
        ],
        'completed' => [
            'title' => 'Uvoz držav uspešno končan',
        ],
        'failed' => [
            'title' => 'Uvoz držav neuspešen',
        ],
    ],

    'filters' => [
        'geographical_region' => [
            'label' => 'Geografska regija',
        ],
        'special_region' => [
            'label' => 'Posebna regija',
        ],
    ],

    'validation' => [
        'duplicate_special_region_membership' => 'Ta država je že članica regije :region.',
        'unknown_region' => 'te regije',
    ],
];

<?php

return [
    'nav_label' => 'Pošte',
    'breadcrumb' => 'Pošte',
    'plural' => 'Pošte',

    'table' => [
        'country' => [
            'label' => 'Država',
        ],
        'code' => [
            'label' => 'Šifra',
        ],
        'name' => [
            'label' => 'Ime',
        ],
    ],

    'actions' => [
        'create' => [
            'label' => 'Nova pošta',
            'heading' => 'Kreiranje pošte',
        ],
        'edit' => [
            'label' => 'Uredi',
            'heading' => 'Uređivanje pošte',
        ],
        'delete' => [
            'label' => 'Izbriši',
            'heading' => 'Brisanje pošte',
        ],
        'restore' => [
            'label' => 'Obnovi',
            'heading' => 'Obnavljanje pošte',
        ],
        'force_delete' => [
            'label' => 'Trajno izbriši',
            'heading' => 'Trajno brisanje pošte',
            'description' => 'Da li ste sigurni da želite izbrisati poštu :name? Zapis više neće biti moguće obnoviti.',
        ],
    ],

    'form' => [
        'country_id' => [
            'label' => 'Država',
        ],
        'code' => [
            'label' => 'Šifra',
        ],
        'name' => [
            'label' => 'Ime',
        ],
    ],

    'filter' => [
        'country' => [
            'label' => 'Država',
        ],
    ],

    'validation' => [
        'unique_country_code' => 'Pošta sa ovom šifrom već postoji za odabranu državu.',
    ],

    'import' => [
        'action_label' => 'Uvezi pošte',
        'modal_heading' => 'Uvoz pošta',
        'country_label' => 'Odaberi državu',
        'country_helper' => 'Odaberi državu za koju želiš da uvoziš poštanske podatke',
        'success_title' => 'Uvoz pošta',
        'success_message' => 'Zadatak za uvoz pošta je dodat u red čekanja za :country.',
        'countries' => [
            'SI' => 'Slovenija',
            'HR' => 'Hrvatska',
        ],
    ],
];
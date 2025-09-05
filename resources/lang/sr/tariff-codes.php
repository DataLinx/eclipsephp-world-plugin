<?php

return [

    'nav_label' => 'Carinske šifre',
    'breadcrumb' => 'Carinske šifre',
    'plural' => 'Carinske šifre',

    'table' => [
        'year' => [
            'label' => 'Godina',
        ],
        'code' => [
            'label' => 'Šifra',
        ],
        'name' => [
            'label' => 'Naziv',
        ],
    ],

    'actions' => [
        'create' => [
            'label' => 'Nova carinska šifra',
            'heading' => 'Nova carinska šifra',
        ],
        'edit' => [
            'label' => 'Uredi',
            'heading' => 'Uredi carinsku šifru',
        ],
        'delete' => [
            'label' => 'Obriši',
            'heading' => 'Obriši carinsku šifru',
        ],
        'restore' => [
            'label' => 'Vrati',
            'heading' => 'Vrati carinsku šifru',
        ],
        'force_delete' => [
            'label' => 'Trajno brisanje',
            'heading' => 'Trajno brisanje carinske šifre',
            'description' => 'Da li ste sigurni da želite da obrišete carinsku šifru :name? Ova radnja je nepovratna.',
        ],
    ],

    'form' => [
        'year' => [
            'label' => 'Godina',
        ],
        'code' => [
            'label' => 'Šifra',
        ],
        'name' => [
            'label' => 'Naziv',
        ],
        'measure_unit' => [
            'label' => 'Merna jedinica',
        ],
    ],

    'validation' => [
        'code' => [
            'unique' => 'Carinska šifra s ovom šifrom već postoji.',
        ],
    ],

    'import' => [
        'action_label' => 'Uvoz carinskih šifara',
        'modal_heading' => 'Uvoz CN carinskih šifara',
        'locales_label' => 'Jezici',
        'job_name' => 'Uvoz carinskih šifara',
    ],

    'notifications' => [
        'queued' => [
            'title' => 'Uvoz carinskih šifara u redu',
        ],
        'completed' => [
            'title' => 'Uvoz carinskih šifara završen',
        ],
        'failed' => [
            'title' => 'Uvoz carinskih šifara nije uspeo',
        ],
    ],
];

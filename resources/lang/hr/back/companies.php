<?php

return [
    // Naslovi stranica
    'title'         => 'Tvrtke',
    'title_create'  => 'Dodaj Tvrtku',
    'title_edit'    => 'Uredi Tvrtku',

    // Manje oznake
    'group_label'   => 'Grupa',
    'empty'         => 'Nema tvrtki.',

    // Kartice (grupe kategorija)
    'tabs' => [
        'companies' => 'Tvrtke',
        'blog'     => 'Blog',
        'pages'    => 'Stranice',
        'footer'   => 'Podnožje',
    ],

    // Zaglavlja tablice
    'table' => [
        'id'      => 'ID',
        'name'    => 'Naziv',
        'group'   => 'Grupa',
        'parent'  => 'Nadkategorija',
        'sort'    => 'Redoslijed',
        'status'  => 'Status',
        'updated' => 'Ažurirano',
        'actions' => 'Akcije',
    ],

    // Forma + savjeti
    'form' => [
        'group'          => 'Grupa',
        'parent'         => 'Nadkategorija',
        'parent_hint'    => 'Ostavite prazno za kategoriju najviše razine.',
        'title'          => 'Naslov',
        'slug'           => 'Slug',
        'auto_slug_hint' => 'Ako ostavite prazno, generirat će se iz naslova.',
        'description'    => 'Opis',
        'image'          => 'Slika',
        'icon'           => 'Ikona',
        'banner'         => 'Baner',
        'sort_order'     => 'Redoslijed',
        'is_active'      => 'Aktivna',
    ],

    // Flash poruke
    'flash' => [
        'created' => 'Tvrtka je dodana.',
        'updated' => 'Tvrtka je ažurirana.',
        'deleted' => 'Tvrtka je obrisana.',
    ],

    // Dijalozi
    'confirm_delete' => 'Obrisati ovu tvrtku? Ova radnja je nepovratna.',
];

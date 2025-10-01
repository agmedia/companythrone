<?php

return [
    // ===== Naslovi =====
    'title'          => 'Korisnici',
    'title_create'   => 'Kreiraj korisnika',
    'title_edit'     => 'Uredi korisnika',
    'role_label'     => 'Uloga',
    'confirm_delete' => 'Obrisati ovog korisnika? Ova radnja je nepovratna.',

    // Kompatibilno s novim bladeovima
    'page_titles' => [
        'create'   => 'Kreiraj korisnika',
        'edit'     => 'Uredi korisnika',
        'password' => 'Promjena lozinke',
    ],

    'sections' => [
        'basic_info'  => 'Osnovni podaci',
        'password'    => 'Lozinka',
        'role_status' => 'Uloga i status',
        'avatar'      => 'Avatar',
    ],

    // Postojeća grupa 'form'
    'form' => [
        'role'            => 'Uloga',
        'status'          => 'Status',
        'fname'           => 'Ime',
        'lname'           => 'Prezime',
        'email'           => 'Email',
        'password'        => 'Lozinka',
        'password_hint'   => 'Ostavite prazno za zadržavanje postojeće lozinke.',
        'address'         => 'Adresa',
        'zip'             => 'Poštanski broj',
        'city'            => 'Grad',
        'state'           => 'Županija',
        'phone'           => 'Telefon',
        'bio'             => 'Bio',
        'avatar'          => 'Avatar',
        'remove_avatar'   => 'Ukloni avatar',
    ],

    // Nova polja koja koriste ažurirani bladeovi
    'fields' => [
        'role'                  => 'Uloga',
        'status'                => 'Status',
        'fname'                 => 'Ime',
        'lname'                 => 'Prezime',
        'email'                 => 'Email',
        'password'              => 'Nova lozinka',
        'password_confirmation' => 'Potvrda lozinke',
        'current_password'      => 'Trenutna lozinka',
        'address'               => 'Adresa',
        'zip'                   => 'Poštanski broj',
        'city'                  => 'Grad',
        'state'                 => 'Županija',
        'phone'                 => 'Telefon',
        'bio'                   => 'Bio',
        'avatar'                => 'Avatar',
        'social'                => 'Društvene mreže',
    ],

    'hints' => [
        'leave_blank_keep' => 'ostavite prazno za zadržavanje postojeće',
        'avatar_limit'     => 'Maks. 4MB. JPG, PNG, WEBP.',
    ],

    // Kartice (tabovi)
    'tabs' => [
        'master'   => 'Master',
        'admin'    => 'Admin',
        'manager'  => 'Menadžer',
        'editor'   => 'Urednik',
        'customer' => 'Kupac',
        'company_owner' => 'Vlasnik tvrtke'
    ],

    // Tablica
    'table' => [
        'user'    => 'Korisnik',
        'email'   => 'Email',
        'phone'   => 'Telefon',
        'city'    => 'Grad',
        'status'  => 'Status',
        'updated' => 'Ažurirano',
        'actions' => 'Radnje',
    ],

    // Poruke
    'flash' => [
        'created'          => 'Korisnik je kreiran.',
        'updated'          => 'Korisnik je ažuriran.',
        'deleted'          => 'Korisnik je obrisan.',
        'password_updated' => 'Lozinka je ažurirana.',
    ],
];

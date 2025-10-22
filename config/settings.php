<?php

return [

    /**
     *
     */
    'payments' => [
        // Novi status narudžbe
        'new_status'  => 1,
        'paid_status' => [3],
        //
        'providers'   => [
            'bank'   => [
                'name'    => 'Bank Transfer',
                'enabled' => true,
                'driver'  => \App\Payments\Providers\Bank\Driver::class,
            ],
            'corvus' => [
                'name'    => 'Corvus',
                'enabled' => true,
                'driver'  => \App\Payments\Providers\Corvus\Driver::class,
            ],
            // ...
        ],

        // 💶 Planovi pretplate
        'plans'       => [
            'default' => [
                'label'    => 'Godišnja pretplata',
                'price'    => 20.00,   // neto cijena (bez PDV-a)
                'currency' => 'EUR',
                'period'   => 'yearly', // monthly | yearly
                'tax_code' => 'tax',    // kod u settings tablici (gdje je spremljen PDV %)
            ],

            // primjer dodatnog plana:
            // 'pro' => [
            //     'label'    => 'Pro Plan',
            //     'price'    => 50.00,
            //     'currency' => 'EUR',
            //     'period'   => 'monthly',
            //     'tax_code' => 'tax',
            // ],
        ],
    ],

    'group_slugs' => [
        'tvrtke' => ['hr' => 'tvrtke', 'en' => 'companies'],
        'blog'   => ['hr' => 'blog', 'en' => 'blog'],
        'pages'  => ['hr' => 'info-stranica', 'en' => 'info-page'],
        'footer' => ['hr' => 'footer', 'en' => 'footer'],
    ],

    // Tabovi / grupe u adminu
    'groups'      => [
        'site'    => ['label' => 'Stranica', 'icon' => 'ti ti-world', 'i18n' => false],
        'ui'      => ['label' => 'UI', 'icon' => 'ti ti-layout', 'i18n' => false],
        'company' => ['label' => 'Basic Info', 'icon' => 'ti ti-building', 'i18n' => false],
    ],

    /**
     *
     * // Anywhere (npr. u paginacijama)
     * $perPageAdmin = app(\App\Services\Settings\SettingsManager::class)->get('ui', 'admin_pagination', 20);
     *
     * // I18n string:
     * $title = app(\App\Services\Settings\SettingsManager::class)->get('site', 'title', ['hr'=>'','en'=>''])['hr'] ?? '';
     *
     */
    // Polja po grupi (tip: text|email|number|boolean|i18n_text|i18n_textarea|textarea|decimal)
    'fields'      => [
        'site' => [
            'title'         => ['type' => 'text', 'label' => 'Naslov stranice', 'default' => '', 'col' => 6],
            'slogan'        => ['type' => 'text', 'label' => 'Slogan stranice', 'default' => '', 'col' => 12],
            'contact_email' => ['type' => 'email', 'label' => 'Kontakt email', 'default' => '', 'col' => 4],
            'contact_phone' => ['type' => 'text', 'label' => 'Kontakt telefon', 'default' => '', 'col' => 4],
            //
            'meta_title' => ['type' => 'text', 'label' => 'Meta naslov', 'default' => '', 'col' => 6],
            'meta_description' => ['type' => 'textarea', 'label' => 'Meta opis stranice', 'default' => '', 'col' => 12],
            'meta_keywords' => ['type' => 'text', 'label' => 'Meta ključne riječi', 'default' => '', 'col' => 12],
        ],

        'ui' => [
            'admin_pagination' => ['type' => 'number', 'label' => 'Admin paginacija', 'default' => 20, 'min' => 5, 'max' => 200, 'step' => 1, 'col' => 3],
            'front_pagination' => ['type' => 'number', 'label' => 'Front paginacija', 'default' => 12, 'min' => 6, 'max' => 60, 'step' => 1, 'col' => 3]
        ],

        'company' => [
            'auth_clicks_required'    => ['type' => 'number', 'label' => 'Potreban broj dnevnih klikova', 'default' => 25, 'min' => 0, 'max' => 100, 'step' => 1, 'col' => 3],
            'auth_referrals_required' => ['type' => 'number', 'label' => 'Potreban broj referenci za aktivaciju linka', 'default' => 5, 'min' => 1, 'max' => 30, 'step' => 1, 'col' => 3],
            'link_active_default'     => ['type' => 'boolean', 'label' => 'Nova kompanija aktivna po defaultu', 'default' => false],
            'send_admin_emails'       => ['type' => 'boolean', 'label' => 'Šalji email-ove administratoru?', 'default' => true],
            'main_price_year'         => ['type' => 'decimal', 'label' => 'Godišnja cijena upisa tvrtke', 'default' => '', 'col' => 3],
        ],

    ],

];

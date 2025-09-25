<?php

return [

    /**
     *
     */
    'payments' => [
        'providers' => [
            'bank' => [
                'name'    => 'Bank Transfer',
                'enabled' => true,
                'driver'  => \App\Payments\Providers\Bank\Driver::class,
            ],
            'wspay' => [
                'name'    => 'WSPay',
                'enabled' => true,
                'driver'  => \App\Payments\Providers\Wspay\Driver::class,
            ],
            // add more ...
        ],
    ],

    // Tabovi / grupe u adminu
    'groups' => [
        'site'     => ['label' => 'Site',     'icon' => 'ti ti-world',  'i18n' => true],
        'ui'       => ['label' => 'UI',       'icon' => 'ti ti-layout', 'i18n' => false],
        'company'  => ['label' => 'Company',  'icon' => 'ti ti-building', 'i18n' => false],
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
    'fields' => [
        'site' => [
            'title'         => ['type' => 'i18n_text',     'label' => 'Site title',  'default' => ['hr'=>'','en'=>'']],
            'slogan'        => ['type' => 'i18n_text',     'label' => 'Site slogan', 'default' => ['hr'=>'','en'=>'']],
            'contact_email' => ['type' => 'email',         'label' => 'Contact email', 'default' => ''],
            'contact_phone' => ['type' => 'text',          'label' => 'Contact phone', 'default' => ''],
            'about'         => ['type' => 'i18n_textarea', 'label' => 'About (footer)', 'default' => ['hr'=>'','en'=>'']],
        ],

        'ui' => [
            'admin_pagination' => ['type' => 'number',  'label' => 'Admin pagination size', 'default' => 20, 'min' => 5, 'max' => 200, 'step' => 1],
            'front_pagination' => ['type' => 'number',  'label' => 'Front pagination size', 'default' => 12, 'min' => 6, 'max' => 60, 'step' => 1],
            'show_beta_badge'  => ['type' => 'boolean', 'label' => 'Show beta badge',      'default' => false],
        ],

        'company' => [
            'auth_clicks_required'   => ['type' => 'number',  'label' => 'Daily clicks required for auth', 'default' => 5, 'min'=>0, 'max'=>100, 'step'=>1],
            'auth_click_window_days' => ['type' => 'number',  'label' => 'Auth click window (days)',        'default' => 1, 'min'=>1, 'max'=>30,  'step'=>1],
            'link_active_default'    => ['type' => 'boolean', 'label' => 'New company link active by default', 'default' => false],
        ],

    ],

];

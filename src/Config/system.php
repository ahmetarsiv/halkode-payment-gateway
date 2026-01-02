<?php

return [
    [
        'key'    => 'sales.payment_methods.halkode',
        'info'   => 'halkode::app.halkode.info',
        'name'   => 'halkode::app.halkode.name',
        'sort'   => 1,
        'fields' => [
            [
                'name'          => 'title',
                'title'         => 'halkode::app.halkode.system.title',
                'type'          => 'text',
                'validation'    => 'required',
                'channel_based' => false,
                'locale_based'  => true,
            ], [
                'name'          => 'description',
                'title'         => 'halkode::app.halkode.system.description',
                'type'          => 'textarea',
                'channel_based' => false,
                'locale_based'  => true,
            ], [
                'name'          => 'image',
                'title'         => 'halkode::app.halkode.system.image',
                'info'          => 'admin::app.configuration.index.sales.payment-methods.logo-information',
                'type'          => 'file',
                'channel_based' => false,
                'locale_based'  => true,
            ], [
                'name'          => 'active',
                'title'         => 'halkode::app.halkode.system.status',
                'type'          => 'boolean',
                'validation'    => 'required',
                'channel_based' => false,
                'locale_based'  => true,
            ],
        ],
    ],
];

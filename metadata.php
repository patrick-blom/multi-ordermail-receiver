<?php
/**
 * Metadata version
 */
$sMetadataVersion = '2.1';

/**
 * Module information
 */
$aModule = [
    'id' => 'multiordermailreceiver',
    'title' => [
        'de' => 'PaBlo - Mehrere Empf&auml;nger f&uuml;r die Admin-Bestell-E-Mail',
        'en' => 'PaBlo - multiple receivers for the admin order email',
    ],
    'description' => [
        'de' => 'Das Modul erweitert die Anzahl der E-Mailempf&auml;nger f&uuml; Admin-Bestell-E-Mail',
        'en' => 'This module extends the amount of the admin order mail receivers',
    ],
    'version' => '1.0',
    'author' => 'Patrick Blom',
    'url' => 'https://www.patrick-blom.de/',
    'email' => 'info@patrick-blom.de',
    'extend' => [
        \OxidEsales\Eshop\Core\Email::class => \PaBlo\MultiOrderMailReceiver\Core\Email::class
    ],
    'blocks' => [
        [
            'template' => 'shop_main.tpl',
            'block' => 'admin_shop_main_leftform',
            'file' => 'views/admin/blocks/shop_main__admin_shop_main_leftform.tpl'
        ]
    ],
    'events' => [
        'onActivate' => '\PaBlo\MultiOrderMailReceiver\Core\MultiOrderMailReceiver::onActivate',
        'onDeactivate' => '\PaBlo\MultiOrderMailReceiver\Core\MultiOrderMailReceiver::onDeactivate'
    ]
];

<?php

/**
 * Module configuration container
 */

return [
    'name'  => 'Pages',
    'description' => 'Pages module allows you to manage static pages on your site',
    'bookmarks' => [
        [
            'name' => 'Add new page',
            'controller' => 'Pages:Admin:Page@addAction',
            'icon' => 'fas fa-file-signature'
        ]
    ],
    'menu' => [
        'name' => 'Pages',
        'icon' => 'fas fa-file-signature',
        'items' => [
            [
                'route' => 'Pages:Admin:Page@indexAction',
                'name' => 'View all pages'
            ],
            [
                'route' => 'Pages:Admin:Page@addAction',
                'name' => 'Add new page'
            ]
        ]
    ]
];
<?php

/**
 * Module configuration container
 */

return array(
    'name'  => 'Pages',
    'description' => 'Pages module allows you to manage static pages on your site',
    // Bookmarks of this module
    'bookmarks' => array(
        array(
            'name' => 'Add new page',
            'controller' => 'Pages:Admin:Page@addAction',
            'icon' => 'fas fa-file-signature'
        )
    ),
    'menu' => array(
        'name' => 'Pages',
        'icon' => 'fas fa-file-signature',
        'items' => array(
            array(
                'route' => 'Pages:Admin:Page@indexAction',
                'name' => 'View all pages'
            ),
            array(
                'route' => 'Pages:Admin:Page@addAction',
                'name' => 'Add new page'
            )
        )
    )
);
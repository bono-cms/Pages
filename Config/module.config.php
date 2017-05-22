<?php

return array(
    'name'  => 'Pages',
    'caption' => 'Pages',
    'route' => 'Pages:Admin:Browser@indexAction',
    'icon' => 'fa fa-file-code-o fa-5x',
    'order' => 1,
    'description' => 'Pages module allows you to manage static pages on your site',

    // Bookmarks of this module
    'bookmarks' => array(
        array(
            'name' => 'Add new page',
            'controller' => 'Pages:Admin:Page@addAction',
            'icon' => 'glyphicon glyphicon-file'
        )
    )
);
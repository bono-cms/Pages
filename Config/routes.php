<?php

/**
 * This file is part of the Bono CMS
 * 
 * For the full copyright and license information, please view
 * the license file that was distributed with this source code.
 */

return [
    '/module/pages/(:var)' => [
        'controller' => 'Page@indexAction'
    ],

    '/%s/module/pages' => [
        'controller' => 'Admin:Page@indexAction',
    ],

    '/%s/module/pages/delete/(:var)' => [
        'controller' => 'Admin:Page@deleteAction',
        'disallow' => ['guest']
    ],
    
    '/%s/module/pages/tweak' => [
        'controller' => 'Admin:Page@tweakAction',
        'disallow' => ['guest']
    ],
    
    '/%s/module/pages/add' => [
        'controller' => 'Admin:Page@addAction'
    ],
    
    '/%s/module/pages/edit/(:var)' => [
        'controller' => 'Admin:Page@editAction'
    ],
    
    '/%s/module/pages/save' => [
        'controller' => 'Admin:Page@saveAction',
        'disallow' => ['guest']
    ]
];
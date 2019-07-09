<?php

/**
 * This file is part of the Bono CMS
 * 
 * Copyright (c) No Global State Lab
 * 
 * For the full copyright and license information, please view
 * the license file that was distributed with this source code.
 */

return array(
    '/module/pages/(:var)' => array(
        'controller' => 'Page@indexAction'
    ),

    '/%s/module/pages' => array(
        'controller' => 'Admin:Page@indexAction',
    ),

    '/%s/module/pages/delete/(:var)' => array(
        'controller' => 'Admin:Page@deleteAction',
        'disallow' => array('guest')
    ),
    
    '/%s/module/pages/tweak' => array(
        'controller' => 'Admin:Page@tweakAction',
        'disallow' => array('guest')
    ),
    
    '/%s/module/pages/add' => array(
        'controller' => 'Admin:Page@addAction'
    ),
    
    '/%s/module/pages/edit/(:var)' => array(
        'controller' => 'Admin:Page@editAction'
    ),
    
    '/%s/module/pages/save' => array(
        'controller' => 'Admin:Page@saveAction',
        'disallow' => array('guest')
    )
);

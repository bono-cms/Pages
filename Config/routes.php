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

    '/admin/module/pages/filter/(:var)' => array(
        'controller' => 'Admin:Browser@filterAction'
    ),
    
    '/admin/module/pages' => array(
        'controller' => 'Admin:Browser@indexAction',
    ),
    
    '/admin/module/pages/browse/(:var)' => array(
        'controller' => 'Admin:Browser@indexAction'
    ),
    
    '/admin/module/pages/delete' => array(
        'controller' => 'Admin:Page@deleteAction',
        'disallow' => array('guest')
    ),
    
    '/admin/module/pages/tweak' => array(
        'controller' => 'Admin:Page@tweakAction',
        'disallow' => array('guest')
    ),
    
    '/admin/module/pages/add' => array(
        'controller' => 'Admin:Page@addAction'
    ),
    
    '/admin/module/pages/edit/(:var)' => array(
        'controller' => 'Admin:Page@editAction'
    ),
    
    '/admin/module/pages/save' => array(
        'controller' => 'Admin:Page@saveAction',
        'disallow' => array('guest')
    )
);

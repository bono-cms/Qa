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
    '/module/qa' => array(
        'controller' => 'Qa@indexAction'
    ),
    
    '/admin/module/qa/config' => array(
        'controller' => 'Admin:Config@indexAction'
    ),
    
    '/admin/module/qa/config.ajax' => array(
        'controller' => 'Admin:Config@saveAction',
        'disallow' => array('guest')
    ),
    
    '/admin/module/qa' => array(
        'controller' => 'Admin:Qa@gridAction'
    ),
    
    '/admin/module/qa/page/(:var)' => array(
        'controller' => 'Admin:Qa@gridAction'
    ),
    
    '/admin/module/qa/delete/(:var)' => array(
        'controller' => 'Admin:Qa@deleteAction',
        'disallow' => array('guest')
    ),
    
    '/admin/module/qa/tweak' => array(
        'controller' => 'Admin:Qa@tweakAction',
        'disallow' => array('guest')
    ),
    
    '/admin/module/qa/add' => array(
        'controller' => 'Admin:Qa@addAction'
    ),
    
    '/admin/module/qa/edit/(:var)'  =>  array(
        'controller' => 'Admin:Qa@editAction'
    ),
    
    '/admin/module/qa/save' => array(
        'controller' => 'Admin:Qa@saveAction',
        'disallow' => array('guest')
    )
);

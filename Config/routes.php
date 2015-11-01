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
        'controller' => 'Admin:Browser@indexAction'
    ),
    
    '/admin/module/qa/page/(:var)' => array(
        'controller' => 'Admin:Browser@indexAction'
    ),
    
    '/admin/module/qa/delete.ajax' => array(
        'controller' => 'Admin:Browser@deleteAction',
        'disallow' => array('guest')
    ),
    
    '/admin/module/qa/delete-selected.ajax' => array(
        'controller' => 'Admin:Browser@deleteSelectedAction',
        'disallow' => array('guest')
    ),
    
    '/admin/module/qa/save.ajax' => array(
        'controller' => 'Admin:Browser@saveAction',
        'disallow' => array('guest')
    ),
    
    '/admin/module/qa/add' => array(
        'controller' => 'Admin:Add@indexAction'
    ),
    
    '/admin/module/qa/add.ajax' => array(
        'controller' => 'Admin:Add@addAction',
        'disallow' => array('guest')
    ),
    
    '/admin/module/qa/edit/(:var)'  =>  array(
        'controller' => 'Admin:Edit@indexAction'
    ),
    
    '/admin/module/qa/edit.ajax' => array(
        'controller' => 'Admin:Edit@updateAction',
        'disallow' => array('guest')
    )
);

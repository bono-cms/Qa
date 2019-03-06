<?php

/**
 * Module configuration container
 */

return array(
    'name' => 'Qa',
    'description' => 'QA module allows you to make Questions and Answers page on your site',
    'menu' => array(
        'name' => 'Questions and Answers',
        'icon' => 'fas fa-question-circle',
        'items' => array(
            array(
                'route' => 'Qa:Admin:Qa@gridAction',
                'name' => 'View all questions and answers'
            ),
            array(
                'route' => 'Qa:Admin:Qa@addAction',
                'name' => 'Add new pair'
            ),
            array(
                'route' => 'Qa:Admin:Config@indexAction',
                'name' => 'Configuration'
            )
        )
    )
);
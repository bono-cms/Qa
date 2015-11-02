<?php

/**
 * This file is part of the Bono CMS
 * 
 * Copyright (c) No Global State Lab
 * 
 * For the full copyright and license information, please view
 * the license file that was distributed with this source code.
 */

namespace Qa\Controller\Admin;

use Cms\Controller\Admin\AbstractController;
use Krystal\Validate\Pattern;

abstract class AbstractQa extends AbstractController
{
    /**
     * Returns prepared and configured validator's instance
     * 
     * @param array $input Raw input data
     * @return \Krystal\Validate\ValidatorChain
     */
    final protected function getValidator(array $input)
    {
        return $this->validatorFactory->build(array(
            'input' => array(
                'source' => $input,
                'definition' => array(
                    'question' => array(
                        'required' => true,
                        'rules' => array(
                            'NotEmpty' => array(
                                'message' => 'Question is required'
                            ),
                            'NoTags' => array(
                                'message' => 'Question can not contain HTML tags'
                            )
                        )
                    ),
                    'questioner' => array(
                        'required' => true,
                        'rules' => array(
                            'NotEmpty' => array(
                                'message' => 'Questioner is required'
                            ),
                            'NoTags' => array(
                                'message' => 'Questioner can not contain HTML tags'
                            )
                        )
                    ),
                    'answerer' => array(
                        'required' => true,
                        'rules' => array(
                            'NotEmpty' => array(
                                'message' => 'Answerer is required'
                            ),
                            'NoTags' => array(
                                'message' => 'Answerer can not contain HTML tags'
                            )
                        )
                    ),
                    'answer' => array(
                        'required' => true,
                        'rules' => array(
                            'NotEmpty' => array(
                                'message' => 'Answer is required'
                            )
                        )
                    ),
                    'date_asked' => new Pattern\DateFormat('m/d/Y'),
                    'date_answered' => new Pattern\DateFormat('m/d/Y')
                )
            )
        ));
    }

    /**
     * Returns Qa manager
     * 
     * @return \Qa\Service\QaManager 
     */
    final protected function getQaManager()
    {
        return $this->getModuleService('qaManager');
    }

    /**
     * Returns template path
     * 
     * @return string
     */
    final protected function getTemplatePath()
    {
        return 'qa.form';
    }

    /**
     * Loads breadcrumbs
     * 
     * @param string $title
     * @return void
     */
    final protected function loadBreadcrumbs($title)
    {
        $this->view->getBreadcrumbBag()->addOne('Questions and Answers', 'Qa:Admin:Browser@indexAction')
                                       ->addOne($title);
    }

    /**
     * Loads shared plugins
     * 
     * @return void
     */
    final protected function loadSharedPlugins()
    {
        $this->view->getPluginBag()
                   ->appendScript('@Qa/admin/qa.form.js')
                   ->load(array($this->getWysiwygPluginName(), 'datepicker'));
    }
}

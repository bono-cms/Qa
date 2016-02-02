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
use Krystal\Stdlib\VirtualEntity;
use Krystal\Validate\Pattern;

final class Qa extends AbstractController
{
    /**
     * Returns Qa manager
     * 
     * @return \Qa\Service\QaManager 
     */
    private function getQaManager()
    {
        return $this->getModuleService('qaManager');
    }

    /**
     * Creates a form
     * 
     * @param \Krystal\Stdlib\VirtualEntity $qa
     * @param string $title
     * @return string
     */
    private function createForm(VirtualEntity $qa, $title)
    {
        // Load view plugins
        $this->view->getPluginBag()
                   ->appendScript('@Qa/admin/qa.form.js')
                   ->load(array($this->getWysiwygPluginName(), 'datepicker'));

        // Append breadcrumbs
        $this->view->getBreadcrumbBag()->addOne('Questions and Answers', 'Qa:Admin:Qa@gridAction')
                                       ->addOne($title);

        return $this->view->render('qa.form', array(
            'timeFormat' => $this->getQaManager()->getTimeFormat(),
            'qa' => $qa
        ));
    }

    /**
     * Renders empty form
     * 
     * @return string
     */
    public function addAction()
    {
        $qa = new VirtualEntity();
        $qa->setTimestampAsked(time())
           ->setTimestampAnswered(time())
           ->setPublished(true);

        return $this->createForm($qa, 'Add a pair');
    }

    /**
     * Renders edit form
     * 
     * @param string $id
     * @return string
     */
    public function editAction($id)
    {
        $qa = $this->getQaManager()->fetchById($id);

        if ($qa !== false) {
            return $this->createForm($qa, 'Edit the pair');
        } else {
            return false;
        }
    }

    /**
     * Renders a grid
     * 
     * @param integer $page Current page number
     * @return string
     */
    public function gridAction($pageNumber = 1)
    {
        // Load view plugins
        $this->view->getPluginBag()
                   ->appendScript('@Qa/admin/browser.js');

        // Append a breadcrumb
        $this->view->getBreadcrumbBag()
                   ->addOne('Questions and Answers');
        
        $qaManager = $this->getQaManager();

        $paginator = $qaManager->getPaginator();
        $paginator->setUrl('/admin/module/qa/page/(:var)');

        return $this->view->render('browser', array(
            'title' => 'Questions and Answers',
            'pairs' => $qaManager->fetchAllByPage($pageNumber, $this->getSharedPerPageCount()),
            'paginator' => $paginator,
        ));
    }

    /**
     * Saves options
     * 
     * @return string
     */
    public function tweakAction()
    {
        if ($this->request->hasPost('published')) {
            $published = $this->request->getPost('published');

            $this->getQaManager()->updatePublished($published);
            $this->flashBag->set('success', 'Settings have been updated successfully');
            return '1';
        }
    }

    /**
     * Removes a pair by its associated id
     * 
     * @return string
     */
    public function deleteAction()
    {
        return $this->invokeRemoval('qaManager');
    }

    /**
     * Persists the pair
     * 
     * @return string
     */
    public function saveAction()
    {
        $input = $this->request->getPost('qa');
        $data = array_merge($input, array('ip' => $this->request->getClientIp()));

        return $this->invokeSave('qaManager', $input['id'], $data, array(
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
}

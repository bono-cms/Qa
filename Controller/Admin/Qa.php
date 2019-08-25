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
     * Returns shared form validator
     * 
     * @param array $input
     * @return \Krystal\Validate\ValidatorChain
     */
    private function getValidator(array $input)
    {
        return $this->createValidator(array(
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

        return $this->createForm($qa, 'Add new pair');
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
            return $this->createForm($qa, $this->translator->translate('Edit the pair "%s"', $qa->getQuestion()));
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
        // Append a breadcrumb
        $this->view->getBreadcrumbBag()
                   ->addOne('Questions and Answers');
        
        $qaManager = $this->getQaManager();

        $paginator = $qaManager->getPaginator();
        $paginator->setUrl($this->createUrl('Qa:Admin:Qa@gridAction', array(), 1));

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
     * @param string $id
     * @return string
     */
    public function deleteAction($id)
    {
        $service = $this->getModuleService('qaManager');

        // Batch removal
        if ($this->request->hasPost('batch')) {
            $ids = array_keys($this->request->getPost('batch'));

            $service->deleteByIds($ids);
            $this->flashBag->set('success', 'Selected elements have been removed successfully');

        } else {
            $this->flashBag->set('warning', 'You should select at least one element to remove');
        }

        // Single removal
        if (!empty($id)) {
            $service->deleteById($id);
            $this->flashBag->set('success', 'Selected element has been removed successfully');
        }

        return '1';
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

        $formValidator = $this->getValidator($input);

        if ($formValidator->isValid()) {
            $service = $this->getModuleService('qaManager');

            if (!empty($input['id'])) {
                if ($service->update($data)) {
                    $this->flashBag->set('success', 'The element has been updated successfully');
                    return '1';
                }

            } else {
                if ($service->add($data)) {
                    $this->flashBag->set('success', 'The element has been created successfully');
                    return $service->getLastId();
                }
            }

        } else {
            return $formValidator->getErrors();
        }
    }
}

<?php

/**
 * This file is part of the Bono CMS
 * 
 * Copyright (c) No Global State Lab
 * 
 * For the full copyright and license information, please view
 * the license file that was distributed with this source code.
 */

namespace Qa\Controller;

use Site\Controller\AbstractController;
use Krystal\Validate\Pattern;

final class Qa extends AbstractController
{
    /**
     * Lists all pairs with pagination
     * 
     * @param string $id Page id
     * @param string $pageNumber Page id
     * @param string $code Language code
     * @param string $slug Page slug
     * @return string
     */
    public function indexAction($id = false, $pageNumber = 1, $code = null, $slug = null)
    {
        if ($this->request->isPost()) {
            return $this->submitAction();
        } else {
            return $this->showAction($id, $pageNumber, $code, $slug);
        }
    }

    /**
     * Displays a front page
     * 
     * @param string $id Page id
     * @param string $pageNumber Page number
     * @param string $code Language code
     * @param string $slug Page slug
     * @return string
     */
    private function showAction($id, $pageNumber, $code, $slug)
    {
        $page = $this->getService('Pages', 'pageManager')->fetchById($id);

        if ($page !== false) {
            $this->loadSitePlugins();
            $this->view->getBreadcrumbBag()->addOne($page->getName());

            $qaManager = $this->getModuleService('qaManager');
            $pairs = $qaManager->fetchAllPublishedByPage($pageNumber, $this->getConfig()->getPerPageCount());

            // Tweak pagination service
            $paginator = $qaManager->getPaginator();
            $this->preparePaginator($paginator, $code, $slug, $pageNumber);

            return $this->view->render('qa', array(
                'pairs' => $pairs,
                'paginator' => $paginator,
                'page' => $page
            ));

        } else {
            return false;
        }
    }

    /**
     * Handles form submission
     * 
     * @return string
     */
    private function submitAction()
    {
        $input = $this->request->getPost();

        $formValidator = $this->createValidator(array(
            'input' => array(
                'source' => $input,
                'definition' => array(
                    'questioner' => new Pattern\Name(),
                    'question' => new Pattern\Message(),
                    'captcha' => new Pattern\Captcha($this->captcha)
                )
            )
        ));

        if ($formValidator->isValid()) {
            $data = array_merge($this->request->getPost(), array('ip' => $this->request->getClientIP()));
            $qaManager = $this->getModuleService('qaManager');

            if ($qaManager->send($data)) {
                $this->sendMessage($input);
                $this->flashBag->set('success', 'Your question has been sent! Thank you!');
                return '1';
            }

        } else {
            return $formValidator->getErrors();
        }
    }

    /**
     * Sends a message from the input
     * 
     * @param array $input Raw input data
     * @return boolean
     */
    private function sendMessage(array $input)
    {
        // Render the body firstly
        $message = $this->view->renderRaw($this->moduleName, 'messages', 'notification', array(
            'input' => $input
        ));

        // Prepare a subject
        $subject = $this->translator->translate('You have received a new question from %s', $input['questioner']);

        // Grab mailer service
        $mailer = $this->getService('Cms', 'mailer');
        return $mailer->send($subject, $message, 'A new question waits for you to answer');
    }

    /**
     * Returns configuration entity
     * 
     * @return \Krystal\Stdlib\VirtualEntity
     */
    private function getConfig()
    {
        return $this->getModuleService('configManager')->getEntity();
    }
}

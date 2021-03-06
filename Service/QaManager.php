<?php

/**
 * This file is part of the Bono CMS
 * 
 * Copyright (c) No Global State Lab
 * 
 * For the full copyright and license information, please view
 * the license file that was distributed with this source code.
 */

namespace Qa\Service;

use Cms\Service\HistoryManagerInterface;
use Cms\Service\AbstractManager;
use Qa\Storage\QaMapperInterface;
use Krystal\Stdlib\VirtualEntity;
use Krystal\Stdlib\ArrayUtils;
use Krystal\Security\Filter;

final class QaManager extends AbstractManager implements QaManagerInterface
{
    /**
     * Any compliant QA mapper
     * 
     * @var \QA\Storage\QaMapperInterface
     */
    private $qaMapper;

    /**
     * History manager to keep track
     * 
     * @var \Cms\Service\HistoryManagerInterface
     */
    private $historyManager;

    /**
     * State initialization
     * 
     * @param \QA\Storage\QaMapperInterface $qaMapper
     * @param \Cms\Service\HistoryManagerInterface $historyManager
     * @return void
     */
    public function __construct(QaMapperInterface $qaMapper, HistoryManagerInterface $historyManager)
    {
        $this->qaMapper = $qaMapper;
        $this->historyManager = $historyManager;
    }

    /**
     * {@inheritDoc}
     */
    protected function toEntity(array $qa)
    {
        $entity = new VirtualEntity();
        $entity->setId($qa['id'], VirtualEntity::FILTER_INT)
              ->setLangId($qa['lang_id'], VirtualEntity::FILTER_INT)
              ->setQuestion($qa['question'], VirtualEntity::FILTER_HTML)
              ->setAnswer($qa['answer'], VirtualEntity::FILTER_SAFE_TAGS)
              ->setQuestioner($qa['questioner'], VirtualEntity::FILTER_HTML)
              ->setAnswerer($qa['answerer'], VirtualEntity::FILTER_HTML)
              ->setPublished($qa['published'], VirtualEntity::FILTER_BOOL)
              ->setTimestampAsked($qa['timestamp_asked'], VirtualEntity::FILTER_INT)
              ->setTimestampAnswered($qa['timestamp_answered'], VirtualEntity::FILTER_INT)
              ->setDateAsked(strtotime($qa['timestamp_asked']))
              ->setDateAnswered(strtotime($qa['timestamp_answered']))
              ->setIP($qa['ip'], VirtualEntity::FILTER_HTML);

        return $entity;
    }

    /**
     * Delete QA pairs by their associated ids
     * 
     * @param array $ids
     * @return boolean
     */
    public function deleteByIds(array $ids)
    {
        foreach ($ids as $id) {
            if (!$this->qaMapper->deleteById($id)) {
                return false;
            }
        }

        $this->track('%s questions have been removed', count($ids));
        return true;
    }

    /**
     * Deletes QA pair by its associated id
     * 
     * @param string $id QA id
     * @return boolean
     */
    public function deleteById($id)
    {
        // Grab question title before we remove id
        $question = Filter::escape($this->qaMapper->fetchQuestionById($id));

        if ($this->qaMapper->deleteById($id)) {
            $this->track('Question "%s" has been removed', $question);
            return true;
            
        } else {
            return false;
        }
    }

    /**
     * Updates published states by their associate ids
     * 
     * @param array $pair Array of ids
     * @return boolean
     */
    public function updatePublished(array $pair)
    {
        foreach ($pair as $id => $published) {
            if (!$this->qaMapper->updatePublishedById($id, $published)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Sends data from a user
     * 
     * @param array $input Raw input data
     * @return boolean
     */
    public function send(array $input)
    {
        // Save current timestamp
        $timestamp = time();

        // Defaults
        $input['timestamp_asked'] = $timestamp;
        $input['timestamp_answered'] = $timestamp;
        $input['answerer'] = '';
        $input['answer'] = '';
        $input['published'] = '0';

        return $this->qaMapper->insert(ArrayUtils::arrayWithout($input, array('captcha')));
    }

    /**
     * Returns default time format
     * 
     * @return string
     */
    public function getTimeFormat()
    {
        return 'm/d/Y';
    }

    /**
     * Returns last QA id
     * 
     * @return integer
     */
    public function getLastId()
    {
        return $this->qaMapper->getLastId();
    }

    /**
     * Returns prepared paginator's instance
     * 
     * @return \Krystal\Paginate\Paginator
     */
    public function getPaginator()
    {
        return $this->qaMapper->getPaginator();
    }

    /**
     * Fetches question bag by its associated id
     * 
     * @param string $id
     * @return \Krystal\Stdlib\VirtualBag|boolean
     */
    public function fetchById($id)
    {
        return $this->prepareResult($this->qaMapper->fetchById($id));
    }

    /**
     * Fetches all QA entities filtered by pagination
     * 
     * @param integer $page Current page number
     * @param integer $itemsPerPage Per page count
     * @return array
     */
    public function fetchAllByPage($page, $itemsPerPage)
    {
        return $this->prepareResults($this->qaMapper->fetchAllByPage($page, $itemsPerPage));
    }

    /**
     * Fetches all published QA entities filtered by pagination
     * 
     * @param integer $page Current page
     * @param integer $itemsPerPage Per page count
     * @return array
     */
    public function fetchAllPublishedByPage($page, $itemsPerPage)
    {
        return $this->prepareResults($this->qaMapper->fetchAllPublishedByPage($page, $itemsPerPage));
    }

    /**
     * Prepare raw input data before sending to a mapper
     * 
     * @param array $data
     * @return void
     */
    private function prepareInput(array $input)
    {
        $input['timestamp_asked'] = strtotime($input['date_asked']);
        $input['timestamp_answered'] = strtotime($input['date_answered']);

        return ArrayUtils::arrayWithout($input, array('date_asked', 'date_answered'));
    }

    /**
     * Adds a QA pair
     * 
     * @param array $input Raw form data
     * @return boolean
     */
    public function add(array $input)
    {
        $input = $this->prepareInput($input);
        $this->track('Question "%s" has been created', $input['question']);
        
        return $this->qaMapper->insert($input);
    }

    /**
     * Updates QA pair
     * 
     * @param array $input Raw form data
     * @return boolean
     */
    public function update(array $input)
    {
        $input = $this->prepareInput($input);
        $this->track('Question "%s" has been updated', $input['question']);

        return $this->qaMapper->update($input);
    }

    /**
     * Tracks activity
     * 
     * @param string $message
     * @param string $placeholder
     * @return boolean
     */
    private function track($message, $placeholder)
    {
        return $this->historyManager->write('QA', $message, $placeholder);
    }
}

<?php

/**
 * This file is part of the Bono CMS
 * 
 * Copyright (c) No Global State Lab
 * 
 * For the full copyright and license information, please view
 * the license file that was distributed with this source code.
 */

namespace Qa\Storage;

interface QaMapperInterface
{
    /**
     * Fetches all published QA pairs filtered by pagination
     * 
     * @param integer $page Current page
     * @param integer $itemsPerPage Per page count
     * @return array
     */
    public function fetchAllPublishedByPage($page, $itemsPerPage);

    /**
     * Fetches question data by QA's associated id
     * 
     * @param string $id
     * @return string
     */
    public function fetchQuestionById($id);

    /**
     * Fetches all published QA pairs
     * 
     * @return array
     */
    public function fetchAllPublished();

    /**
     * Fetches all QA pairs filtered by pagination
     * 
     * @param integer $page Current page
     * @param integer $itemsPerPage Per page count
     * @return array
     */
    public function fetchAllByPage($page, $itemsPerPage);

    /**
     * Updates QA data
     * 
     * @param array $input Raw input data
     * @return boolean
     */
    public function update(array $data);

    /**
     * Inserts QA data
     * 
     * @param array $input Raw input data
     * @return boolean
     */
    public function insert(array $data);

    /**
     * Updates published state by QA's associated id
     * 
     * @param string $id
     * @param string $published Either 0 or 1
     * @return boolean
     */
    public function updatePublishedById($id, $published);

    /**
     * Fetches QA data by its associated id
     * 
     * @param string $id
     * @return array
     */
    public function fetchById($id);

    /**
     * Deletes QA pair by its associated id
     * 
     * @param string $id
     * @return boolean
     */
    public function deleteById($id);
}

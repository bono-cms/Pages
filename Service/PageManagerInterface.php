<?php

/**
 * This file is part of the Bono CMS
 * 
 * Copyright (c) No Global State Lab
 * 
 * For the full copyright and license information, please view
 * the license file that was distributed with this source code.
 */

namespace Pages\Service;

/* API for Page Manager */
interface PageManagerInterface
{
    /**
     * Fetches default page bag
     * 
     * @return object|boolean
     */
    public function fetchDefault();

    /**
     * Updates page's seo property by its associated id
     * 
     * @param array $pair
     * @return boolean
     */
    public function updateSeo(array $pair);

    /**
     * Makes a page id default one
     * 
     * @param string $id Some exiting page id
     * @return boolean
     */
    public function makeDefault($id);

    /**
     * Fetches all pages filtered by pagination
     * 
     * @param string $page Current page
     * @param string $itemsPerPage Items per page count
     * @return array
     */
    public function fetchAllByPage($page, $itemsPerPage);

    /**
     * Returns prepared paginator instance
     * 
     * @return \Krystal\Paginate\Paginator
     */
    public function getPaginator();

    /**
     * Returns last page id
     * 
     * @return integer
     */
    public function getLastId();

    /**
     * Adds a page
     * 
     * @param array $input Raw input data
     * @return boolean
     */
    public function add(array $input);

    /**
     * Updates a page
     * 
     * @param array $input Raw input data
     * @return boolean
     */
    public function update(array $input);

    /**
     * Deletes a page by its associated id
     * 
     * @param string $id
     * @return boolean
     */
    public function deleteById($id);

    /**
     * Delete pages by their associated ids
     * 
     * @param array $ids
     * @return boolean
     */
    public function deleteByIds(array $ids);

    /**
     * Fetches a record by its associated id
     * 
     * @param string $id
     * @param boolean $withTranslations Whether to fetch translations
     * @return \Krystal\Stdlib\VirtualEntity
     */
    public function fetchById($id, $withTranslations = false);
}

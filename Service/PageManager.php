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

use Cms\Service\AbstractManager;
use Cms\Service\WebPageManagerInterface;
use Cms\Service\HistoryManagerInterface;
use Pages\Storage\PageMapperInterface;
use Pages\Storage\DefaultMapperInterface;
use Menu\Contract\MenuAwareManager;
use Menu\Service\MenuWidgetInterface;
use Krystal\Security\Filter;
use Krystal\Stdlib\ArrayUtils;
use Krystal\Db\Filter\FilterableServiceInterface;

final class PageManager extends AbstractManager implements PageManagerInterface, FilterableServiceInterface, MenuAwareManager
{
    /**
     * Any compliant page mapper
     * 
     * @var \Pages\Storage\PageMapperInterface
     */
    private $pageMapper;

    /**
     * A mapper which is responsible for handling default page ids with language associations
     * 
     * @var \Page\Storage\DefaultMapper
     */
    private $defaultMapper;

    /**
     * Web page manager is responsible for managing slugs
     * 
     * @var \Cms\Service\WebPageManagerInterface
     */
    private $webPageManager;

    /**
     * History Manager to track activity
     * 
     * @var \Cms\Service\HistoryManagerInterface
     */
    private $historyManager;

    /**
     * State initialization
     * 
     * @param \Page\Storage\PageMapperInterface $pageMapper
     * @param \Page\Storage\DefaultMapper $defaultMapper
     * @param \Cms\Service\WebPageManagerInterface $webPageManager
     * @param \Cms\Service\HistoryManagerInterface $historyManager
     * @param \Menu\Service\MenuWidgetInterface $menuWidget Optional menu widget service
     * @return void
     */
    public function __construct(
        PageMapperInterface $pageMapper, 
        DefaultMapperInterface $defaultMapper, 
        WebPageManagerInterface $webPageManager, 
        HistoryManagerInterface $historyManager
    ){
        $this->pageMapper = $pageMapper;
        $this->defaultMapper = $defaultMapper;
        $this->webPageManager = $webPageManager;
        $this->historyManager = $historyManager;
    }

    /**
     * Fetches web page id by associated page id
     * 
     * @param string $id Page id
     * @return string
     */
    public function fetchWebPageIdById($id)
    {
        return $this->pageMapper->fetchWebPageIdById($id);
    }

    /**
     * Filters the raw input
     * 
     * @param array|\ArrayAccess $input Raw input data
     * @param integer $page Current page number
     * @param integer $itemsPerPage Items per page to be displayed
     * @param string $sortingColumn Column name to be sorted
     * @param string $desc Whether to sort in DESC order
     * @return array
     */
    public function filter($input, $page, $itemsPerPage, $sortingColumn, $desc)
    {
        return $this->prepareResults($this->pageMapper->filter($input, $page, $itemsPerPage, $sortingColumn, $desc));
    }

    /**
     * Returns default web page id
     * 
     * @return integer
     */
    public function getDefaultWebPageId()
    {
        $id = $this->defaultMapper->fetchDefaultId();
        return (int) $this->pageMapper->fetchWebPageIdByPageId($id);
    }

    /**
     * {@inheritDoc}
     */
    public function fetchNameByWebPageId($webPageId)
    {
        return $this->pageMapper->fetchNameByWebPageId($webPageId);
    }

    /**
     * {@inheritDoc}
     */
    protected function toEntity(array $page)
    {
        $entity = new PageEntity();
        $entity->setId($page['id'], PageEntity::FILTER_INT)
                ->setLangId($page['lang_id'], PageEntity::FILTER_INT)
                ->setWebPageId($page['web_page_id'], PageEntity::FILTER_INT)
                ->setContent($page['content'], PageEntity::FILTER_SAFE_TAGS)
                ->setSlug($page['slug'], PageEntity::FILTER_TAGS)
                ->setController($page['controller'], PageEntity::FILTER_TAGS)
                ->setTemplate($page['template'], PageEntity::FILTER_TAGS)
                ->setProtected($page['protected'], PageEntity::FILTER_BOOL)
                
                // @TODO Fix this
                ->setDefault($page['id'], PageEntity::FILTER_BOOL)
                
                ->setSeo($page['seo'], PageEntity::FILTER_BOOL)
                ->setUrl($this->webPageManager->surround($entity->getSlug(), $entity->getLangId()))
                ->setPermanentUrl('/module/pages/'.$entity->getId())

                // Meta data
                ->setTitle($page['title'], PageEntity::FILTER_HTML)
                ->setName($page['name'], PageEntity::FILTER_HTML)
                ->setMetaDescription($page['meta_description'], PageEntity::FILTER_HTML)
                ->setKeywords($page['keywords'], PageEntity::FILTER_HTML);

        return $entity;
    }

    /**
     * Fetches entity of default page
     * 
     * @return \Krystal\Stdlib\VirtualEntity|boolean
     */
    public function fetchDefault()
    {
        $id = $this->defaultMapper->fetchDefaultId();

        if ($id) {
            return $this->fetchById($id);
        } else {
            return false;
        }
    }

    /**
     * Updates page's SEO property by its associated id
     * 
     * @param array $pair
     * @return boolean
     */
    public function updateSeo(array $pair)
    {
        foreach ($pair as $id => $seo) {
            if (!$this->pageMapper->updateSeoById($id, $seo)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Makes a page id default one
     * 
     * @param string $id Some exiting page id
     * @return boolean
     */
    public function makeDefault($id)
    {
        if ($this->defaultMapper->exists()) {
            return $this->defaultMapper->update($id);
        } else {
            return $this->defaultMapper->insert($id);
        }
    }

    /**
     * Fetches all page entities filtered by pagination
     * 
     * @param string $page Current page
     * @param string $itemsPerPage Items per page count
     * @return array
     */
    public function fetchAllByPage($page, $itemsPerPage)
    {
        return $this->prepareResults($this->pageMapper->fetchAllByPage($page, $itemsPerPage));
    }

    /**
     * Returns prepared paginator instance
     * 
     * @return \Krystal\Paginate\Paginator
     */
    public function getPaginator()
    {
        return $this->pageMapper->getPaginator();
    }

    /**
     * Returns last page id
     * 
     * @return integer
     */
    public function getLastId()
    {
        return $this->pageMapper->getLastId();
    }

    /**
     * Save a page
     * 
     * @param array $input
     * @return boolean
     */
    private function savePage(array $input)
    {
        $data = ArrayUtils::arrayWithout($input['page'], array('controller', 'makeDefault', 'slug', 'menu'));

        return $this->pageMapper->savePage('Pages', 'Pages:Page@indexAction', $data, $input['translation']);
    }

    /**
     * Adds a page
     * 
     * @param array $input Raw input data
     * @return boolean
     */
    public function add(array $input)
    {
        $this->savePage($input);

        // It was inserted successfully
        $id = $this->getLastId();

        // If checkbox was checked
        if (isset($input['page']['makeDefault']) && $input['page']['makeDefault'] == '1') {
            $this->makeDefault($id);
        }

        #$this->track('A new "%s" page has been created', $page['name']);
        return true;
    }

    /**
     * Updates a page
     * 
     * @param array $input Raw input data
     * @return boolean
     */
    public function update(array $input)
    {
        $this->savePage($input);

        #$this->track('The page "%s" has been updated', $page['name']);
        return true;
    }

    /**
     * Deletes a page by its associated id
     * 
     * @param string $id Page's id
     * @return boolean
     */
    public function deleteById($id)
    {
        // Gotta grab page's title, before removing it
        #$name = Filter::escape($this->pageMapper->fetchNameById($id));

        if ($this->pageMapper->deletePage($id)) {
            #$this->track('The page "%s" has been removed', $name);
            return true;

        } else {
            return false;
        }
    }

    /**
     * Delete pages by their associated ids
     * 
     * @param array $ids
     * @return boolean
     */
    public function deleteByIds(array $ids)
    {
        $this->pageMapper->deletePage($ids);

        $this->track('%s pages have been removed', count($ids));
        return true;
    }

    /**
     * Fetches a record by its associated id
     * 
     * @param string $id
     * @param boolean $withTranslations Whether to fetch translations
     * @return \Krystal\Stdlib\VirtualEntity
     */
    public function fetchById($id, $withTranslations = false)
    {
        if ($withTranslations === true) {
            return $this->prepareResults($this->pageMapper->fetchById($id, true));
        } else {
            return $this->prepareResult($this->pageMapper->fetchById($id, false));
        }
    }

    /**
     * Tracks activity
     * 
     * @param string $message
     * @param string $placeholder
     * @return boolean
     */
    private function track($message, $placeholder = '')
    {
        return $this->historyManager->write('Pages', $message, $placeholder);
    }
}

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
use Krystal\Security\Filter;
use Krystal\Stdlib\ArrayUtils;
use Krystal\Db\Filter\FilterableServiceInterface;
use Krystal\Image\Tool\ImageManagerInterface;

final class PageManager extends AbstractManager implements PageManagerInterface, FilterableServiceInterface
{
    /**
     * Any compliant page mapper
     * 
     * @var \Pages\Storage\PageMapperInterface
     */
    private $pageMapper;

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
     * Image handler
     * 
     * @var \Krystal\Image\Tool\ImageManagerInterface
     */
    private $imageManager;

    /**
     * State initialization
     * 
     * @param \Page\Storage\PageMapperInterface $pageMapper
     * @param \Cms\Service\WebPageManagerInterface $webPageManager
     * @param \Cms\Service\HistoryManagerInterface $historyManager
     * @param \Krystal\Image\Tool\ImageManagerInterface $imageManager
     * @return void
     */
    public function __construct(
        PageMapperInterface $pageMapper, 
        WebPageManagerInterface $webPageManager, 
        HistoryManagerInterface $historyManager,
        ImageManagerInterface $imageManager
    ){
        $this->pageMapper = $pageMapper;
        $this->webPageManager = $webPageManager;
        $this->historyManager = $historyManager;
        $this->imageManager = $imageManager;
    }

    /**
     * Returns a collection of switching URLs
     * 
     * @param string $id Page ID
     * @param string $controller Optionally can be overridden
     * @return array
     */
    public function getSwitchUrls($id, $controller = null)
    {
        if (is_null($controller)) {
            $controller = 'Pages:Page@indexAction';
        }

        return $this->pageMapper->createSwitchUrls($id, 'Pages', $controller);
    }

    /**
     * Filters the raw input
     * 
     * @param array|\ArrayAccess $input Raw input data
     * @param integer $page Current page number
     * @param integer $itemsPerPage Items per page to be displayed
     * @param string $sortingColumn Column name to be sorted
     * @param string $desc Whether to sort in DESC order
     * @param array $parameters
     * @return array
     */
    public function filter($input, $page, $itemsPerPage, $sortingColumn, $desc, array $parameters = array())
    {
        return $this->prepareResults($this->pageMapper->filter($input, $page, $itemsPerPage, $sortingColumn, $desc, $parameters));
    }

    /**
     * Fetches web page id by associated page id
     * 
     * @param string $id Page id
     * @return string
     */
    public function fetchWebPageIdById($id)
    {
        $page = $this->fetchById($id);
        return $page->getWebPageId();
    }

    /**
     * Returns default web page id
     * 
     * @return integer
     */
    public function getDefaultWebPageId()
    {
        $page = $this->fetchDefault();
        return $page->getWebPageId();
    }

    /**
     * Fetches entity of default page
     * 
     * @return \Krystal\Stdlib\VirtualEntity|boolean
     */
    public function fetchDefault()
    {
        return $this->prepareResult($this->pageMapper->fetchDefault());
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
     * {@inheritDoc}
     */
    protected function toEntity(array $page)
    {
        $imageBag = clone $this->imageManager->getImageBag();
        $imageBag->setId($page['id'])
                 ->setCover($page['image']);

        $entity = new PageEntity();
        $entity->setId($page['id'], PageEntity::FILTER_INT)
                ->setImageBag($imageBag)
                ->setLangId($page['lang_id'], PageEntity::FILTER_INT)
                ->setWebPageId($page['web_page_id'], PageEntity::FILTER_INT)
                ->setContent($page['content'], PageEntity::FILTER_SAFE_TAGS)
                ->setSlug($page['slug'], PageEntity::FILTER_TAGS)
                ->setController($page['controller'], PageEntity::FILTER_TAGS)
                ->setTemplate($page['template'], PageEntity::FILTER_TAGS)
                ->setProtected($page['protected'], PageEntity::FILTER_BOOL)
                ->setSeo($page['seo'], PageEntity::FILTER_BOOL)
                ->setDefault($page['default'], PageEntity::FILTER_BOOL)
                ->setImage($page['image'])
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
        return $this->pageMapper->updateDefault((int) $id);
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
        // References
        $file =& $input['files']['file'];
        $data =& $input['data'];

        // If image file is selected
        if (!empty($file)) {
            // And finally append
            $data['page']['image'] = $file->getUniqueName();
        }

        // Use explicit controller if provided, otherwise fall back to default one
        $controller = isset($data['page']['controller']) ? $data['page']['controller'] : 'Pages:Page@indexAction';

        // Keep only page related attributes
        $data['page'] = ArrayUtils::arrayWithout($data['page'], array('controller', 'makeDefault', 'slug', 'menu', 'remove_cover'));

        return $this->pageMapper->savePage('Pages', $controller, $data['page'], $data['translation']);
    }

    /**
     * Adds a page
     * 
     * @param array $input Raw input data
     * @return boolean
     */
    public function add(array $input)
    {
        // References
        $file = isset($input['files']['file']) ? $input['files']['file'] : false;
        $data =& $input['data'];

        $this->savePage($input);

        // It was inserted successfully
        $id = $this->getLastId();

        // If checkbox was checked
        if (isset($data['page']['makeDefault']) && $data['page']['makeDefault'] == '1') {
            $this->makeDefault($id);
        }

        // If image file is selected
        if ($file) {
            $this->imageManager->upload($id, $file);
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
        // References
        $file = isset($input['files']['file']) ? $input['files']['file'] : false;
        $data =& $input['data'];
        $page =& $data['page'];

        // Allow to remove a cover, only it case it exists and checkbox was checked
        if (isset($page['remove_cover']) && !empty($page['image'])) {
            $this->imageManager->delete($page['id']);
            $page['image'] = '';
        } else {
            // If image file is selected
            if ($file) {
                // Remove previous image if any
                if (!empty($page['image'])) {
                    $this->imageManager->delete($page['id']);
                }

                // Now upload a new one
                $this->imageManager->upload($page['id'], $file);
            }
        }

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

        // Remove image if any
        $this->imageManager->delete($id);

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
        // Remove associated images if any
        $this->imageManager->deleteMany($ids);

        $this->pageMapper->deletePage($ids);

        $this->track('%s pages have been removed', count($ids));
        return true;
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

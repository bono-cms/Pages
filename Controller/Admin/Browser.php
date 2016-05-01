<?php

/**
 * This file is part of the Bono CMS
 * 
 * Copyright (c) No Global State Lab
 * 
 * For the full copyright and license information, please view
 * the license file that was distributed with this source code.
 */

namespace Pages\Controller\Admin;

use Cms\Controller\Admin\AbstractController;
use Krystal\Db\Filter\QueryContainer;

final class Browser extends AbstractController
{
    /**
     * Returns page manager
     * 
     * @return \Pages\Service\PageManager
     */
    private function getPageManager()
    {
        return $this->getModuleService('pageManager');
    }

    /**
     * Creates grid template
     * 
     * @param array $pages A collection of page entities
     * @param string $url
     * @return string
     */
    private function createGrid(array $pages, $url = null)
    {
        // Append a breadcrumb
        $this->view->getBreadcrumbBag()
                   ->addOne('Pages');

        $paginator = $this->getPageManager()->getPaginator();

        if ($url !== null) {
            $paginator->setUrl($url);
        }

        return $this->view->render('browser', array(
            'paginator' => $paginator,
            'pages' => $pages,
            'filter' => new QueryContainer($this->request->getQuery(), $this->createUrl('Pages:Admin:Browser@filterAction', array(null))),
        ));
    }

    /**
     * Applies a filter
     * 
     * @return string
     */
    public function filterAction()
    {
        $records = $this->getFilter($this->getPageManager(), $this->createUrl('Pages:Admin:Browser@filterAction', array(null)));

        if ($records !== false) {
            return $this->createGrid($records);
        } else {
            return $this->indexAction();
        }
    }

    /**
     * Renders a grid
     * 
     * @param string $page Current page
     * @return string
     */
    public function indexAction($page = 1)
    {
        $pages = $this->getPageManager()->fetchAllByPage($page, $this->getSharedPerPageCount());
        $url = $this->createUrl('Pages:Admin:Browser@indexAction', array(), 1);

        return $this->createGrid($pages, $url);
    }
}

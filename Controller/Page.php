<?php

/**
 * This file is part of the Bono CMS
 * 
 * Copyright (c) No Global State Lab
 * 
 * For the full copyright and license information, please view
 * the license file that was distributed with this source code.
 */

namespace Pages\Controller;

use Krystal\Stdlib\VirtualEntity;
use Krystal\Validate\Pattern;
use Site\Controller\AbstractController;

final class Page extends AbstractController
{
    /**
     * Renders a page by its associated id
     * 
     * @param string $id Page's id
     * @return string
     */
    public function indexAction($id)
    {
        // If id is null, then a default page must be fetched
        if (is_null($id)) {
            $page = $this->getPageManager()->fetchDefault();
        } else {
            $page = $this->getPageManager()->fetchById($id);
        }

        // If $page isn't false, then the right $id is supplied
        if ($page !== false) {
            $this->loadSitePlugins();

            // If page isn't default, then we append a breadcrumb
            if (!$page->getDefault()) {
                $this->view->getBreadcrumbBag()
                           ->addOne($page->getName());
            } else {
                // Otherwise we should never have breadcrumbs
                $this->view->getBreadcrumbBag()
                           ->clear();
            }

            // Append fields on demand
            $this->appendFieldsIfPossible($page);

            return $this->view->render($page->hasTemplate() ? $page->getTemplate() : 'pages-page', array(
                'page' => $page,
                'languages' => $this->getPageManager()->getSwitchUrls($id)
            ));

        } else {
            // Returning false from controller's action triggers 404 error automatically
            return false;
        }
    }

    /**
     * Displays "404: Not found" page
     * 
     * @return string
     */
    public function notFoundAction()
    {
        $this->loadSitePlugins();
        $this->view->getBreadcrumbBag()
                   ->addOne('404');

        $page = new VirtualEntity();
        $page->setTitle($this->translator->translate('Page not found'))
             ->setName($this->translator->translate('Page not found'))
             ->setContent($this->translator->translate('Requested page doesn\'t exist'))
             ->setSeo(false);

        // There's no need to set 404 status code here, as its handled by the router internally
        return $this->view->render('pages-404', array(
            'page' => $page,
            'languages' => $this->getService('Cms', 'languageManager')->fetchAll(true)
        ));
    }

    /**
     * Displays a home page
     * 
     * @return string
     */
    public function homeAction()
    {
        $pageManager = $this->getPageManager();
        $page = $pageManager->fetchDefault();

        if ($page !== false) {
            $this->loadSitePlugins();
            // Clear all breadcrumbs
            $this->view->getBreadcrumbBag()->clear();

            // Append fields if possible
            $this->appendFieldsIfPossible($page);

            return $this->view->render('pages-home', array(
                'page' => $page,
                'languages' => $this->getService('Cms', 'languageManager')->fetchAll(true)
            ));

        } else {
            // Returning false from a controller's action triggers 404 error automatically
            return false;
        }
    }

    /**
     * Returns page manager
     * 
     * @return \Pages\Service\PageManager
     */
    private function getPageManager()
    {
        return $this->getModuleService('pageManager');
    }
}

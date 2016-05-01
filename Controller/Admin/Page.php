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

use Krystal\Validate\Pattern;
use Krystal\Stdlib\VirtualEntity;
use Cms\Controller\Admin\AbstractController;
use Pages\Service\ControllerProvider;

final class Page extends AbstractController
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
     * Creates a form
     * 
     * @param \Krystal\Stdlib\VirtualEntity $page
     * @param string $title
     * @return string
     */
    private function createForm(VirtualEntity $page, $title)
    {
        // Load view plugins
        $this->loadMenuWidget();
        $this->view->getPluginBag()->load($this->getWysiwygPluginName())
                                   ->appendScript('@Pages/admin/page.form.js');

        // Append breadcrumbs
        $this->view->getBreadcrumbBag()->addOne('Pages', 'Pages:Admin:Browser@indexAction')
                                       ->addOne($title);

        $provider = new ControllerProvider($this->moduleManager->getRoutes());

        return $this->view->render('page.form', array(
            'controllers' => $provider->getControllers(),
            'page' => $page
        ));
    }

    /**
     * Renders empty form
     * 
     * @return string
     */
    public function addAction()
    {
        $page = new VirtualEntity();
        $page->setSeo(true)
             ->setController('Pages:Page@indexAction');

        return $this->createForm($page, 'Add a page');
    }

    /**
     * Renders edit form
     * 
     * @param string $id
     * @return string
     */
    public function editAction($id)
    {
        $page = $this->getPageManager()->fetchById($id);

        if ($page !== false) {
            return $this->createForm($page, 'Edit the page');
        } else {
            return false;
        }
    }

    /**
     * Deletes selected page by its associated id
     * 
     * @param string $id
     * @return string
     */
    public function deleteAction($id)
    {
        return $this->invokeRemoval('pageManager', $id);
    }

    /**
     * Saves options
     * 
     * @return string
     */
    public function tweakAction()
    {
        if ($this->request->hasPost('seo')) {
            $seo = $this->request->getPost('seo');

            if ($this->request->hasPost('default')) {
                $default = $this->request->getPost('default');
                $this->getPageManager()->makeDefault($default);
            }

            if ($this->getPageManager()->updateSeo($seo)) {
                $this->flashBag->set('success', 'Settings have been saved successfully');
                return '1';
            }
        }
    }

    /**
     * Persists a page
     * 
     * @return string
     */
    public function saveAction()
    {
        $input = $this->request->getPost('page');

        return $this->invokeSave('pageManager', $input['id'], $this->request->getPost(), array(
            'input' => array(
                'source' => $input,
                'definition' => array(
                    'title' => new Pattern\Title()
                )
            )
        ));
    }
}

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
     * @param \Krystal\Stdlib\VirtualEntity|array $page
     * @param string $title
     * @return string
     */
    private function createForm($page, $title)
    {
        // Load view plugins
        $this->view->getPluginBag()->load(array('preview', $this->getWysiwygPluginName()))
                                   ->appendScript('@Pages/admin/page.form.js');

        // Append breadcrumbs
        $this->view->getBreadcrumbBag()->addOne('Pages', 'Pages:Admin:Browser@indexAction')
                                       ->addOne($title);

        $provider = new ControllerProvider($this->moduleManager->getRoutes());

        return $this->view->render('page.form', array(
            'controllers' => $provider->getControllers(),
            'page' => $page,
            'new' => is_object($page)
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
        $page = $this->getPageManager()->fetchById($id, true);

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
        $service = $this->getModuleService('pageManager');

        // Batch removal
        if ($this->request->hasPost('toDelete')) {
            $ids = array_keys($this->request->getPost('toDelete'));

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

        $formValidator = $this->createValidator(array(
            'input' => array(
                'source' => $input,
                'definition' => array(
                    'name' => new Pattern\Name()
                )
            )
        ));

        if (1) {
            $service = $this->getModuleService('pageManager');

            if (!empty($input['id'])) {
                if ($service->update($this->request->getAll())) {
                    $this->flashBag->set('success', 'The element has been updated successfully');
                    return '1';
                }

            } else {
                if ($service->add($this->request->getAll())) {
                    $this->flashBag->set('success', 'The element has been created successfully');
                    return $service->getLastId();
                }
            }

        } else {
            return $formValidator->getErrors();
        }
    }
}

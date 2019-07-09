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

final class Browser extends AbstractController
{
    /**
     * Renders a grid
     * 
     * @return string
     */
    public function indexAction()
    {
        // Append a breadcrumb
        $this->view->getBreadcrumbBag()
                   ->addOne('Pages');

        $service = $this->getModuleService('pageManager');
        $pages = $this->getFilter($service);


        return $this->view->render('browser', array(
            'query' => $this->request->getQuery(),
            'paginator' => $service->getPaginator(),
            'pages' => $pages,
            'filterApplied' => $this->request->getQuery('filter', false)
        ));
    }
}

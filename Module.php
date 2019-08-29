<?php

/**
 * This file is part of the Bono CMS
 * 
 * Copyright (c) No Global State Lab
 * 
 * For the full copyright and license information, please view
 * the license file that was distributed with this source code.
 */

namespace Pages;

use Cms\AbstractCmsModule;
use Pages\Service\PageManager;
use Krystal\Image\Tool\ImageManager;
use Block\Service\FieldService;

final class Module extends AbstractCmsModule
{
    /**
     * Returns album image manager
     * 
     * @return \Krystal\Image\Tool\ImageManager
     */
    private function createImageManager()
    {
        $plugins = array(
            'thumb' => array(
                'dimensions' => array(
                    // Administration area
                    array(350, 350)
                )
            ),

            'original' => array(
                'prefix' => 'original'
            )
        );

        return new ImageManager(
            '/data/uploads/module/pages',
            $this->appConfig->getRootDir(),
            $this->appConfig->getRootUrl(),
            $plugins
        );
    }

    /**
     * {@inheritDoc}
     */
    public function getServiceProviders()
    {
        $pageMapper = $this->getMapper('/Pages/Storage/MySQL/PageMapper');

        return array(
            'pageManager' => new PageManager($pageMapper, $this->getWebPageManager(), $this->createImageManager()),
            'fieldService' => $this->moduleManager->isLoaded('Block') ? new FieldService($this->getMapper('\Pages\Storage\MySQL\PageExtraFieldMapper')) : null
        );
    }
}

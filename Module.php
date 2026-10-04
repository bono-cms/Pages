<?php

/**
 * This file is part of the Bono CMS
 * 
 * For the full copyright and license information, please view
 * the license file that was distributed with this source code.
 */

namespace Pages;

use Cms\AbstractCmsModule;
use Pages\Service\PageManager;
use Krystal\Image\Tool\ImageManager;

final class Module extends AbstractCmsModule
{
    /**
     * Returns album image manager
     * 
     * @return \Krystal\Image\Tool\ImageManager
     */
    private function createImageManager()
    {
        $plugins = [
            'thumb' => [
                'dimensions' => [
                    // Administration area
                    [350, 350]
                ]
            ],

            'original' => [
                'prefix' => 'original'
            ]
        ];

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

        return [
            'pageManager' => new PageManager($pageMapper, $this->getWebPageManager(), $this->createImageManager()),
            'blockFieldService' => $this->createFieldService('\Pages\Storage\MySQL\PageExtraFieldMapper')
        ];
    }
}

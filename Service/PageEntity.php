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

use Krystal\Stdlib\VirtualEntity;

final class PageEntity extends VirtualEntity
{
    /**
     * Checks whether page has image
     * 
     * @return boolean
     */
    public function hasImage()
    {
        return $this->getImage() != '';
    }

    /**
     * Returns image URL
     * 
     * @param string $size
     * @return string
     */
    public function getImageUrl($size)
    {
        return $this->getImageBag()->getUrl($size);
    }

    /**
     * The alias to getDefault() 
     * 
     * @return boolean
     */
    public function isDefault()
    {
        return $this->getDefault();
    }
}

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
     * Returns a single value of a field by its id
     * 
     * @param int $id Field id
     * @return string
     */
    public function getField($id)
    {
        $fields = $this->getFields();

        return isset($fields[$id]) ? $fields[$id] : null;
    }

    /**
     * Checks whether current page entity has custom template override
     * 
     * @return boolean
     */
    public function hasTemplate()
    {
        $template = $this->getTemplate();
        return trim($template) !== '';
    }

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

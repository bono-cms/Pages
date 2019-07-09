<?php

/**
 * This file is part of the Bono CMS
 * 
 * Copyright (c) No Global State Lab
 * 
 * For the full copyright and license information, please view
 * the license file that was distributed with this source code.
 */

namespace Pages\Storage;

interface PageMapperInterface
{
    /**
     * Marks page ID as a default one
     * 
     * @param string $id Page ID
     * @return boolean
     */
    public function updateDefault($id);

    /**
     * Updates whether SEO should be enabled or not
     * 
     * @param string $id Page id
     * @param string $seo Either 0 or 1
     * @return boolean
     */
    public function updateSeoById($id, $seo);

    /**
     * Fetches default page
     * 
     * @return array
     */
    public function fetchDefault();

    /**
     * Fetches page data by its associated id
     * 
     * @param string $id Page id
     * @param boolean $withTranslations Whether to fetch translations
     * @return array
     */
    public function fetchById($id, $withTranslations);
}

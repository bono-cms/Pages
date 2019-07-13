<?php

/**
 * This file is part of the Bono CMS
 * 
 * Copyright (c) No Global State Lab
 * 
 * For the full copyright and license information, please view
 * the license file that was distributed with this source code.
 */

namespace Pages\Storage\MySQL;

use Cms\Storage\MySQL\AbstractStorageDropper;

final class Dropper extends AbstractStorageDropper
{
    /**
     * {@inheritDoc}
     */
    protected function getTables()
    {
        return array(
            PageMapper::getTableName(),
            PageTranslationMapper::getTableName(),

            // Extra fields
            PageExtraFieldRelation::getTableName(),
            PageExtraFieldMapper::getTableName(),
            PageExtraFieldTranslationMapper::getTableName()
        );
    }
}

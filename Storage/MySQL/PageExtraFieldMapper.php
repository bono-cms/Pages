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

use Block\Storage\MySQL\AbstractFieldMapper;

final class PageExtraFieldMapper extends AbstractFieldMapper
{
    /**
     * {@inheritDoc}
     */
    public static function getTableName()
    {
        return self::getWithPrefix('bono_module_pages_extra_fields');
    }

    /**
     * {@inheritDoc}
     */
    public static function getTranslationTable()
    {
        return PageExtraFieldTranslationMapper::getTableName();
    }

    /**
     * {@inheritDoc}
     */
    public static function getRelationTable()
    {
        return PageExtraFieldRelation::getTableName();
    }
}

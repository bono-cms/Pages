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

use Cms\Storage\MySQL\AbstractMapper;
use Block\Storage\MySQL\CategoryFieldMapper;
use Block\Storage\MySQL\CategoryMapper;
use Block\Storage\SharedFieldInterface;

final class PageExtraFieldMapper extends AbstractMapper implements SharedFieldInterface
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
     * Find attached fields by page id
     * 
     * @param int $id
     * @return array
     */
    public function findFields($id)
    {
        // To be selected
        $columns = array(
            CategoryFieldMapper::column('id'),
            CategoryFieldMapper::column('name'),
            CategoryFieldMapper::column('type'),
            CategoryMapper::column('name') => 'category',
            PageExtraFieldMapper::column('value') // Non-translatable value
        );

        $db = $this->db->select($columns, true)
                       ->from(CategoryFieldMapper::getTableName())
                       ->leftJoin(CategoryMapper::getTableName(), array(
                            CategoryMapper::column('id') => CategoryFieldMapper::getRawColumn('category_id')
                       ))
                       // Block relation
                       ->leftJoin(PageExtraFieldRelation::getTableName(), array(
                            PageExtraFieldRelation::column('slave_id') => CategoryFieldMapper::getRawColumn('category_id')
                       ))
                       // Field value mapper
                       ->leftJoin(PageExtraFieldMapper::getTableName(), array(
                            PageExtraFieldMapper::column('page_id') => PageExtraFieldRelation::getRawColumn('master_id'),
                            PageExtraFieldMapper::column('field_id') => CategoryFieldMapper::getRawColumn('id'),
                       ))
                       ->whereEquals(PageExtraFieldRelation::column('master_id'), $id);

        return $db->queryAll();
    }

    /**
     * Find attached category ids
     * 
     * @param int $pageId Target page id
     * @return array
     */
    public function findAttachedSlaves($pageId)
    {
        return $this->getSlaveIdsFromJunction(PageExtraFieldRelation::getTableName(), $pageId);
    }

    /**
     * Save junction relation
     * 
     * @param int $pageId Target page id
     * @param array $slaveIds
     * @return boolean
     */
    public function saveRelation($pageId, array $slaveIds)
    {
        return $this->syncWithJunction(PageExtraFieldRelation::getTableName(), $pageId, $slaveIds);
    }
}

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
use Cms\Storage\MySQL\WebPageMapper;
use Cms\Contract\WebPageMapperAwareInterface;
use Pages\Storage\PageMapperInterface;
use Krystal\Db\Sql\RawSqlFragment;
use Krystal\Db\Filter\InputDecorator;

final class PageMapper extends AbstractMapper implements PageMapperInterface, WebPageMapperAwareInterface
{
    /**
     * {@inheritDoc}
     */
    public static function getTableName()
    {
        return self::getWithPrefix('bono_module_pages');
    }

    /**
     * {@inheritDoc}
     */
    public static function getTranslationTable()
    {
        return PageTranslationMapper::getTableName();
    }

    /**
     * Return shared columns to be selected
     * 
     * @return array
     */
    private function getColumns()
    {
        return array(
            self::getFullColumnName('id'),
            self::getFullColumnName('template'),
            self::getFullColumnName('protected'),
            self::getFullColumnName('seo'),
            self::getFullColumnName('default'),
            PageTranslationMapper::getFullColumnName('lang_id'),
            PageTranslationMapper::getFullColumnName('web_page_id'),
            PageTranslationMapper::getFullColumnName('content'),
            PageTranslationMapper::getFullColumnName('title'),
            PageTranslationMapper::getFullColumnName('name'),
            PageTranslationMapper::getFullColumnName('meta_description'),
            PageTranslationMapper::getFullColumnName('keywords'),

            // Web page meta columns
            WebPageMapper::getFullColumnName('slug'),
            WebPageMapper::getFullColumnName('controller'),
        );
    }

    /**
     * Marks page ID as a default one
     * 
     * @param string $id Page ID
     * @return boolean
     */
    public function updateDefault($id)
    {
        // Data to be updated
        $data = array(
            'default' => new RawSqlFragment('CASE WHEN id = '.$id.' THEN 1 ELSE 0 END')
        );

        return $this->db->update(self::getTableName(), $data)
                        ->execute();
    }

    /**
     * Updates whether SEO should be enabled or not
     * 
     * @param string $id Page id
     * @param string $seo Either 0 or 1
     * @return boolean
     */
    public function updateSeoById($id, $seo)
    {
        return $this->updateColumnByPk($id, 'seo', $seo);
    }

    /**
     * Filters the raw input
     * 
     * @param array|\ArrayAccess $input Raw input data
     * @param integer $page Current page number
     * @param integer $itemsPerPage Items per page to be displayed
     * @param string $sortingColumn Column name to be sorted
     * @param string $desc Whether to sort in DESC order
     * @return array
     */
    public function filter($input, $page, $itemsPerPage, $sortingColumn, $desc)
    {
        if (!$sortingColumn) {
            $sortingColumn = $this->getPk();
        }

        $db = $this->createWebPageSelect($this->getColumns())
                    // Optional attribute filters
                    ->whereEquals(
                        PageTranslationMapper::getFullColumnName('lang_id'), 
                        $this->getLangId()
                    )
                    ->andWhereLike(
                        PageTranslationMapper::getFullColumnName('name'), 
                        '%'.$input['name'].'%', true
                    )
                    ->andWhereEquals(self::getFullColumnName($this->getPk()), $input['id'], true)
                    ->andWhereEquals(self::getFullColumnName('seo'), $input['seo'], true)
                    ->orderBy(self::getFullColumnName($sortingColumn));

        if ($desc) {
            $db->desc();
        }

        return $db->paginate($page, $itemsPerPage)
                  ->queryAll();
    }

    /**
     * Fetches all pages filtered by pagination
     * 
     * @param string $page Current page id
     * @param string $itemsPerPage Per page count
     * @return array
     */
    public function fetchAllByPage($page, $itemsPerPage)
    {
        return $this->filter(new InputDecorator(), $page, $itemsPerPage, false, true);
    }

    /**
     * Fetches default page
     * 
     * @return array
     */
    public function fetchDefault()
    {
        return $this->createWebPageSelect($this->getColumns())
                    ->whereEquals(self::getFullColumnName('default'), new RawSqlFragment('1'))
                    ->andWhereEquals(PageTranslationMapper::getFullColumnName(self::PARAM_COLUMN_LANG_ID), $this->getLangId())
                    ->query();
    }

    /**
     * Fetches page data by its associated id
     * 
     * @param string $id Page id
     * @param boolean $withTranslations Whether to fetch translations
     * @return array
     */
    public function fetchById($id, $withTranslations)
    {
        return $this->findWebPage($this->getColumns(), $id, $withTranslations);
    }
}

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
            self::column('id'),
            self::column('template'),
            self::column('protected'),
            self::column('image'),
            self::column('seo'),
            self::column('default'),
            PageTranslationMapper::column('lang_id'),
            PageTranslationMapper::column('web_page_id'),
            PageTranslationMapper::column('content'),
            PageTranslationMapper::column('title'),
            PageTranslationMapper::column('name'),
            PageTranslationMapper::column('meta_description'),
            PageTranslationMapper::column('keywords'),

            // Web page meta columns
            WebPageMapper::column('slug'),
            WebPageMapper::column('controller'),
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
        $sortingColumns = array(
            'seo' => self::column('seo'),
            'name' => PageTranslationMapper::column('name')
        );

        // Current sorting column
        $sortingColumn = isset($sortingColumns[$sortingColumn]) ? $sortingColumns[$sortingColumn] : self::column($this->getPk());

        if (!$sortingColumn) {
            $sortingColumn = $this->getPk();
        }

        $db = $this->createWebPageSelect($this->getColumns())
                    // Optional attribute filters
                    ->whereEquals(
                        PageTranslationMapper::column('lang_id'), 
                        $this->getLangId()
                    )
                    ->andWhereLike(
                        PageTranslationMapper::column('name'), 
                        '%'.$input['name'].'%', true
                    )
                    ->andWhereEquals(self::column($this->getPk()), $input['id'], true)
                    ->andWhereEquals(self::column('seo'), $input['seo'], true)
                    ->orderBy($sortingColumn);

        if ($desc) {
            $db->desc();
        }

        return $db->paginate($page, $itemsPerPage)
                  ->queryAll();
    }

    /**
     * Fetches default page
     * 
     * @return array
     */
    public function fetchDefault()
    {
        return $this->createWebPageSelect($this->getColumns())
                    ->whereEquals(self::column('default'), new RawSqlFragment('1'))
                    ->andWhereEquals(PageTranslationMapper::column(self::PARAM_COLUMN_LANG_ID), $this->getLangId())
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

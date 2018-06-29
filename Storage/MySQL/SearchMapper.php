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
use Krystal\Db\Sql\QueryBuilderInterface;

final class SearchMapper extends AbstractMapper
{
    /**
     * {@inheritDoc}
     */
    public function appendQuery(QueryBuilderInterface $queryBuilder, $placeholder)
    {
        // Columns to be selected
        $columns = array(
            PageMapper::column('id'),
            PageTranslationMapper::column('web_page_id'),
            PageTranslationMapper::column('lang_id'),
            PageTranslationMapper::column('title'),
            PageTranslationMapper::column('content'),
            PageTranslationMapper::column('name')
        );

        $queryBuilder->select($columns)
                     ->from(PageMapper::getTableName())
                     // Translation relation
                     ->innerJoin(PageTranslationMapper::getTableName())
                     ->on()
                     ->equals(
                        PageMapper::column('id'),
                        PageTranslationMapper::column('id')
                     )
                     // Filtering conditions
                     ->whereEquals(PageMapper::column('seo'), '1')
                     ->andWhereEquals(PageTranslationMapper::column('lang_id'), "'{$this->getLangId()}'")
                     ->rawAnd()
                     ->openBracket()
                     // Search
                     ->like(PageTranslationMapper::column('name'), $placeholder)
                     ->rawOr()
                     ->like(PageTranslationMapper::column('content'), $placeholder)
                     ->closeBracket();
    }
}

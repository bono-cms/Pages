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
            PageMapper::getFullColumnName('id'),
            PageTranslationMapper::getFullColumnName('web_page_id'),
            PageTranslationMapper::getFullColumnName('lang_id'),
            PageTranslationMapper::getFullColumnName('title'),
            PageTranslationMapper::getFullColumnName('content'),
            PageTranslationMapper::getFullColumnName('name')
        );

        $queryBuilder->select($columns)
                     ->from(PageMapper::getTableName())
                     // Translation relation
                     ->innerJoin(PageTranslationMapper::getTableName())
                     ->on()
                     ->equals(
                        PageMapper::getFullColumnName('id'),
                        PageTranslationMapper::getFullColumnName('id')
                     )
                     // Filtering conditions
                     ->whereEquals(PageMapper::getFullColumnName('seo'), '1')
                     ->andWhereEquals(PageTranslationMapper::getFullColumnName('lang_id'), "'{$this->getLangId()}'")
                     ->rawAnd()
                     ->openBracket()
                     // Search
                     ->like(PageTranslationMapper::getFullColumnName('name'), $placeholder)
                     ->rawOr()
                     ->like(PageTranslationMapper::getFullColumnName('content'), $placeholder)
                     ->closeBracket();
    }
}

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
            PageMapper::getFullColumnName('web_page_id', PageMapper::getTranslationTable()),
            PageMapper::getFullColumnName('lang_id', PageMapper::getTranslationTable()),
            PageMapper::getFullColumnName('title', PageMapper::getTranslationTable()),
            PageMapper::getFullColumnName('content', PageMapper::getTranslationTable()),
            PageMapper::getFullColumnName('name', PageMapper::getTranslationTable())
        );

        $queryBuilder->select($columns)
                     ->from(PageMapper::getTableName())
                     // Translation relation
                     ->innerJoin(PageMapper::getTranslationTable())
                     ->on()
                     ->equals(
                        PageMapper::getFullColumnName('id'),
                        PageMapper::getFullColumnName('id', PageMapper::getTranslationTable())
                     )
                     // Filtering conditions
                     ->whereEquals(PageMapper::getFullColumnName('seo'), '1')
                     ->andWhereEquals(PageMapper::getFullColumnName('lang_id', PageMapper::getTranslationTable()), "'{$this->getLangId()}'")
                     ->rawAnd()
                     ->openBracket()
                     // Search
                     ->like(PageMapper::getFullColumnName('name', PageMapper::getTranslationTable()), $placeholder)
                     ->rawOr()
                     ->like(PageMapper::getFullColumnName('content', PageMapper::getTranslationTable()), $placeholder)
                     ->closeBracket();
    }
}

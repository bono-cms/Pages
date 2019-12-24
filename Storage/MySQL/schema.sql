
/* Pages */
DROP TABLE IF EXISTS `bono_module_pages`;
CREATE TABLE `bono_module_pages` (
    `id` INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
    `template` varchar(32) NOT NULL COMMENT 'Template override',
    `protected` varchar(1) NOT NULL COMMENT 'Whether this page is allowed to be removed in simple mode',
    `seo` varchar(1) NOT NULL COMMENT 'Whether it should be indexed in SEO',
    `default` varchar(1) NOT NULL COMMENT 'Whether this page is considered default',
    `image` varchar(30) NOT NULL COMMENT 'Attached page image'
) ENGINE = InnoDB DEFAULT CHARSET = UTF8;

DROP TABLE IF EXISTS `bono_module_pages_translations`;
CREATE TABLE `bono_module_pages_translations` (
    `id` INT NOT NULL,
    `lang_id` INT NOT NULL COMMENT 'Language identificator',
    `web_page_id` INT NOT NULL COMMENT 'Web page identificator can be found in site module',
    `title` varchar(255) NOT NULL COMMENT 'Page title',
    `name` varchar(255) NOT NULL COMMENT 'Page name',
    `content` LONGTEXT NOT NULL COMMENT 'Fits for description',
    `keywords` TEXT NOT NULL,
    `meta_description` TEXT NOT NULL COMMENT 'Meta-description for search engines',

    FOREIGN KEY (id) REFERENCES bono_module_pages(id) ON DELETE CASCADE,
    FOREIGN KEY (lang_id) REFERENCES bono_module_cms_languages(id) ON DELETE CASCADE,
    FOREIGN KEY (web_page_id) REFERENCES bono_module_cms_webpages(id) ON DELETE CASCADE
) ENGINE = InnoDB DEFAULT CHARSET = UTF8;

/* Extra fields */
DROP TABLE IF EXISTS `bono_module_pages_extra_fields_cat_rel`;
CREATE TABLE `bono_module_pages_extra_fields_cat_rel` (
    `master_id` INT NOT NULL COMMENT 'Page ID',
    `slave_id` INT NOT NULL COMMENT 'Category ID',

    FOREIGN KEY (master_id) REFERENCES bono_module_pages(id) ON DELETE CASCADE,
    FOREIGN KEY (slave_id) REFERENCES bono_module_block_categories(id) ON DELETE CASCADE
) ENGINE = InnoDB DEFAULT CHARSET = UTF8;

DROP TABLE IF EXISTS `bono_module_pages_extra_fields`;
CREATE TABLE `bono_module_pages_extra_fields` (
    `id` INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
    `entity_id` INT NOT NULL COMMENT 'Page ID',
    `field_id` INT NOT NULL COMMENT 'Related field_id in block module',
    `value` LONGTEXT NOT NULL COMMENT 'Non-translateable value',
    
    FOREIGN KEY (entity_id) REFERENCES bono_module_pages(id) ON DELETE CASCADE,
    FOREIGN KEY (field_id) REFERENCES bono_module_block_category_fields(id) ON DELETE CASCADE
) ENGINE = InnoDB DEFAULT CHARSET = UTF8;

DROP TABLE IF EXISTS `bono_module_pages_extra_fields_translations`;
CREATE TABLE `bono_module_pages_extra_fields_translations` (
    `id` INT NOT NULL,
    `lang_id` INT NOT NULL COMMENT 'Language identificator',
    `value` LONGTEXT NOT NULL,

    FOREIGN KEY (id) REFERENCES bono_module_pages_extra_fields(id) ON DELETE CASCADE,
    FOREIGN KEY (lang_id) REFERENCES bono_module_cms_languages(id) ON DELETE CASCADE
) ENGINE = InnoDB DEFAULT CHARSET = UTF8;

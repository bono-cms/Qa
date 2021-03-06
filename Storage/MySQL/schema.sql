
DROP TABLE IF EXISTS `bono_module_qa`;
CREATE TABLE `bono_module_qa` (
    `id` INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
    `lang_id` INT NOT NULL,
    `question` LONGTEXT NOT NULL,
    `answer` LONGTEXT NOT NULL,
    `questioner` varchar(254) NOT NULL,
    `answerer` varchar(254) NOT NULL,
    `published` varchar(1) NOT NULL,
    `timestamp_asked` INT(10) NOT NULL,
    `timestamp_answered` INT(10) NOT NULL,
    `ip` varchar(50) NOT NULL

    FOREIGN KEY (lang_id) REFERENCES bono_module_cms_languages(id) ON DELETE CASCADE
) DEFAULT CHARSET=UTF8 ENGINE = InnoDB;
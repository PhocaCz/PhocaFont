-- -------------------------------------------------------------------- --
-- Phoca Font manual installation                                   --
-- -------------------------------------------------------------------- --
-- See documentation on https://www.phoca.cz/                            --
--                                                                      --
-- Change all prefixes #__ to prefix which is set in your Joomla! site  --
-- (e.g. from #__phocafont to jos_phocafont)                    --
-- Run this SQL queries in your database tool, e.g. in phpMyAdmin       --
-- If you have questions, just ask in Phoca Forum                       --
-- https://www.phoca.cz/forum/                                           --
-- -------------------------------------------------------------------- --

CREATE TABLE IF NOT EXISTS `#__phocafont_font` (
  `id` int(11) NOT NULL auto_increment,
  `catid` int(11) NOT NULL default 0,
  `title` varchar(255) NOT NULL default '',
  `alias` varchar(255) NOT NULL default '',
  `xmlfile` varchar(70) NOT NULL default '',
  `regular` varchar(70) NOT NULL default '',
  `bold` varchar(70) NOT NULL default '',
  `italic` varchar(70) NOT NULL default '',
  `bolditalic` varchar(70) NOT NULL default '',
  `condensed` varchar(70) NOT NULL default '',
  `condensedbold` varchar(70) NOT NULL default '',
  `condenseditalic` varchar(70) NOT NULL default '',
  `condensedbolditalic` varchar(70) NOT NULL default '',
  `alternative` varchar(255) NOT NULL default '',
  `variant` varchar(255) NOT NULL default '',
  `subset` varchar(255) NOT NULL default '',
  `format` varchar(30) NOT NULL default '',
  `published` tinyint(1) NOT NULL default '0',
  `defaultfont` tinyint(1) NOT NULL default '0',
  `checked_out` int(11) unsigned NOT NULL default '0',
  `checked_out_time` datetime NOT NULL default '0000-00-00 00:00:00',
  `ordering` int(11) NOT NULL default '0',
  `access` tinyint(3) unsigned NOT NULL default '0',
  `params` text,
  `language` char(7) NOT NULL Default '',
  PRIMARY KEY  (`id`),
  KEY `cat_idx` (`published`,`access`),
  KEY `idx_access` (`access`),
  KEY `idx_checkout` (`checked_out`)
) CHARACTER SET `utf8`;
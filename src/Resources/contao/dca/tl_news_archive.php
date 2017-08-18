<?php

/**
 * Contao Open Source CMS
 */

/**
 * @file tl_news_archive.php
 * @author Sascha Weidner
 * @version 3.0.0
 * @package sioweb.contao.extensions.glossar
 * @copyright Sascha Weidner, Sioweb
 */

$GLOBALS['TL_DCA']['tl_news_archive']['palettes']['default'] = rtrim($GLOBALS['TL_DCA']['tl_news_archive']['palettes']['default'],';').'{glossar_legend},glossar_disallow';
$GLOBALS['TL_DCA']['tl_news_archive']['fields']['glossar_disallow'] = array(
	'label'                   => &$GLOBALS['TL_LANG']['tl_news_archive']['glossar_disallow'],
	'exclude'                 => true,
	'filter'                  => true,
	'inputType'               => 'checkbox',
	'sql'                     => "char(1) NOT NULL default ''"
);
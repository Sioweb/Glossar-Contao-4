<?php

/**
 * Contao Open Source CMS
 */

/**
 * @file tl_news.php
 * @author Sascha Weidner
 * @version 2.3
 * @package sioweb.contao.extensions.glossar
 * @copyright Sascha Weidner, Sioweb
 */

use Contao\NewsBundle\ContaoNewsBundle;

if (class_exists(ContaoNewsBundle::class)) {
	$GLOBALS['TL_DCA']['tl_news']['fields']['glossar'] = [
		'sql' => "text NULL",
	];

	$GLOBALS['TL_DCA']['tl_news']['fields']['fallback_glossar'] = [
		'sql' => "text NULL",
	];

	$GLOBALS['TL_DCA']['tl_news']['fields']['glossar_time'] = [
		'sql' => "int(10) unsigned NOT NULL default '0'",
	];
}

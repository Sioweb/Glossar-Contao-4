<?php

/**
 * Contao Open Source CMS
 */

/**
 * @file tl_news_archive.php
 * @author Sascha Weidner
 * @version 2.3
 * @package sioweb.contao.extensions.glossar
 * @copyright Sascha Weidner, Sioweb
 */

use Contao\NewsBundle\ContaoNewsBundle;

if (class_exists(ContaoNewsBundle::class)) {
	/**
	 * Extend default palette
	 */
	Contao\CoreBundle\DataContainer\PaletteManipulator::create()
		->addLegend('glossar_legend', 'comments_legend', Contao\CoreBundle\DataContainer\PaletteManipulator::POSITION_AFTER)
		->addField(['glossar_disallow'], 'glossar_legend', Contao\CoreBundle\DataContainer\PaletteManipulator::POSITION_APPEND)
		->applyToPalette('default', 'tl_news_archive');

	$GLOBALS['TL_DCA']['tl_news_archive']['fields']['glossar_disallow'] = [
		'label'				=> &$GLOBALS['TL_LANG']['tl_news_archive']['glossar_disallow'],
		'exclude'			=> true,
		'filter'			=> true,
		'inputType'			=> 'checkbox',
		'sql'				=> "char(1) NOT NULL default ''",
	];
}

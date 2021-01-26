<?php

/**
 * Contao Open Source CMS
 */

/**
 * @file tl_faq_category.php
 * @author Sascha Weidner
 * @version 2.3
 * @package sioweb.contao.extensions.glossar
 * @copyright Sascha Weidner, Sioweb
 */

use Contao\FaqBundle\ContaoFaqBundle;

if (class_exists(ContaoFaqBundle::class)) {
	/**
	 * Extend default palette
	 */
	Contao\CoreBundle\DataContainer\PaletteManipulator::create()
		->addLegend('glossar_legend', 'comments_legend', Contao\CoreBundle\DataContainer\PaletteManipulator::POSITION_AFTER)
		->addField(['glossar_disallow'], 'glossar_legend', Contao\CoreBundle\DataContainer\PaletteManipulator::POSITION_APPEND)
		->applyToPalette('default', 'tl_faq_category');

	$GLOBALS['TL_DCA']['tl_faq_category']['fields']['glossar_disallow'] = [
		'label'				=> &$GLOBALS['TL_LANG']['tl_faq_category']['glossar_disallow'],
		'exclude'			=> true,
		'filter'			=> true,
		'inputType'			=> 'checkbox',
		'sql'				=> "char(1) NOT NULL default ''",
	];
}

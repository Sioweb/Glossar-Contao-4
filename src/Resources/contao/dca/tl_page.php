<?php

/**
 * Contao Open Source CMS
 */

/**
 * @file tl_page.php
 * @author Sascha Weidner
 * @version 3.0.0
 * @package sioweb.contao.extensions.glossar
 * @copyright Sascha Weidner, Sioweb
 */


/**
 * Extend default palette
 */
Contao\CoreBundle\DataContainer\PaletteManipulator::create()
    ->addLegend('glossar_legend', 'publish_legend', Contao\CoreBundle\DataContainer\PaletteManipulator::POSITION_AFTER)
    ->addField(['disableGlossar', 'disableGlossarCloud', 'glossar_no_fallback', 'glossar_max_replacements'], 'glossar_legend', Contao\CoreBundle\DataContainer\PaletteManipulator::POSITION_APPEND)
    ->applyToPalette('regular', 'tl_page');

$GLOBALS['TL_DCA']['tl_page']['fields']['disableGlossar'] = [
    'label'				=> &$GLOBALS['TL_LANG']['tl_page']['disableGlossar'],
    'exclude'			=> true,
    'inputType'			=> 'checkbox',
    'sql'				=> "char(1) NOT NULL default ''",
];

$GLOBALS['TL_DCA']['tl_page']['fields']['glossar_max_replacements'] = [
    'label'				=> &$GLOBALS['TL_LANG']['tl_page']['glossar_max_replacements'],
    'exclude'			=> true,
    'inputType'			=> 'text',
    'eval'				=> ['maxlength' => 255, 'tl_class' => 'long clr'],
    'sql'				=> "int(10) NOT NULL default '0'",
];

$GLOBALS['TL_DCA']['tl_page']['fields']['disableGlossarCloud'] = [
    'label'				=> &$GLOBALS['TL_LANG']['tl_page']['disableGlossarCloud'],
    'exclude'			=> true,
    'inputType'			=> 'checkbox',
    'sql'				=> "char(1) NOT NULL default ''",
];

$GLOBALS['TL_DCA']['tl_page']['fields']['glossar_no_fallback'] = [
    'label'				=> &$GLOBALS['TL_LANG']['tl_page']['glossar_no_fallback'],
    'exclude'			=> true,
    'inputType'			=> 'checkbox',
    'sql'				=> "char(1) NOT NULL default ''",
];

$GLOBALS['TL_DCA']['tl_page']['fields']['glossar'] = [
    'sql'				=> "text NULL",
];

$GLOBALS['TL_DCA']['tl_page']['fields']['fallback_glossar'] = [
    'sql'				=> "text NULL",
];

$GLOBALS['TL_DCA']['tl_page']['fields']['glossar_time'] = [
    'sql'				=> "int(10) unsigned NOT NULL default '0'",
];

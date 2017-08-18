<?php

/**
 * Contao Open Source CMS
 */

/**
 * @file tl_calendar.php
 * @author Sascha Weidner
 * @version 3.0.0
 * @package sioweb.contao.extensions.glossar
 * @copyright Sascha Weidner, Sioweb
 */

$Semikolon = substr($GLOBALS['TL_DCA']['tl_calendar']['palettes']['default'],-1);
$GLOBALS['TL_DCA']['tl_calendar']['palettes']['default'] .= ($Semikolon===';'?'':';').'{glossar_legend},glossar_disallow';
$GLOBALS['TL_DCA']['tl_calendar']['fields']['glossar_disallow'] = array(
	'label'                   => &$GLOBALS['TL_LANG']['tl_calendar']['glossar_disallow'],
	'exclude'                 => true,
	'filter'                  => true,
	'inputType'               => 'checkbox',
	'sql'                     => "char(1) NOT NULL default ''"
);
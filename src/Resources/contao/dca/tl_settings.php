<?php

/**
 * Contao Open Source CMS
 */

/**
 * @file tl_settings.php
 * @author Sascha Weidner
 * @version 3.0.0
 * @package sioweb.contao.extensions.glossar
 * @copyright Sascha Weidner, Sioweb
 */


$GLOBALS['TL_DCA']['tl_settings']['subpalettes']['enableGlossar'] = '';
$GLOBALS['TL_DCA']['tl_settings']['palettes']['__selector__'][] = 'enableGlossar';

Contao\CoreBundle\DataContainer\PaletteManipulator::create()
	->addLegend('glossar_legend', 'chmod_legend', Contao\CoreBundle\DataContainer\PaletteManipulator::POSITION_AFTER)
	->addField(['enableGlossar'], 'glossar_legend', Contao\CoreBundle\DataContainer\PaletteManipulator::POSITION_APPEND)
	->applyToPalette('default', 'tl_settings');

Contao\CoreBundle\DataContainer\PaletteManipulator::create()
	->addField(['glossarPurgable', 'disableToolTips', 'acceptTeasersAsContent', 'termAsHeadline', 'noPlural', 'glossarIncludeUnsearchable', 'activateGlossarTags', 'disableGlossarCache', 'glossar_no_fallback', 'glossar_archive', 'strictSearch', 'glossarMaxWidth', 'glossarMaxHeight', 'ignoreInTags', 'illegalChars', 'jumpToGlossar', 'glossar_max_replacements'], 'glossar_legend', Contao\CoreBundle\DataContainer\PaletteManipulator::POSITION_APPEND)
	->applyToSubpalette('enableGlossar', 'tl_settings');

$GLOBALS['TL_DCA']['tl_settings']['fields']['ignoreInTags'] = [
	'label'				=> &$GLOBALS['TL_LANG']['tl_settings']['ignoreInTags'],
	'exclude'			=> true,
	'inputType'			=> 'text',
	'eval'				=> ['maxlength' => 255, 'tl_class' => 'w50 clr long'],
	'sql'				=> "text NULL",
];

$GLOBALS['TL_DCA']['tl_settings']['fields']['illegalChars'] = [
	'label'				=> &$GLOBALS['TL_LANG']['tl_settings']['illegalChars'],
	'exclude'			=> true,
	'inputType'			=> 'text',
	'eval'				=> ['maxlength' => 255, 'tl_class' => 'w50 clr long', 'allowHtml' => true, 'decodeEntities' => false],
	'sql'				=> "text NULL",
];

$GLOBALS['TL_DCA']['tl_settings']['fields']['jumpToGlossar'] = [
	'label'				=> &$GLOBALS['TL_LANG']['tl_settings']['jumpToGlossar'],
	'exclude'			=> true,
	'inputType'			=> 'pageTree',
	'foreignKey'		=> 'tl_page.title',
	'eval'				=> ['fieldType' => 'radio', 'tl_class' => 'w50 clr'],
	'sql'				=> "int(10) unsigned NOT NULL default '0'",
	'relation'			=> ['type' => 'belongsTo', 'load' => 'lazy'],
];

$GLOBALS['TL_DCA']['tl_settings']['fields']['glossarPurgable'] = [
	'label'				=> &$GLOBALS['TL_LANG']['tl_settings']['glossarPurgable'],
	'exclude'			=> true,
	'inputType'			=> 'checkbox',
	'eval'				=> ['tl_class' => 'w50 clr'],
	'sql'				=> "char(1) NOT NULL default ''",
];

$GLOBALS['TL_DCA']['tl_settings']['fields']['disableToolTips'] = [
	'label'				=> &$GLOBALS['TL_LANG']['tl_settings']['disableToolTips'],
	'exclude'			=> true,
	'inputType'			=> 'checkbox',
	'eval'				=> ['tl_class' => 'w50 clr'],
	'sql'				=> "char(1) NOT NULL default ''",
];

$GLOBALS['TL_DCA']['tl_settings']['fields']['acceptTeasersAsContent'] = [
	'label'				=> &$GLOBALS['TL_LANG']['tl_settings']['acceptTeasersAsContent'],
	'exclude'			=> true,
	'inputType'			=> 'checkbox',
	'eval'				=> ['tl_class' => 'w50 clr'],
	'sql'				=> "char(1) NOT NULL default ''",
];

$GLOBALS['TL_DCA']['tl_settings']['fields']['noPlural'] = [
	'label'				=> &$GLOBALS['TL_LANG']['tl_settings']['noPlural'],
	'exclude'			=> true,
	'inputType'			=> 'checkbox',
	'eval'				=> ['tl_class' => 'w50 clr'],
	'sql'				=> "char(1) NOT NULL default ''",
];

$GLOBALS['TL_DCA']['tl_settings']['fields']['termAsHeadline'] = [
	'label'				=> &$GLOBALS['TL_LANG']['tl_settings']['termAsHeadline'],
	'exclude'			=> true,
	'inputType'			=> 'checkbox',
	'eval'				=> ['tl_class' => 'w50 clr'],
	'sql'				=> "char(1) NOT NULL default ''",
];

$GLOBALS['TL_DCA']['tl_settings']['fields']['enableGlossar'] = [
	'label'				=> &$GLOBALS['TL_LANG']['tl_settings']['enableGlossar'],
	'exclude'			=> true,
	'inputType'			=> 'checkbox',
	'eval'				=> ['tl_class' => 'w50 clr'],
	'eval'				=> ['submitOnChange' => true],
	'sql'				=> "char(1) NOT NULL default ''",
];

$GLOBALS['TL_DCA']['tl_settings']['fields']['glossarIncludeUnsearchable'] = [
	'label'				=> &$GLOBALS['TL_LANG']['tl_settings']['glossarIncludeUnsearchable'],
	'exclude'			=> true,
	'inputType'			=> 'checkbox',
	'eval'				=> ['tl_class' => 'w50 clr'],
	'sql'				=> "char(1) NOT NULL default ''",
];

$GLOBALS['TL_DCA']['tl_settings']['fields']['activateGlossarTags'] = [
	'label'				=> &$GLOBALS['TL_LANG']['tl_settings']['activateGlossarTags'],
	'exclude'			=> true,
	'inputType'			=> 'checkbox',
	'eval'				=> ['tl_class' => 'w50 clr'],
	'sql'				=> "char(1) NOT NULL default ''",
];

$GLOBALS['TL_DCA']['tl_settings']['fields']['glossar_no_fallback'] = [
	'label'				=> &$GLOBALS['TL_LANG']['tl_settings']['glossar_no_fallback'],
	'exclude'			=> true,
	'inputType'			=> 'checkbox',
	'eval'				=> ['tl_class' => 'w50 clr'],
	'sql'				=> "char(1) NOT NULL default ''",
];

$GLOBALS['TL_DCA']['tl_settings']['fields']['strictSearch'] = [
	'label'				=> &$GLOBALS['TL_LANG']['tl_settings']['strictSearch'],
	'default'			=> 'alias',
	'inputType'			=> 'select',
	'options'			=> array_keys((array)$GLOBALS['TL_LANG']['glossar']['strictSearch']),
	'reference'			=> &$GLOBALS['TL_LANG']['glossar']['strictSearch'],
	'eval'				=> ['tl_class' => 'w50 clr long', 'includeBlankOption' => true],
	'sql'				=> "varchar(20) NOT NULL default ''",
];

$GLOBALS['TL_DCA']['tl_settings']['fields']['disableGlossarCache'] = [
	'label'				=> &$GLOBALS['TL_LANG']['tl_settings']['disableGlossarCache'],
	'exclude'			=> true,
	'inputType'			=> 'checkbox',
	'eval'				=> ['tl_class' => 'w50 clr', 'submitOnChange' => true],
	'sql'				=> "char(1) NOT NULL default ''",
];

$GLOBALS['TL_DCA']['tl_settings']['fields']['glossar_archive'] = [
	'label'				=> &$GLOBALS['TL_LANG']['tl_settings']['glossar_archive'],
	'inputType'			=> 'checkboxWizard',
	'options_callback'	=> function () {
		return array_keys(System::getContainer()->get('sioweb.glossar.get.glossar_pages')->run());
	},
	'reference'			=> &$GLOBALS['TL_LANG']['glossar']['glossar_archives'],
	'eval'				=> ['tl_class' => 'w50 clr long', 'multiple' => true],
	'sql'				=> "TEXT NULL",
];

$GLOBALS['TL_DCA']['tl_settings']['fields']['glossarMaxWidth'] = [
	'label'				=> &$GLOBALS['TL_LANG']['tl_settings']['glossarMaxWidth'],
	'exclude'			=> true,
	'inputType'			=> 'text',/*inputUnit*/
	'options'			=> ['px', 'em', '%', 'rem'],
	'eval'				=> ['rgxp' => 'natural', 'nospace' => true, 'tl_class' => 'w50'],
	'sql'				=> "varchar(255) NOT NULL default ''",
];

$GLOBALS['TL_DCA']['tl_settings']['fields']['glossarMaxHeight'] = [
	'label'				=> &$GLOBALS['TL_LANG']['tl_settings']['glossarMaxHeight'],
	'exclude'			=> true,
	'inputType'			=> 'text',/*inputUnit*/
	'options'			=> ['px', 'em', '%', 'rem'],
	'eval'				=> ['rgxp' => 'natural', 'nospace' => true, 'tl_class' => 'w50'],
	'sql'				=> "varchar(255) NOT NULL default ''",
];

$GLOBALS['TL_DCA']['tl_settings']['fields']['glossar_max_replacements'] = [
	'label'				=> &$GLOBALS['TL_LANG']['tl_settings']['glossar_max_replacements'],
	'exclude'			=> true,
	'inputType'			=> 'text',
	'eval'				=> ['maxlength' => 255, 'tl_class' => 'long clr'],
	'sql'				=> "varchar(255) NOT NULL default '0'",
];

<?php

/*
 * This file is part of Contao.
 *
 * (c) Leo Feyer
 *
 * @license LGPL-3.0-or-later
 */


/**
 * Extend the default palettes
 */
Contao\CoreBundle\DataContainer\PaletteManipulator::create()
	->addLegend('glossar_legend', 'amg_legend', Contao\CoreBundle\DataContainer\PaletteManipulator::POSITION_BEFORE)
	->addField(['glossar', 'glossarp'], 'glossar_legend', Contao\CoreBundle\DataContainer\PaletteManipulator::POSITION_APPEND)
	->applyToPalette('extend', 'tl_user')
	->applyToPalette('custom', 'tl_user');


/**
 * Add fields to tl_user_group
 */
$GLOBALS['TL_DCA']['tl_user']['fields']['glossar'] = [
	'label'                   => &$GLOBALS['TL_LANG']['tl_user']['glossar'],
	'exclude'                 => true,
	'inputType'               => 'checkbox',
	'foreignKey'              => 'tl_glossar.title',
	'eval'                    => ['multiple' => true],
	'sql'                     => "blob NULL",
];

$GLOBALS['TL_DCA']['tl_user']['fields']['glossarp'] = [
	'label'                   => &$GLOBALS['TL_LANG']['tl_user']['glossarp'],
	'exclude'                 => true,
	'inputType'               => 'checkbox',
	'options'                 => ['create', 'delete'],
	'reference'               => &$GLOBALS['TL_LANG']['MSC'],
	'eval'                    => ['multiple' => true],
	'sql'                     => "blob NULL",
];

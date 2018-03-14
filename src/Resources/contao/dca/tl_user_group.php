<?php

/**
 * Contao Open Source CMS
 */


/**
 * Extend default palette
 */
Contao\CoreBundle\DataContainer\PaletteManipulator::create()
    ->addLegend('glossar_legend', 'news_legend', Contao\CoreBundle\DataContainer\PaletteManipulator::POSITION_BEFORE)
    ->addField(array('glossar','glossarp'), 'glossar_legend', Contao\CoreBundle\DataContainer\PaletteManipulator::POSITION_APPEND)
    ->applyToPalette('default', 'tl_user_group')
;

/**
 * Add fields to tl_user_group
 */
$GLOBALS['TL_DCA']['tl_user_group']['fields']['glossar'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_user']['glossar'],
	'exclude'                 => true,
	'inputType'               => 'checkbox',
	'foreignKey'              => 'tl_glossar.title',
	'eval'                    => array('multiple'=>true),
	'sql'                     => "blob NULL"
);


/**
 * Add fields to tl_user_group
 */
$GLOBALS['TL_DCA']['tl_user_group']['fields']['glossarp'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_user']['glossarp'],
	'exclude'                 => true,
	'inputType'               => 'checkbox',
	'options'                 => array('create', 'delete'),
	'reference'               => &$GLOBALS['TL_LANG']['MSC'],
	'eval'                    => array('multiple'=>true),
	'sql'                     => "blob NULL"
);

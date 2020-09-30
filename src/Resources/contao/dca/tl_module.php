<?php

/**
 * Contao Open Source CMS
 */

/**
 * @file tl_module.php
 * @author Sascha Weidner
 * @version 2.3
 * @package sioweb.contao.extensions.glossar
 * @copyright Sascha Weidner, Sioweb
 */

$GLOBALS['TL_DCA']['tl_module']['palettes']['glossar_pagination']    = '{title_legend},name,headline,type;{glossar_legend},glossar;{alphapagination_legend:hide},addAlphaPagination;{protected_legend:hide},protected;{expert_legend:hide},guests,cssID,space';
$GLOBALS['TL_DCA']['tl_module']['palettes']['glossar_cloud']         = '{title_legend},name,headline,type;{glossar_legend},glossar,glossar_items,glossar_max_level,glossar_disable_domains;{protected_legend:hide},protected;{expert_legend:hide},guests,cssID,space';

$GLOBALS['TL_DCA']['tl_module']['palettes']['__selector__'][] = 'addAlphaPagination';
$GLOBALS['TL_DCA']['tl_module']['subpalettes']['addAlphaPagination'] = 'addNumericPagination,showAfterChoose,addOnlyTrueLinks,paginationPosition';

$GLOBALS['TL_DCA']['tl_module']['fields']['glossar'] = [
	'label'					=> &$GLOBALS['TL_LANG']['tl_module']['glossar'],
	'inputType'				=> 'select',
	'foreignKey'			=> 'tl_glossar.title',
	'eval'					=> ['tl_class' => 'clr', 'includeBlankOption' => true],
	'sql'					=> "varchar(20) NOT NULL default ''",
];

$GLOBALS['TL_DCA']['tl_module']['fields']['glossar_items'] = [
	'label'					=> &$GLOBALS['TL_LANG']['tl_module']['glossar_items'],
	'inputType'				=> 'text',
	'eval'					=> ['tl_class' => 'w50'],
	'sql'					=> "int(11) NOT NULL default '0'",
];

$GLOBALS['TL_DCA']['tl_module']['fields']['glossar_max_level'] = [
	'label'					=> &$GLOBALS['TL_LANG']['tl_module']['glossar_max_level'],
	'default'				=> 3,
	'inputType'				=> 'text',
	'eval'					=> ['tl_class' => 'w50'],
	'sql'					=> "int(11) NOT NULL default '3'",
];

$GLOBALS['TL_DCA']['tl_module']['fields']['glossar_disable_domains'] = [
	'label'					=> &$GLOBALS['TL_LANG']['tl_module']['glossar_disable_domains'],
	'inputType'				=> 'checkboxWizard',
	'options_callback'		=> ['tl_glossar_module', 'loadRootPages'],
	'eval'					=> ['tl_class' => 'clr long', 'multiple' => true],
	'sql'					=> "text NULL",
];

$GLOBALS['TL_DCA']['tl_module']['fields']['sortGlossarBy'] = [
	'label'					=> &$GLOBALS['TL_LANG']['tl_module']['sortGlossarBy'],
	'default'				=> 'alias',
	'inputType'				=> 'select',
	'options'				=> ['id', 'id_desc', 'date', 'date_desc', 'alias', 'alias_desc'],
	'reference'				=> &$GLOBALS['glossar']['sortGlossarBy'],
	'eval'					=> ['tl_class' => 'w50'],
	'sql'					=> "varchar(20) NOT NULL default ''",
];

$GLOBALS['TL_DCA']['tl_module']['fields']['addOnlyTrueLinks'] = [
	'label'					=> &$GLOBALS['TL_LANG']['tl_module']['addOnlyTrueLinks'],
	'exclude'				=> true,
	'inputType'				=> 'checkbox',
	'eval'					=> ['submitOnChange' => true],
	'sql'					=> "char(1) NOT NULL default ''",
];

$GLOBALS['TL_DCA']['tl_module']['fields']['addAlphaPagination'] = [
	'label'					=> &$GLOBALS['TL_LANG']['tl_module']['addAlphaPagination'],
	'exclude'				=> true,
	'inputType'				=> 'checkbox',
	'eval'					=> ['submitOnChange' => true],
	'sql'					=> "char(1) NOT NULL default ''",
];

$GLOBALS['TL_DCA']['tl_module']['fields']['addNumericPagination'] = [
	'label'					=> &$GLOBALS['TL_LANG']['tl_module']['addNumericPagination'],
	'exclude'				=> true,
	'inputType'				=> 'checkbox',
	'sql'					=> "char(1) NOT NULL default ''",
];

$GLOBALS['TL_DCA']['tl_module']['fields']['paginationPosition'] = [
	'label'					=> &$GLOBALS['TL_LANG']['tl_module']['paginationPosition'],
	'default'				=> 'after',
	'inputType'				=> 'select',
	'options'				=> array_keys((array)$GLOBALS['TL_LANG']['glossar']['paginationPositions']),
	'reference'				=> &$GLOBALS['TL_LANG']['glossar']['paginationPositions'],
	'eval'					=> ['tl_class' => 'w50 clr'],
	'sql'					=> "varchar(20) NOT NULL default 'after'",
];

$GLOBALS['TL_DCA']['tl_module']['fields']['paginationPosition'] = [
	'label'					=> &$GLOBALS['TL_LANG']['tl_module']['paginationPosition'],
	'default'				=> 'after',
	'inputType'				=> 'select',
	'options'				=> array_keys((array)$GLOBALS['TL_LANG']['glossar']['paginationPositions']),
	'reference'				=> &$GLOBALS['TL_LANG']['glossar']['paginationPositions'],
	'eval'					=> ['tl_class' => 'w50 clr'],
	'sql'					=> "varchar(20) NOT NULL default 'after'",
];

class tl_glossar_module extends Backend
{
	public function loadRootPages()
	{
		$Page = \PageModel::findByType('root');
		if (empty($Page)) {
			return [];
		}

		$arrPages = [];
		while ($Page->next()) {
			$arrPages[$Page->id] = $Page->dns . ' <span style="color: #ccc;font-size:10px;display:inline-block;">[' . $Page->title . ']</span>';
		}
		return $arrPages;
	}
}

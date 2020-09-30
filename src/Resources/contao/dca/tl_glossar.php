<?php

/**
 * Contao Open Source CMS
 */

/**
 * @file tl_glossar.php
 * @author Sascha Weidner
 * @version 2.3
 * @package sioweb.contao.extensions.glossar
 * @copyright Sascha Weidner, Sioweb
 */

$GLOBALS['TL_DCA']['tl_glossar'] = [

	// Config
	'config' => [
		'dataContainer'				=> 'Table',
		'ctable'					=> ['tl_sw_glossar'],
		'switchToEdit'				=> true,
		'enableVersioning'			=> true,
	],

	// List
	'list' => [
		'sorting' => [
			'mode'					=> 1,
			'fields'				=> ['title'],
			'flag'					=> 1,
			'panelLayout'			=> 'sort,search,limit'
		],
		'label' => [
			'fields'				=> ['title'],
			'format'				=> '%s',
		],
		'global_operations' => [
			'all' => [
				'label'				=> &$GLOBALS['TL_LANG']['MSC']['all'],
				'href'				=> 'act=select',
				'class'				=> 'header_edit_all',
				'attributes'		=> 'onclick="Backend.getScrollOffset()" accesskey="e"'
			],
			'import' => [
				'label'				=> &$GLOBALS['TL_LANG']['MSC']['import'],
				'href'				=> 'key=importGlossar',
				'class'				=> 'header_edit_all',
			],
			'export' => [
				'label'				=> &$GLOBALS['TL_LANG']['MSC']['export'],
				'href'				=> 'key=exportGlossar',
				'class'				=> 'header_edit_all',
			],
		],
		'operations' => [
			'edit' => [
				'label'				=> &$GLOBALS['TL_LANG']['tl_glossar']['edit'],
				'href'				=> 'table=tl_sw_glossar',
				'icon'				=> 'edit.svg'
			],
			'editheader' => [
				'label'				=> &$GLOBALS['TL_LANG']['tl_glossar']['editheader'],
				'href'				=> 'act=edit',
				'icon'				=> 'header.svg',
				'button_callback'	=> ['sioweb.glossar.dca.glossar', 'editHeader'],
			],
			'copy' => [
				'label'				=> &$GLOBALS['TL_LANG']['tl_glossar']['copy'],
				'href'				=> 'act=copy',
				'icon'				=> 'copy.svg',
				'button_callback'	=> ['sioweb.glossar.dca.glossar', 'copyArchive'],
			],
			'delete' => [
				'label'				=> &$GLOBALS['TL_LANG']['tl_glossar']['delete'],
				'href'				=> 'act=delete',
				'icon'				=> 'delete.svg',
				'attributes'		=> 'onclick="if(!confirm(\'' . $GLOBALS['TL_LANG']['MSC']['deleteConfirm'] . '\'))return false;Backend.getScrollOffset()"',
				'button_callback'	=> ['sioweb.glossar.dca.glossar', 'deleteArchive'],
			],
			'show' => [
				'label'				=> &$GLOBALS['TL_LANG']['tl_glossar']['show'],
				'href'				=> 'act=show',
				'icon'				=> 'show.svg'
			],
			'export' => [
				'label'				=> &$GLOBALS['TL_LANG']['MSC']['export'],
				'href'				=> 'key=exportTerms',
				'icon'				=> 'theme_export.svg',
				'class'				=> 'header_edit_all',
			]
		]
	],

	// Palettes
	'palettes' => [
		'__selector__'				=> ['allowComments', 'seo'],
		'default'					=> '{title_legend},title,alias,language,fallback,allowComments;{seo_legend},seo',
	],

	// Subpalettes
	'subpalettes' => [
		'allowComments'				=> 'notify,sortOrder,perPage,moderate,bbcode,requireLogin,disableCaptcha',
		'seo'						=> 'term_in_title_tag,term_description_tag,term_in_title_str_tag',
	],

	// Fields
	'fields' => [
		'id' => [
			'foreignKey'			=> 'tl_glossar.pid',
			'relation'				=> ['type' => 'belongsToMany', 'load' => 'eager', 'field' => 'pid'],
		],
		'title' => [
			'label'					=> &$GLOBALS['TL_LANG']['tl_glossar']['title'],
			'exclude'				=> true,
			'inputType'				=> 'text',
			'eval'					=> ['mandatory' => true, 'maxlength' => 255, 'tl_class' => 'w50'],
		],
		'alias' => [
			'label'					=> &$GLOBALS['TL_LANG']['tl_glossar']['alias'],
			'exclude'				=> true,
			'inputType'				=> 'text',
			'search'				=> true,
			'eval'					=> ['rgxp' => 'alias', 'doNotCopy' => true, 'maxlength' => 128, 'tl_class' => 'w50'],
			'save_callback' => [
				['sioweb.glossar.dca.glossar', 'generateAlias'],
			]
		],
		'language' => [
			'label'					=> &$GLOBALS['TL_LANG']['tl_glossar']['language'],
			'exclude'				=> true,
			'search'				=> true,
			'inputType'				=> 'text',
			'eval'					=> ['mandatory' => true, 'maxlength' => 255, 'tl_class' => 'w50'],
		],
		'fallback' => [
			'label'					=> &$GLOBALS['TL_LANG']['tl_glossar']['fallback'],
			'exclude'				=> true,
			'inputType'				=> 'checkbox',
			'eval'					=> ['doNotCopy' => true, 'tl_class' => 'w50 m12'],
			'save_callback' => [
				['sioweb.glossar.dca.glossar', 'checkFallback'],
			],
		],
		'allowComments' => [
			'label'					=> &$GLOBALS['TL_LANG']['tl_glossar']['allowComments'],
			'exclude'				=> true,
			'filter'				=> true,
			'inputType'				=> 'checkbox',
			'eval'					=> ['submitOnChange' => true, 'tl_class' => 'w50'],
		],
		'notify' => [
			'label'					=> &$GLOBALS['TL_LANG']['tl_glossar']['notify'],
			'default'				=> 'notify_admin',
			'exclude'				=> true,
			'inputType'				=> 'select',
			'options'				=> ['notify_admin', 'notify_author', 'notify_both'],
			'reference'				=> &$GLOBALS['TL_LANG']['tl_glossar'],
		],
		'sortOrder' => [
			'label'					=> &$GLOBALS['TL_LANG']['tl_glossar']['sortOrder'],
			'default'				=> 'ascending',
			'exclude'				=> true,
			'inputType'				=> 'select',
			'options'				=> ['ascending', 'descending'],
			'reference'				=> &$GLOBALS['TL_LANG']['MSC'],
			'eval'					=> ['tl_class' => 'w50'],
		],
		'perPage' => [
			'label'					=> &$GLOBALS['TL_LANG']['tl_glossar']['perPage'],
			'exclude'				=> true,
			'inputType'				=> 'text',
			'eval'					=> ['rgxp' => 'natural', 'tl_class' => 'w50'],
		],
		'moderate' => [
			'label'					=> &$GLOBALS['TL_LANG']['tl_glossar']['moderate'],
			'exclude'				=> true,
			'inputType'				=> 'checkbox',
			'eval'					=> ['tl_class' => 'w50'],
		],
		'bbcode' => [
			'label'					=> &$GLOBALS['TL_LANG']['tl_glossar']['bbcode'],
			'exclude'				=> true,
			'inputType'				=> 'checkbox',
			'eval'					=> ['tl_class' => 'w50'],
		],
		'requireLogin' => [
			'label'					=> &$GLOBALS['TL_LANG']['tl_glossar']['requireLogin'],
			'exclude'				=> true,
			'inputType'				=> 'checkbox',
			'eval'					=> ['tl_class' => 'w50'],
		],
		'disableCaptcha' => [
			'label'					=> &$GLOBALS['TL_LANG']['tl_glossar']['disableCaptcha'],
			'exclude'				=> true,
			'inputType'				=> 'checkbox',
			'eval'					=> ['tl_class' => 'w50'],
		],
		'seo' => [
			'label'					=> &$GLOBALS['TL_LANG']['tl_glossar']['seo'],
			'exclude'				=> true,
			'filter'				=> true,
			'sorting'				=> true,
			'inputType'				=> 'checkbox',
			'eval'					=> ['submitOnChange' => true],
		],
		'term_in_title_tag' => [
			'label'					=> &$GLOBALS['TL_LANG']['tl_glossar']['term_in_title_tag'],
			'exclude'				=> true,
			'inputType'				=> 'checkbox',
			'eval'					=> ['tl_class' => 'w50 clr'],
		],
		'term_in_title_str_tag' => [
			'label'					=> &$GLOBALS['TL_LANG']['tl_glossar']['term_in_title_str_tag'],
			'exclude'				=> true,
			'inputType'				=> 'text',
			'eval'					=> ['maxlength' => 255, 'tl_class' => 'w50 clr', 'gsIgnore' => true],
		],
		'replace_pageTitle' => [
			'label'					=> &$GLOBALS['TL_LANG']['tl_glossar']['replace_pageTitle'],
			'exclude'				=> true,
			'inputType'				=> 'checkbox',
			'eval'					=> ['tl_class' => 'w50 clr'],
		],
		'term_description_tag' => [
			'label'					=> &$GLOBALS['TL_LANG']['tl_glossar']['term_description_tag'],
			'exclude'				=> true,
			'inputType'				=> 'text',
			'eval'					=> ['maxlength' => 255, 'tl_class' => 'long clr', 'gsIgnore' => true],
		],
		'canonicalType' => [],
		'canonicalJumpTo' => [],
		'canonicalWebsite'  => [],
	]
];

<?php

/**
 * Contao Open Source CMS
 */

/**
 * @file tl_glossar_log.php
 * @author Sascha Weidner
 * @version 2.3
 * @package sioweb.contao.extensions.glossar
 * @copyright Sascha Weidner, Sioweb
 */

$GLOBALS['TL_DCA']['tl_glossar_log'] = [

	// Config
	'config' => [
		'dataContainer'					=> 'Table',
		'switchToEdit'					=> true,
		'enableVersioning'				=> true,
	],

	// List
	'list' => [
		'sorting' => [
			'mode'						=> 4,
			'fields'					=> ['type,title'],
			'headerFields'				=> ['title','language','tstamp'],
			'child_record_callback'   	=> ['tl_glossar_log', 'listTerms'],
			'panelLayout'				=> 'filter;sort,search,limit',
			'child_record_class'		=> 'no_padding',
		],
		'global_operations' => [
			'all' => [
				'label'					=> &$GLOBALS['TL_LANG']['MSC']['all'],
				'href'					=> 'act=select',
				'class'					=> 'header_edit_all',
				'attributes'			=> 'onclick="Backend.getScrollOffset()" accesskey="e"',
			],
		],
		'operations' => [
			'edit' => [
				'label'					=> &$GLOBALS['TL_LANG']['tl_glossar_log']['edit'],
				'href'					=> 'act=edit',
				'icon'					=> 'edit.gif',
			],
			'delete' => [
				'label'					=> &$GLOBALS['TL_LANG']['tl_glossar_log']['delete'],
				'href'					=> 'act=delete',
				'icon'					=> 'delete.gif',
				'attributes'			=> 'onclick="if(!confirm(\'' . $GLOBALS['TL_LANG']['MSC']['deleteConfirm'] . '\'))return false;Backend.getScrollOffset()"',
			],
		]
	],

  	// Palettes
  	'palettes' => [
		'__selector__'					=> ['type'],
		'default'						=> '{title_legend},user,action',
  	],

	// Fields
	'fields' => [
		'user' => [
			'label'						=> &$GLOBALS['TL_LANG']['tl_glossar_log']['user'],
			'exclude'					=> true,
			'inputType'					=> 'text',
			'eval'						=> ['mandatory' => true, 'maxlength' => 255, 'tl_class' => 'w50'],
		],
		'pid' => [
			'label'						=> &$GLOBALS['TL_LANG']['tl_glossar_log']['term'],
			'exclude'					=> true,
			'inputType'					=> 'text',
			'foreignKey'				=> 'tl_sw_glossar.title',
			'eval'						=> ['mandatory' => true, 'maxlength' => 255, 'tl_class' => 'w50'],
			'relation'					=> ['type' => 'belongsTo', 'load' => 'eager'],
		],
		'page' => [
			'label'						=> &$GLOBALS['TL_LANG']['tl_glossar_log']['page'],
			'exclude'					=> true,
			'inputType'					=> 'text',
			'eval'						=> ['mandatory' => true, 'maxlength' => 255, 'tl_class' => 'w50'],
		],
		'host' => [
			'label'						=> &$GLOBALS['TL_LANG']['tl_glossar_log']['host'],
			'exclude'					=> true,
			'inputType'					=> 'text',
			'eval'						=> ['mandatory' => true, 'maxlength' => 255, 'tl_class' => 'w50'],
		],
		'language' => [
			'label'						=> &$GLOBALS['TL_LANG']['tl_glossar_log']['language'],
			'exclude'					=> true,
			'inputType'					=> 'text',
			'eval'						=> ['mandatory' => true, 'maxlength' => 255, 'tl_class' => 'w50'],
		],
		'action' => [
			'label'						=> &$GLOBALS['TL_LANG']['tl_glossar_log']['action'],
			'exclude'					=> true,
			'inputType'					=> 'text',
			'eval'						=> ['mandatory' => true, 'maxlength' => 255, 'tl_class' => 'w50'],
		],
	]
];
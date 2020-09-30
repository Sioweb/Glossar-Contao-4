<?php

/**
 * Contao Open Source CMS
 */

/**
 * @file tl_sw_glossar.php
 * @author Sascha Weidner
 * @version 2.3
 * @package sioweb.contao.extensions.glossar
 * @copyright Sascha Weidner, Sioweb
 */

/* Contao 3.2 support */
if (empty($GLOBALS['glossar']['types'])) {
	$this->loadLanguageFile('default');
}

$GLOBALS['TL_DCA']['tl_sw_glossar'] = [
	// Config
	'config' => [
		'dataContainer'               => 'Table',
		'enableVersioning'            => true,
		'ptable'                      => 'tl_glossar',
		'ctable'                      => ['tl_content']
	],

	// List
	'list' => [
		'sorting' => [
			'mode'                    => 4,
			'flag'                    => 2,
			'fields'                  => ['title'],
			'headerFields'            => ['title', 'language', 'tstamp'],
			'child_record_callback'   => ['sioweb.glossar.dca.terms', 'listTerms'],
			'panelLayout'             => 'filter;sort,search,limit',
			'child_record_class'      => 'no_padding'
		],
		'global_operations' => [
			'all' => [
				'label'               => &$GLOBALS['TL_LANG']['MSC']['all'],
				'href'                => 'act=select',
				'class'               => 'header_edit_all',
				'attributes'          => 'onclick="Backend.getScrollOffset()" accesskey="e"'
			],
		],
		'operations' => [
			'edit' => [
				'label'               => &$GLOBALS['TL_LANG']['tl_sw_glossar']['edit'],
				'href'                => 'table=tl_content',
				'icon'                => 'edit.svg'
			],
			'editheader' => [
				'label'               => &$GLOBALS['TL_LANG']['tl_sw_glossar']['editmeta'],
				'href'                => 'act=edit',
				'icon'                => 'header.svg'
			],
			'copy' => [
				'label'               => &$GLOBALS['TL_LANG']['tl_sw_glossar']['copy'],
				'href'                => 'act=paste&amp;mode=copy',
				'icon'                => 'copy.svg'
			],
			'cut' => [
				'label'               => &$GLOBALS['TL_LANG']['tl_sw_glossar']['cut'],
				'href'                => 'act=paste&amp;mode=cut',
				'icon'                => 'cut.svg'
			],
			'delete' => [
				'label'               => &$GLOBALS['TL_LANG']['tl_sw_glossar']['delete'],
				'href'                => 'act=delete',
				'icon'                => 'delete.svg',
				'attributes'          => 'onclick="if(!confirm(\'' . $GLOBALS['TL_LANG']['MSC']['deleteConfirm'] . '\'))return false;Backend.getScrollOffset()"'
			],
			'toggle' => [
				'label'               => &$GLOBALS['TL_LANG']['tl_sw_glossar']['toggle'],
				'icon'                => 'visible.svg',
				'attributes'          => 'onclick="Backend.getScrollOffset();return AjaxRequest.toggleVisibility(this,%s)"',
				'button_callback'     => ['sioweb.glossar.dca.terms', 'toggleIcon']
			],
			'show' => [
				'label'               => &$GLOBALS['TL_LANG']['tl_sw_glossar']['show'],
				'href'                => 'act=show',
				'icon'                => 'show.svg'
			]
		]
	],

	// Palettes
	'palettes' => [
		'__selector__'                => ['type', 'source', 'seo', 'published', 'addImage'],
		'default'                     => '{title_legend},type,title,alias;{teaser_legend},teaser;{image_legend},addImage;{more_legend},ignoreInTags,illegalChars,maxWidth,maxHeight,strictSearch,date,noPlural,termAsHeadline;{source_legend},source;{seo_legend},seo;{publish_legend},published',
		'abbr'                        => '{title_legend},type,title,alias,ignoreInTags,illegalChars,explanation;{source_legend},source;{publish_legend},published'
	],

	'subpalettes' => [
		'seo'                 => 'term_in_title_tag,term_description_tag,term_in_title_str_tag',
		'addImage'            => 'singleSRC,size,floating,imagemargin,fullsize,overwriteMeta',
		'source_page'         => 'jumpTo',
		'source_internal'     => 'jumpTo',
		'source_article'      => 'articleId',
		'source_external'     => 'url,target',
		'published'           => 'start,stop'
	],

	// Fields
	'fields' => [
		// 'pid' => array
		// (
		//   'foreignKey'              => 'tl_content.id',
		//   'relation'                => ['type'=>'belongsTo', 'load'=>'eager')
		// ],
		// 'pidlog' => array
		// (
		//   'foreignKey'              => 'tl_glossar_log.id',
		//   'relation'                => ['type'=>'belongsTo', 'load'=>'eager', 'field' => 'pid')
		// ],
		'type' => [
			'label'                   => &$GLOBALS['TL_LANG']['tl_sw_glossar']['type'],
			'default'                 => 'default',
			'exclude'                 => true,
			'filter'                  => true,
			'inputType'               => 'select',
			'options'                 => array_keys((array)$GLOBALS['TL_LANG']['glossar']['types']),
			'reference'               => &$GLOBALS['TL_LANG']['glossar']['types'],
			'eval'                    => ['tl_class' => 'w50 clr long', 'chosen' => true, 'submitOnChange' => true],
		],
		'title' => [
			'label'                   => &$GLOBALS['TL_LANG']['tl_sw_glossar']['title'],
			'inputType'               => 'text',
			'exclude'                 => true,
			'filter'                  => true,
			'sorting'                 => true,
			'eval'                    => ['mandatory' => true, 'maxlength' => 255, 'tl_class' => 'w50', 'gsIgnore' => true],
		],
		'alias' => [
			'label'                   => &$GLOBALS['TL_LANG']['tl_sw_glossar']['alias'],
			'exclude'                 => true,
			'inputType'               => 'text',
			'search'                  => true,
			'eval'                    => ['rgxp' => 'alias', 'doNotCopy' => true, 'maxlength' => 128, 'tl_class' => 'w50', 'gsIgnore' => true],
			'save_callback' => [
				['sioweb.glossar.dca.terms', 'generateAlias'],
			],
		],
		'source' => [
			'label'                   => &$GLOBALS['TL_LANG']['tl_sw_glossar']['source'],
			'default'                 => 'default',
			'exclude'                 => true,
			'filter'                  => true,
			'inputType'               => 'radio',
			'options_callback'        => ['sioweb.glossar.dca.terms', 'getSourceOptions'],
			'reference'               => &$GLOBALS['TL_LANG']['glossar']['sources'],
			'eval'                    => ['submitOnChange' => true, 'helpwizard' => true],
		],
		'jumpTo' => [
			'label'                   => &$GLOBALS['TL_LANG']['tl_sw_glossar']['jumpTo'],
			'exclude'                 => true,
			'inputType'               => 'pageTree',
			'foreignKey'              => 'tl_page.title',
			'eval'                    => ['fieldType' => 'radio', 'tl_class' => 'w50 clr', 'gsLabel' => 'jumpToGlossar'],
			'relation'                => ['type' => 'belongsTo', 'load' => 'lazy'],
		],
		'articleId' => [
			'label'                   => &$GLOBALS['TL_LANG']['tl_sw_glossar']['articleId'],
			'exclude'                 => true,
			'inputType'               => 'select',
			'options_callback'        => ['sioweb.glossar.dca.terms', 'getArticleAlias'],
			'eval'                    => ['chosen' => true, 'mandatory' => true],
		],
		'url' => [
			'label'                   => &$GLOBALS['TL_LANG']['MSC']['url'],
			'exclude'                 => true,
			'search'                  => true,
			'inputType'               => 'text',
			'eval'                    => ['mandatory' => true, 'decodeEntities' => true, 'maxlength' => 255, 'tl_class' => 'w50'],
		],
		'target' => [
			'label'                   => &$GLOBALS['TL_LANG']['MSC']['target'],
			'exclude'                 => true,
			'inputType'               => 'checkbox',
			'eval'                    => ['tl_class' => 'w50 m12'],
		],
		'maxWidth' => [
			'label'                   => &$GLOBALS['TL_LANG']['tl_sw_glossar']['maxWidth'],
			'exclude'                 => true,
			'inputType'               => 'text',
			'eval'                    => ['maxlength' => 255, 'tl_class' => 'w50', 'gsLabel' => 'glossarMaxWidth'],
		],
		'maxHeight' => [
			'label'                   => &$GLOBALS['TL_LANG']['tl_sw_glossar']['maxHeight'],
			'exclude'                 => true,
			'inputType'               => 'text',
			'eval'                    => ['maxlength' => 255, 'tl_class' => 'w50', 'gsLabel' => 'glossarMaxHeight'],
		],
		'ignoreInTags' => [
			'label'                   => &$GLOBALS['TL_LANG']['tl_sw_glossar']['ignoreInTags'],
			'exclude'                 => true,
			'inputType'               => 'text',
			'eval'                    => ['maxlength' => 255, 'tl_class' => 'w50'],
		],
		'illegalChars' => [
			'label'                   => &$GLOBALS['TL_LANG']['tl_sw_glossar']['illegalChars'],
			'exclude'                 => true,
			'inputType'               => 'text',
			'eval'                    => ['maxlength' => 255, 'tl_class' => 'w50'],
		],
		'date' => [
			'label'                   => &$GLOBALS['TL_LANG']['tl_sw_glossar']['date'],
			'default'                 => time(),
			'exclude'                 => true,
			'filter'                  => true,
			'sorting'                 => true,
			'flag'                    => 8,
			'inputType'               => 'text',
			'eval'                    => ['rgxp' => 'date', 'doNotCopy' => true, 'datepicker' => true, 'tl_class' => 'w50 wizard', 'gsIgnore' => true],
		],
		'noPlural' => [
			'label'                   => &$GLOBALS['TL_LANG']['tl_sw_glossar']['noPlural'],
			'exclude'                 => true,
			'inputType'               => 'checkbox',
			'eval'                    => ['tl_class' => 'w50 clr'],
		],
		'strictSearch' => [
			'label'                   => &$GLOBALS['TL_LANG']['tl_sw_glossar']['strictSearch'],
			'default'                 => 'alias',
			'inputType'               => 'select',
			'options'                 => array_keys((array)$GLOBALS['TL_LANG']['glossar']['strictSearch']),
			'reference'               => &$GLOBALS['TL_LANG']['glossar']['strictSearch'],
			'eval'                    => ['tl_class' => 'w50', 'includeBlankOption' => true],
		],
		'termAsHeadline' => [
			'label'                   => &$GLOBALS['TL_LANG']['tl_sw_glossar']['termAsHeadline'],
			'exclude'                 => true,
			'inputType'               => 'checkbox',
			'eval'                    => ['tl_class' => 'w50'],
		],
		'teaser' => [
			'label'                   => &$GLOBALS['TL_LANG']['tl_sw_glossar']['teaser'],
			'exclude'                 => true,
			'search'                  => true,
			'inputType'               => 'textarea',
			'eval'                    => ['rte' => 'tinyMCE', 'tl_class' => 'clr long', 'gsIgnore' => true],
		],
		'description' => [
			'label'                   => &$GLOBALS['TL_LANG']['tl_sw_glossar']['description'],
			'exclude'                 => true,
			'search'                  => true,
			'inputType'               => 'textarea',
			'eval'                    => ['rte' => 'tinyMCE', 'style' => 'height: 50px;', 'tl_class' => 'clr long', 'gsIgnore' => true],
		],
		'explanation' => [
			'label'                   => &$GLOBALS['TL_LANG']['tl_sw_glossar']['explanation'],
			'exclude'                 => true,
			'search'                  => true,
			'inputType'               => 'text',
			'eval'                    => ['maxlength' => 255, 'tl_class' => 'clr long', 'gsIgnore' => true],
		],
		'url' => [
			'label'                   => &$GLOBALS['TL_LANG']['tl_sw_glossar']['url'],
			'exclude'                 => true,
			'search'                  => true,
			'inputType'               => 'text',
			'eval'                    => ['maxlength' => 255, 'tl_class' => 'clr', 'gsIgnore' => true],
		],
		'target' => [
			'label'                   => &$GLOBALS['TL_LANG']['MSC']['target'],
			'exclude'                 => true,
			'inputType'               => 'checkbox',
			'eval'                    => ['tl_class' => 'w50 m12', 'gsIgnore' => true],
		],
		'seo' => [
			'label'                   => &$GLOBALS['TL_LANG']['tl_sw_glossar']['seo'],
			'exclude'                 => true,
			'filter'                  => true,
			'sorting'                 => true,
			'inputType'               => 'checkbox',
			'eval'                    => ['submitOnChange' => true],
		],
		'term_in_title_tag' => [
			'label'                   => &$GLOBALS['TL_LANG']['tl_sw_glossar']['term_in_title_tag'],
			'exclude'                 => true,
			'inputType'               => 'checkbox',
			'eval'                    => ['tl_class' => 'w50 clr'],
		],
		'term_in_title_str_tag' => [
			'label'                   => &$GLOBALS['TL_LANG']['tl_sw_glossar']['term_in_title_str_tag'],
			'exclude'                 => true,
			'inputType'               => 'text',
			'eval'                    => ['maxlength' => 255, 'tl_class' => 'w50 clr', 'gsIgnore' => true],
		],
		'replace_pageTitle' => [
			'label'                   => &$GLOBALS['TL_LANG']['tl_sw_glossar']['replace_pageTitle'],
			'exclude'                 => true,
			'inputType'               => 'checkbox',
			'eval'                    => ['tl_class' => 'w50 clr'],
		],
		'term_description_tag' => [
			'label'                   => &$GLOBALS['TL_LANG']['tl_sw_glossar']['term_description_tag'],
			'exclude'                 => true,
			'inputType'               => 'text',
			'eval'                    => ['maxlength' => 255, 'tl_class' => 'long clr', 'gsIgnore' => true],
		],
		'tags' => [
			'label'                   => &$GLOBALS['TL_LANG']['tl_sw_glossar']['tags'],
			'inputType'               => 'tag',
			'eval'                    => ['tl_class' => 'clr long'],
		],
		'published' => [
			'label'                   => &$GLOBALS['TL_LANG']['tl_sw_glossar']['published'],
			'exclude'                 => true,
			'filter'                  => true,
			'flag'                    => 1,
			'inputType'               => 'checkbox',
			'eval'                    => ['submitOnChange' => true, 'doNotCopy' => true],
		],
		'start' => [
			'label'                   => &$GLOBALS['TL_LANG']['tl_sw_glossar']['start'],
			'exclude'                 => true,
			'inputType'               => 'text',
			'eval'                    => ['rgxp' => 'datim', 'datepicker' => true, 'tl_class' => 'w50 wizard'],
		],
		'stop' => [
			'label'                   => &$GLOBALS['TL_LANG']['tl_sw_glossar']['stop'],
			'exclude'                 => true,
			'inputType'               => 'text',
			'eval'                    => ['rgxp' => 'datim', 'datepicker' => true, 'tl_class' => 'w50 wizard'],
		],
		'addImage' => [
			'label'                   => &$GLOBALS['TL_LANG']['tl_sw_glossar']['addImage'],
			'exclude'                 => true,
			'inputType'               => 'checkbox',
			'eval'                    => ['submitOnChange' => true],
		],
		'overwriteMeta' => [
			'label'                   => &$GLOBALS['TL_LANG']['tl_sw_glossar']['overwriteMeta'],
			'exclude'                 => true,
			'inputType'               => 'checkbox',
			'eval'                    => ['submitOnChange' => true, 'tl_class' => 'w50 clr'],
		],
		'singleSRC' => [
			'label'                   => &$GLOBALS['TL_LANG']['tl_sw_glossar']['singleSRC'],
			'exclude'                 => true,
			'inputType'               => 'fileTree',
			'eval'                    => ['filesOnly' => true, 'fieldType' => 'radio', 'mandatory' => true, 'tl_class' => 'clr'],
			'load_callback' => [
				['sioweb.glossar.dca.terms', 'setSingleSrcFlags'],
			],
		],
		'alt' => [
			'label'                   => &$GLOBALS['TL_LANG']['tl_sw_glossar']['alt'],
			'exclude'                 => true,
			'search'                  => true,
			'inputType'               => 'text',
			'eval'                    => ['maxlength' => 255, 'tl_class' => 'w50'],
		],
		'imageTitle' => [
			'label'                   => &$GLOBALS['TL_LANG']['tl_sw_glossar']['imageTitle'],
			'exclude'                 => true,
			'search'                  => true,
			'inputType'               => 'text',
			'eval'                    => ['maxlength' => 255, 'tl_class' => 'w50'],
		],
		'size' => [
			'label'                   => &$GLOBALS['TL_LANG']['tl_sw_glossar']['size'],
			'exclude'                 => true,
			'inputType'               => 'imageSize',
			'reference'               => &$GLOBALS['TL_LANG']['MSC'],
			'eval'                    => ['rgxp' => 'natural', 'includeBlankOption' => true, 'nospace' => true, 'helpwizard' => true, 'tl_class' => 'w50'],
			'options_callback' => function () {
				return System::getContainer()->get('contao.image.image_sizes')->getOptionsForUser(BackendUser::getInstance());
			},
		],
		'imagemargin' => [
			'label'                   => &$GLOBALS['TL_LANG']['tl_sw_glossar']['imagemargin'],
			'exclude'                 => true,
			'inputType'               => 'trbl',
			'options'                 => $GLOBALS['TL_CSS_UNITS'],
			'eval'                    => ['includeBlankOption' => true, 'tl_class' => 'w50'],
		],
		'imageUrl' => [
			'label'                   => &$GLOBALS['TL_LANG']['tl_sw_glossar']['imageUrl'],
			'exclude'                 => true,
			'search'                  => true,
			'inputType'               => 'text',
			'eval'                    => ['rgxp' => 'url', 'decodeEntities' => true, 'maxlength' => 255, 'dcaPicker' => true, 'addWizardClass' => false, 'tl_class' => 'w50'],
		],
		'fullsize' => [
			'label'                   => &$GLOBALS['TL_LANG']['tl_sw_glossar']['fullsize'],
			'exclude'                 => true,
			'inputType'               => 'checkbox',
			'eval'                    => ['tl_class' => 'w50 m12'],
		],
		'caption' => [
			'label'                   => &$GLOBALS['TL_LANG']['tl_sw_glossar']['caption'],
			'exclude'                 => true,
			'search'                  => true,
			'inputType'               => 'text',
			'eval'                    => ['maxlength' => 255, 'allowHtml' => true, 'tl_class' => 'w50'],
		],
		'floating' => [
			'label'                   => &$GLOBALS['TL_LANG']['tl_sw_glossar']['floating'],
			'default'                 => 'above',
			'exclude'                 => true,
			'inputType'               => 'radioTable',
			'options'                 => ['above', 'left', 'right', 'below'],
			'eval'                    => ['cols' => 4, 'tl_class' => 'w50'],
			'reference'               => &$GLOBALS['TL_LANG']['MSC'],
		],
	],
];

if (in_array('tags', $this->Config->getActiveModules())) {
	foreach ($GLOBALS['TL_DCA']['tl_sw_glossar']['palettes'] as $palette => &$fields) {
		if (is_array($fields)) {
			continue;
		}
		$fields = str_replace('alias', 'alias,tags', $fields);
	}
	unset($fields);
}

<?php

/**
 * Contao Open Source CMS
 */

/**
 * @file tl_sw_glossar.php
 * @author Sascha Weidner
 * @version 3.0.0
 * @package sioweb.contao.extensions.glossar
 * @copyright Sascha Weidner, Sioweb
 */

/* Contao 3.2 support */
if(empty($GLOBALS['glossar']['types'])) {
  $this->loadLanguageFile('default');
}

$GLOBALS['TL_DCA']['tl_sw_glossar'] = array(
  // Config
  'config' => array
  (
    'dataContainer'               => 'Table',
    'enableVersioning'            => true,
    'ptable'                      => 'tl_glossar',
    'ctable'                      => array('tl_content')
  ),

  // List
  'list' => array
  (
    'sorting' => array
    (
      'mode'                    => 4,
      'flag'                    => 2,
      'fields'                  => array('title'),
      'headerFields'            => array('title','language','tstamp'),
      'child_record_callback'   => array('sioweb.glossar.dca.terms', 'listTerms'),
      'panelLayout'             => 'filter;sort,search,limit',
      'child_record_class'      => 'no_padding'
    ),
    'global_operations' => array
    (
      'all' => array
      (
        'label'               => &$GLOBALS['TL_LANG']['MSC']['all'],
        'href'                => 'act=select',
        'class'               => 'header_edit_all',
        'attributes'          => 'onclick="Backend.getScrollOffset()" accesskey="e"'
      ),
    ),
    'operations' => array
    (
      'edit' => array
      (
        'label'               => &$GLOBALS['TL_LANG']['tl_sw_glossar']['edit'],
        'href'                => 'table=tl_content',
        'icon'                => 'edit.svg'
      ),
      'editheader' => array
      (
        'label'               => &$GLOBALS['TL_LANG']['tl_sw_glossar']['editmeta'],
        'href'                => 'act=edit',
        'icon'                => 'header.svg'
      ),
      'copy' => array
      (
        'label'               => &$GLOBALS['TL_LANG']['tl_sw_glossar']['copy'],
        'href'                => 'act=paste&amp;mode=copy',
        'icon'                => 'copy.svg'
      ),
      'cut' => array
      (
        'label'               => &$GLOBALS['TL_LANG']['tl_sw_glossar']['cut'],
        'href'                => 'act=paste&amp;mode=cut',
        'icon'                => 'cut.svg'
      ),
      'delete' => array
      (
        'label'               => &$GLOBALS['TL_LANG']['tl_sw_glossar']['delete'],
        'href'                => 'act=delete',
        'icon'                => 'delete.svg',
        'attributes'          => 'onclick="if(!confirm(\'' . $GLOBALS['TL_LANG']['MSC']['deleteConfirm'] . '\'))return false;Backend.getScrollOffset()"'
      ),
      'toggle' => array
      (
        'label'               => &$GLOBALS['TL_LANG']['tl_sw_glossar']['toggle'],
        'icon'                => 'visible.svg',
        'attributes'          => 'onclick="Backend.getScrollOffset();return AjaxRequest.toggleVisibility(this,%s)"',
        'button_callback'     => array('sioweb.glossar.dca.terms', 'toggleIcon')
      ),
      'show' => array
      (
        'label'               => &$GLOBALS['TL_LANG']['tl_sw_glossar']['show'],
        'href'                => 'act=show',
        'icon'                => 'show.svg'
      )
    )
  ),

  // Palettes
  'palettes' => array
  (
    '__selector__'                => array('type','source','seo','published','addImage'),
    'default'                     => '{title_legend},type,title,alias;{teaser_legend},teaser;{image_legend},addImage;{more_legend},ignoreInTags,illegalChars,maxWidth,maxHeight,strictSearch,date,noPlural,termAsHeadline;{source_legend},source;{seo_legend},seo;{publish_legend},published',
    'abbr'                        => '{title_legend},type,title,alias,ignoreInTags,illegalChars,explanation;{source_legend},source;{publish_legend},published'
  ),

  'subpalettes' => array
  (
    'seo'                 => 'term_in_title_tag,term_description_tag,term_in_title_str_tag',
    'addImage'            => 'singleSRC,size,floating,imagemargin,fullsize,overwriteMeta',
    'source_page'         => 'jumpTo',
    'source_internal'     => 'jumpTo',
    'source_article'      => 'articleId',
    'source_external'     => 'url,target',
    'published'           => 'start,stop'
  ),

  // Fields
  'fields' => array
  (
    // 'pid' => array
    // (
    //   'foreignKey'              => 'tl_content.id',
    //   'relation'                => array('type'=>'belongsTo', 'load'=>'eager')
    // ),
    // 'pidlog' => array
    // (
    //   'foreignKey'              => 'tl_glossar_log.id',
    //   'relation'                => array('type'=>'belongsTo', 'load'=>'eager', 'field' => 'pid')
    // ),
    'type' => array
    (
      'label'                   => &$GLOBALS['TL_LANG']['tl_sw_glossar']['type'],
      'default'                 => 'default',
      'exclude'                 => true,
      'filter'                  => true,
      'inputType'               => 'select',
      'options'                 => array_keys((array)$GLOBALS['TL_LANG']['glossar']['types']),
      'reference'               => &$GLOBALS['TL_LANG']['glossar']['types'],
      'eval'                    => array('tl_class'=>'w50 clr long', 'chosen'=>true, 'submitOnChange'=>true),
    ),
    'title' => array
    (
      'label'                   => &$GLOBALS['TL_LANG']['tl_sw_glossar']['title'],
      'inputType'               => 'text',
      'exclude'                 => true,
      'filter'                  => true,
      'sorting'                 => true,
      'eval'                    => array('mandatory'=>true,'maxlength'=>255,'tl_class'=>'w50','gsIgnore'=>true),
    ),
    'alias' => array
    (
      'label'                   => &$GLOBALS['TL_LANG']['tl_sw_glossar']['alias'],
      'exclude'                 => true,
      'inputType'               => 'text',
      'search'                  => true,
      'eval'                    => array('rgxp'=>'alias','doNotCopy'=>true,'maxlength'=>128,'tl_class'=>'w50','gsIgnore'=>true),
      'save_callback' => array
      (
        array('sioweb.glossar.dca.terms', 'generateAlias')
      )
    ),
    'source' => array
    (
      'label'                   => &$GLOBALS['TL_LANG']['tl_sw_glossar']['source'],
      'default'                 => 'default',
      'exclude'                 => true,
      'filter'                  => true,
      'inputType'               => 'radio',
      'options_callback'        => array('sioweb.glossar.dca.terms', 'getSourceOptions'),
      'reference'               => &$GLOBALS['TL_LANG']['glossar']['sources'],
      'eval'                    => array('submitOnChange'=>true, 'helpwizard'=>true),
    ),
    'jumpTo' => array
    (
      'label'                   => &$GLOBALS['TL_LANG']['tl_sw_glossar']['jumpTo'],
      'exclude'                 => true,
      'inputType'               => 'pageTree',
      'foreignKey'              => 'tl_page.title',
      'eval'                    => array('fieldType'=>'radio','tl_class'=>'w50 clr','gsLabel'=>'jumpToGlossar'),
      'relation'                => array('type'=>'belongsTo','load'=>'lazy')
    ),
    'articleId' => array
    (
      'label'                   => &$GLOBALS['TL_LANG']['tl_sw_glossar']['articleId'],
      'exclude'                 => true,
      'inputType'               => 'select',
      'options_callback'        => array('sioweb.glossar.dca.terms', 'getArticleAlias'),
      'eval'                    => array('chosen'=>true, 'mandatory'=>true),
    ),
    'url' => array
    (
      'label'                   => &$GLOBALS['TL_LANG']['MSC']['url'],
      'exclude'                 => true,
      'search'                  => true,
      'inputType'               => 'text',
      'eval'                    => array('mandatory'=>true, 'decodeEntities'=>true, 'maxlength'=>255, 'tl_class'=>'w50'),
    ),
    'target' => array
    (
      'label'                   => &$GLOBALS['TL_LANG']['MSC']['target'],
      'exclude'                 => true,
      'inputType'               => 'checkbox',
      'eval'                    => array('tl_class'=>'w50 m12'),
    ),
    'maxWidth' => array
    (
      'label'                   => &$GLOBALS['TL_LANG']['tl_sw_glossar']['maxWidth'],
      'exclude'                 => true,
      'inputType'               => 'text',
      'eval'                    => array('maxlength'=>255, 'tl_class'=>'w50','gsLabel'=>'glossarMaxWidth'),
    ),
    'maxHeight' => array
    (
      'label'                   => &$GLOBALS['TL_LANG']['tl_sw_glossar']['maxHeight'],
      'exclude'                 => true,
      'inputType'               => 'text',
      'eval'                    => array('maxlength'=>255, 'tl_class'=>'w50','gsLabel'=>'glossarMaxHeight'),
    ),
    'ignoreInTags' => array
    (
      'label'                   => &$GLOBALS['TL_LANG']['tl_sw_glossar']['ignoreInTags'],
      'exclude'                 => true,
      'inputType'               => 'text',
      'eval'                    => array('maxlength'=>255,'tl_class'=>'w50'),
    ),
    'illegalChars' => array
    (
      'label'                   => &$GLOBALS['TL_LANG']['tl_sw_glossar']['illegalChars'],
      'exclude'                 => true,
      'inputType'               => 'text',
      'eval'                    => array('maxlength'=>255,'tl_class'=>'w50'),
    ),
    'date' => array
    (
      'label'                   => &$GLOBALS['TL_LANG']['tl_sw_glossar']['date'],
      'default'                 => time(),
      'exclude'                 => true,
      'filter'                  => true,
      'sorting'                 => true,
      'flag'                    => 8,
      'inputType'               => 'text',
      'eval'                    => array('rgxp'=>'date', 'doNotCopy'=>true,'datepicker'=>true,'tl_class'=>'w50 wizard','gsIgnore'=>true),
    ),
    'noPlural' => array
    (
      'label'                   => &$GLOBALS['TL_LANG']['tl_sw_glossar']['noPlural'],
      'exclude'                 => true,
      'inputType'               => 'checkbox',
      'eval'                    => array('tl_class'=>'w50 clr'),
    ),
    'strictSearch' => array
    (
      'label'                   => &$GLOBALS['TL_LANG']['tl_sw_glossar']['strictSearch'],
      'default'                 => 'alias',
      'inputType'               => 'select',
      'options'                 => array_keys((array)$GLOBALS['TL_LANG']['glossar']['strictSearch']),
      'reference'               => &$GLOBALS['TL_LANG']['glossar']['strictSearch'],
      'eval'                    => array('tl_class'=>'w50','includeBlankOption'=>true),
    ),
    'termAsHeadline' => array
    (
      'label'                   => &$GLOBALS['TL_LANG']['tl_sw_glossar']['termAsHeadline'],
      'exclude'                 => true,
      'inputType'               => 'checkbox',
      'eval'                    => array('tl_class'=>'w50'),
    ),
    'teaser' => array
    (
      'label'                   => &$GLOBALS['TL_LANG']['tl_sw_glossar']['teaser'],
      'exclude'                 => true,
      'search'                  => true,
      'inputType'               => 'textarea',
      'eval'                    => array('rte'=>'tinyMCE','tl_class'=>'clr long','gsIgnore'=>true),
    ),
    'description' => array
    (
      'label'                   => &$GLOBALS['TL_LANG']['tl_sw_glossar']['description'],
      'exclude'                 => true,
      'search'                  => true,
      'inputType'               => 'textarea',
      'eval'                    => array('rte'=>'tinyMCE','style'=>'height: 50px;','tl_class'=>'clr long','gsIgnore'=>true),
    ),
    'explanation' => array
    (
      'label'                   => &$GLOBALS['TL_LANG']['tl_sw_glossar']['explanation'],
      'exclude'                 => true,
      'search'                  => true,
      'inputType'               => 'text',
      'eval'                    => array('maxlength'=>255,'tl_class'=>'clr long','gsIgnore'=>true),
    ),
    'url' => array
    (
      'label'                   => &$GLOBALS['TL_LANG']['tl_sw_glossar']['url'],
      'exclude'                 => true,
      'search'                  => true,
      'inputType'               => 'text',
      'eval'                    => array('maxlength'=>255, 'tl_class'=>'clr','gsIgnore'=>true),
    ),
    'target' => array
    (
      'label'                   => &$GLOBALS['TL_LANG']['MSC']['target'],
      'exclude'                 => true,
      'inputType'               => 'checkbox',
      'eval'                    => array('tl_class'=>'w50 m12','gsIgnore'=>true),
    ),
    'seo' => array
    (
      'label'                   => &$GLOBALS['TL_LANG']['tl_sw_glossar']['seo'],
      'exclude'                 => true,
      'filter'                  => true,
      'sorting'                 => true,
      'inputType'               => 'checkbox',
      'eval'                    => array('submitOnChange'=>true),
    ),
    'term_in_title_tag' => array
    (
      'label'                   => &$GLOBALS['TL_LANG']['tl_sw_glossar']['term_in_title_tag'],
      'exclude'                 => true,
      'inputType'               => 'checkbox',
      'eval'                    => array('tl_class'=>'w50 clr'),
    ),
    'term_in_title_str_tag' => array
    (
      'label'                   => &$GLOBALS['TL_LANG']['tl_sw_glossar']['term_in_title_str_tag'],
      'exclude'                 => true,
      'inputType'               => 'text',
      'eval'                    => array('maxlength'=>255,'tl_class'=>'w50 clr','gsIgnore'=>true),
    ),
    'replace_pageTitle' => array
    (
      'label'                   => &$GLOBALS['TL_LANG']['tl_sw_glossar']['replace_pageTitle'],
      'exclude'                 => true,
      'inputType'               => 'checkbox',
      'eval'                    => array('tl_class'=>'w50 clr'),
    ),
    'term_description_tag' => array
    (
      'label'                   => &$GLOBALS['TL_LANG']['tl_sw_glossar']['term_description_tag'],
      'exclude'                 => true,
      'inputType'               => 'text',
      'eval'                    => array('maxlength'=>255,'tl_class'=>'long clr','gsIgnore'=>true),
    ),
    'tags' => array
    (
      'label'                   => &$GLOBALS['TL_LANG']['tl_sw_glossar']['tags'],
      'inputType'               => 'tag',
      'eval'                    => array('tl_class'=>'clr long'),
    ),
    'published' => array
    (
      'label'                   => &$GLOBALS['TL_LANG']['tl_sw_glossar']['published'],
      'exclude'                 => true,
      'filter'                  => true,
      'flag'                    => 1,
      'inputType'               => 'checkbox',
      'eval'                    => array('submitOnChange'=>true, 'doNotCopy'=>true),
    ),
    'start' => array
    (
      'label'                   => &$GLOBALS['TL_LANG']['tl_sw_glossar']['start'],
      'exclude'                 => true,
      'inputType'               => 'text',
      'eval'                    => array('rgxp'=>'datim', 'datepicker'=>true, 'tl_class'=>'w50 wizard'),
    ),
    'stop' => array
    (
      'label'                   => &$GLOBALS['TL_LANG']['tl_sw_glossar']['stop'],
      'exclude'                 => true,
      'inputType'               => 'text',
      'eval'                    => array('rgxp'=>'datim', 'datepicker'=>true, 'tl_class'=>'w50 wizard'),
    ),
    'addImage' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_sw_glossar']['addImage'],
			'exclude'                 => true,
			'inputType'               => 'checkbox',
			'eval'                    => array('submitOnChange'=>true),
		),
		'overwriteMeta' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_sw_glossar']['overwriteMeta'],
			'exclude'                 => true,
			'inputType'               => 'checkbox',
			'eval'                    => array('submitOnChange'=>true, 'tl_class'=>'w50 clr'),
		),
		'singleSRC' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_sw_glossar']['singleSRC'],
			'exclude'                 => true,
			'inputType'               => 'fileTree',
			'eval'                    => array('filesOnly'=>true, 'fieldType'=>'radio', 'mandatory'=>true, 'tl_class'=>'clr'),
			'load_callback' => array
			(
        array('sioweb.glossar.dca.terms', 'setSingleSrcFlags')
			),
		),
		'alt' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_sw_glossar']['alt'],
			'exclude'                 => true,
			'search'                  => true,
			'inputType'               => 'text',
			'eval'                    => array('maxlength'=>255, 'tl_class'=>'w50'),
		),
		'imageTitle' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_sw_glossar']['imageTitle'],
			'exclude'                 => true,
			'search'                  => true,
			'inputType'               => 'text',
			'eval'                    => array('maxlength'=>255, 'tl_class'=>'w50'),
		),
		'size' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_sw_glossar']['size'],
			'exclude'                 => true,
			'inputType'               => 'imageSize',
			'reference'               => &$GLOBALS['TL_LANG']['MSC'],
			'eval'                    => array('rgxp'=>'natural', 'includeBlankOption'=>true, 'nospace'=>true, 'helpwizard'=>true, 'tl_class'=>'w50'),
			'options_callback' => function ()
			{
				return System::getContainer()->get('contao.image.image_sizes')->getOptionsForUser(BackendUser::getInstance());
			},
		),
		'imagemargin' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_sw_glossar']['imagemargin'],
			'exclude'                 => true,
			'inputType'               => 'trbl',
			'options'                 => $GLOBALS['TL_CSS_UNITS'],
			'eval'                    => array('includeBlankOption'=>true, 'tl_class'=>'w50'),
		),
		'imageUrl' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_sw_glossar']['imageUrl'],
			'exclude'                 => true,
			'search'                  => true,
			'inputType'               => 'text',
			'eval'                    => array('rgxp'=>'url', 'decodeEntities'=>true, 'maxlength'=>255, 'dcaPicker'=>true, 'addWizardClass'=>false, 'tl_class'=>'w50'),
		),
		'fullsize' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_sw_glossar']['fullsize'],
			'exclude'                 => true,
			'inputType'               => 'checkbox',
			'eval'                    => array('tl_class'=>'w50 m12'),
		),
		'caption' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_sw_glossar']['caption'],
			'exclude'                 => true,
			'search'                  => true,
			'inputType'               => 'text',
			'eval'                    => array('maxlength'=>255, 'allowHtml'=>true, 'tl_class'=>'w50'),
		),
		'floating' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_sw_glossar']['floating'],
			'default'                 => 'above',
			'exclude'                 => true,
			'inputType'               => 'radioTable',
			'options'                 => array('above', 'left', 'right', 'below'),
			'eval'                    => array('cols'=>4, 'tl_class'=>'w50'),
			'reference'               => &$GLOBALS['TL_LANG']['MSC'],
		),
  )
);

if(in_array('tags', $this->Config->getActiveModules())) {
  foreach($GLOBALS['TL_DCA']['tl_sw_glossar']['palettes'] as $palette => &$fields) {
    if(is_array($fields)) {
      continue;
    }
    $fields = str_replace('alias','alias,tags', $fields);
  }
  unset($fields);
}
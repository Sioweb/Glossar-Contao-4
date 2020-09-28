<?php

/**
 * Contao Open Source CMS
 */

/**
 * @file tl_glossar.php
 * @author Sascha Weidner
 * @version 3.0.0
 * @package sioweb.contao.extensions.glossar
 * @copyright Sascha Weidner, Sioweb
 */

$GLOBALS['TL_DCA']['tl_glossar'] = array(

  // Config
  'config' => array
  (
    'dataContainer'               => 'Table',
    'ctable'                      => array('tl_sw_glossar'),
    'switchToEdit'                => true,
    'enableVersioning'            => true
  ),

  // List
  'list' => array
  (
    'sorting' => array
    (
      'mode'                    => 1,
      'fields'                  => array('title'),
      'flag'                    => 1,
      'panelLayout'             => 'sort,search,limit'
    ),
    'label' => array
    (
      'fields'                  => array('title'),
      'format'                  => '%s',
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
      'import' => array
      (
        'label'               => &$GLOBALS['TL_LANG']['MSC']['import'],
        'href'                => 'key=importGlossar',
        'class'               => 'header_edit_all',
      ),
      'export' => array
      (
        'label'               => &$GLOBALS['TL_LANG']['MSC']['export'],
        'href'                => 'key=exportGlossar',
        'class'               => 'header_edit_all',
      )
    ),
    'operations' => array
    (
      'edit' => array
      (
        'label'               => &$GLOBALS['TL_LANG']['tl_glossar']['edit'],
        'href'                => 'table=tl_sw_glossar',
        'icon'                => 'edit.svg'
      ),
      'editheader' => array
      (
        'label'               => &$GLOBALS['TL_LANG']['tl_glossar']['editheader'],
        'href'                => 'act=edit',
        'icon'                => 'header.svg',
        'button_callback'     => array('sioweb.glossar.dca.glossar', 'editHeader')
      ),
      'copy' => array
      (
        'label'               => &$GLOBALS['TL_LANG']['tl_glossar']['copy'],
        'href'                => 'act=copy',
        'icon'                => 'copy.svg',
        'button_callback'     => array('sioweb.glossar.dca.glossar', 'copyArchive')
      ),
      'delete' => array
      (
        'label'               => &$GLOBALS['TL_LANG']['tl_glossar']['delete'],
        'href'                => 'act=delete',
        'icon'                => 'delete.svg',
        'attributes'          => 'onclick="if(!confirm(\'' . $GLOBALS['TL_LANG']['MSC']['deleteConfirm'] . '\'))return false;Backend.getScrollOffset()"',
        'button_callback'     => array('sioweb.glossar.dca.glossar', 'deleteArchive')
      ),
      'show' => array
      (
        'label'               => &$GLOBALS['TL_LANG']['tl_glossar']['show'],
        'href'                => 'act=show',
        'icon'                => 'show.svg'
      ),
      'export' => array
      (
        'label'               => &$GLOBALS['TL_LANG']['MSC']['export'],
        'href'                => 'key=exportTerms',
        'icon'                => 'theme_export.svg',
        'class'               => 'header_edit_all',
      )
    )
  ),

  // Palettes
  'palettes' => array
  (
    '__selector__'                => array('allowComments', 'seo'),
    'default'                     => '{title_legend},title,alias,language,fallback,allowComments;{seo_legend},seo',
  ),

  // Subpalettes
  'subpalettes' => array
  (
    'allowComments'               => 'notify,sortOrder,perPage,moderate,bbcode,requireLogin,disableCaptcha',
    'seo'                         => 'term_in_title_tag,term_description_tag,term_in_title_str_tag',
  ),

  // Fields
  'fields' => array
  (
    'id' => array
    (
      'foreignKey'              => 'tl_glossar.pid',
      'relation'                => array('type'=>'belongsToMany', 'load'=>'eager', 'field' => 'pid')
    ),
    'title' => array
    (
      'label'                   => &$GLOBALS['TL_LANG']['tl_glossar']['title'],
      'exclude'                 => true,
      'inputType'               => 'text',
      'eval'                    => array('mandatory'=>true, 'maxlength'=>255, 'tl_class'=>'w50'),
    ),
    'alias' => array
    (
      'label'                   => &$GLOBALS['TL_LANG']['tl_glossar']['alias'],
      'exclude'                 => true,
      'inputType'               => 'text',
      'search'                  => true,
      'eval'                    => array('rgxp'=>'alias', 'doNotCopy'=>true, 'maxlength'=>128, 'tl_class'=>'w50'),
      'save_callback' => array(
        array('sioweb.glossar.dca.glossar', 'generateAlias')
      )
    ),
    'language' => array
    (
      'label'                   => &$GLOBALS['TL_LANG']['tl_glossar']['language'],
      'exclude'                 => true,
      'search'                  => true,
      'inputType'               => 'text',
      'eval'                    => array('mandatory'=>true, 'maxlength'=>255, 'tl_class'=>'w50'),
    ),
   'fallback' => array
    (
      'label'                   => &$GLOBALS['TL_LANG']['tl_glossar']['fallback'],
      'exclude'                 => true,
      'inputType'               => 'checkbox',
      'eval'                    => array('doNotCopy'=>true, 'tl_class'=>'w50 m12'),
      'save_callback' => array(
        array('sioweb.glossar.dca.glossar', 'checkFallback')
      ),
    ),
    'allowComments' => array
    (
      'label'                   => &$GLOBALS['TL_LANG']['tl_glossar']['allowComments'],
      'exclude'                 => true,
      'filter'                  => true,
      'inputType'               => 'checkbox',
      'eval'                    => array('submitOnChange'=>true,'tl_class'=>'w50'),
    ),
    'notify' => array
    (
      'label'                   => &$GLOBALS['TL_LANG']['tl_glossar']['notify'],
      'default'                 => 'notify_admin',
      'exclude'                 => true,
      'inputType'               => 'select',
      'options'                 => array('notify_admin', 'notify_author', 'notify_both'),
      'reference'               => &$GLOBALS['TL_LANG']['tl_glossar'],
    ),
    'sortOrder' => array
    (
      'label'                   => &$GLOBALS['TL_LANG']['tl_glossar']['sortOrder'],
      'default'                 => 'ascending',
      'exclude'                 => true,
      'inputType'               => 'select',
      'options'                 => array('ascending', 'descending'),
      'reference'               => &$GLOBALS['TL_LANG']['MSC'],
      'eval'                    => array('tl_class'=>'w50'),
    ),
    'perPage' => array
    (
      'label'                   => &$GLOBALS['TL_LANG']['tl_glossar']['perPage'],
      'exclude'                 => true,
      'inputType'               => 'text',
      'eval'                    => array('rgxp'=>'natural', 'tl_class'=>'w50'),
    ),
    'moderate' => array
    (
      'label'                   => &$GLOBALS['TL_LANG']['tl_glossar']['moderate'],
      'exclude'                 => true,
      'inputType'               => 'checkbox',
      'eval'                    => array('tl_class'=>'w50'),
    ),
    'bbcode' => array
    (
      'label'                   => &$GLOBALS['TL_LANG']['tl_glossar']['bbcode'],
      'exclude'                 => true,
      'inputType'               => 'checkbox',
      'eval'                    => array('tl_class'=>'w50'),
    ),
    'requireLogin' => array
    (
      'label'                   => &$GLOBALS['TL_LANG']['tl_glossar']['requireLogin'],
      'exclude'                 => true,
      'inputType'               => 'checkbox',
      'eval'                    => array('tl_class'=>'w50'),
    ),
    'disableCaptcha' => array
    (
      'label'                   => &$GLOBALS['TL_LANG']['tl_glossar']['disableCaptcha'],
      'exclude'                 => true,
      'inputType'               => 'checkbox',
      'eval'                    => array('tl_class'=>'w50'),
    ),
    'seo' => array
    (
      'label'                   => &$GLOBALS['TL_LANG']['tl_glossar']['seo'],
      'exclude'                 => true,
      'filter'                  => true,
      'sorting'                 => true,
      'inputType'               => 'checkbox',
      'eval'                    => array('submitOnChange'=>true),
    ),
    'term_in_title_tag' => array
    (
      'label'                   => &$GLOBALS['TL_LANG']['tl_glossar']['term_in_title_tag'],
      'exclude'                 => true,
      'inputType'               => 'checkbox',
      'eval'                    => array('tl_class'=>'w50 clr'),
    ),
    'term_in_title_str_tag' => array
    (
      'label'                   => &$GLOBALS['TL_LANG']['tl_glossar']['term_in_title_str_tag'],
      'exclude'                 => true,
      'inputType'               => 'text',
      'eval'                    => array('maxlength'=>255,'tl_class'=>'w50 clr','gsIgnore'=>true),
    ),
    'replace_pageTitle' => array
    (
      'label'                   => &$GLOBALS['TL_LANG']['tl_glossar']['replace_pageTitle'],
      'exclude'                 => true,
      'inputType'               => 'checkbox',
      'eval'                    => array('tl_class'=>'w50 clr'),
    ),
    'term_description_tag' => array
    (
      'label'                   => &$GLOBALS['TL_LANG']['tl_glossar']['term_description_tag'],
      'exclude'                 => true,
      'inputType'               => 'text',
      'eval'                    => array('maxlength'=>255,'tl_class'=>'long clr','gsIgnore'=>true),
    ),
  )
);
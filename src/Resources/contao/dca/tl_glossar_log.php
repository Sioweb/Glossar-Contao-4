<?php

/**
 * Contao Open Source CMS
 */

/**
 * @file tl_glossar_log.php
 * @author Sascha Weidner
 * @version 3.0.0
 * @package sioweb.contao.extensions.glossar
 * @copyright Sascha Weidner, Sioweb
 */

$GLOBALS['TL_DCA']['tl_glossar_log'] = array(

  // Config
  'config' => array
  (
    'dataContainer'               => 'Table',
    'switchToEdit'                => true,
    'enableVersioning'            => true
  ),

  // List
  'list' => array
  (
    'sorting' => array
    (
      'mode'                    => 4,
      'fields'                  => array('type,title'),
      'headerFields'            => array('title','language','tstamp'),
      'child_record_callback'   => array('tl_glossar_log', 'listTerms'),
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
        'label'               => &$GLOBALS['TL_LANG']['tl_glossar_log']['edit'],
        'href'                => 'act=edit',
        'icon'                => 'edit.gif',
      ),
      'delete' => array
      (
        'label'               => &$GLOBALS['TL_LANG']['tl_glossar_log']['delete'],
        'href'                => 'act=delete',
        'icon'                => 'delete.gif',
        'attributes'          => 'onclick="if(!confirm(\'' . $GLOBALS['TL_LANG']['MSC']['deleteConfirm'] . '\'))return false;Backend.getScrollOffset()"'
      ),
    )
  ),

  // Palettes
  'palettes' => array
  (
    '__selector__'                => array('type'),
    'default'                     => '{title_legend},user,action',
  ),

  // Fields
  'fields' => array
  (
    'user' => array
    (
      'label'                   => &$GLOBALS['TL_LANG']['tl_glossar_log']['user'],
      'exclude'                 => true,
      'inputType'               => 'text',
      'eval'                    => array('mandatory'=>true, 'maxlength'=>255, 'tl_class'=>'w50'),
    ),
    'pid' => array
    (
      'label'                   => &$GLOBALS['TL_LANG']['tl_glossar_log']['term'],
      'exclude'                 => true,
      'inputType'               => 'text',
      'foreignKey'              => 'tl_sw_glossar.title',
      'eval'                    => array('mandatory'=>true, 'maxlength'=>255, 'tl_class'=>'w50'),
      'relation'                => array('type'=>'belongsTo', 'load'=>'eager')
    ),
    'page' => array
    (
      'label'                   => &$GLOBALS['TL_LANG']['tl_glossar_log']['page'],
      'exclude'                 => true,
      'inputType'               => 'text',
      'eval'                    => array('mandatory'=>true, 'maxlength'=>255, 'tl_class'=>'w50'),
    ),
    'host' => array
    (
      'label'                   => &$GLOBALS['TL_LANG']['tl_glossar_log']['host'],
      'exclude'                 => true,
      'inputType'               => 'text',
      'eval'                    => array('mandatory'=>true, 'maxlength'=>255, 'tl_class'=>'w50'),
    ),
    'language' => array
    (
      'label'                   => &$GLOBALS['TL_LANG']['tl_glossar_log']['language'],
      'exclude'                 => true,
      'inputType'               => 'text',
      'eval'                    => array('mandatory'=>true, 'maxlength'=>255, 'tl_class'=>'w50'),
    ),
    'action' => array
    (
      'label'                   => &$GLOBALS['TL_LANG']['tl_glossar_log']['action'],
      'exclude'                 => true,
      'inputType'               => 'text',
      'eval'                    => array('mandatory'=>true, 'maxlength'=>255, 'tl_class'=>'w50'),
    ),
  )
);
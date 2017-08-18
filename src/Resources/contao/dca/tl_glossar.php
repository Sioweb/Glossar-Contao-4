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
    'enableVersioning'            => true,
    'sql' => array
    (
      'keys' => array
      (
        'id' => 'primary'
      )
    )
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
        'icon'                => 'edit.gif',
      ),
      'editheader' => array
      (
        'label'               => &$GLOBALS['TL_LANG']['tl_glossar']['editheader'],
        'href'                => 'act=edit',
        'icon'                => 'header.gif',
      ),
      'copy' => array
      (
        'label'               => &$GLOBALS['TL_LANG']['tl_glossar']['copy'],
        'href'                => 'act=copy',
        'icon'                => 'copy.gif',
        'attributes'          => 'onclick="Backend.getScrollOffset()"',
        'button_callback'     => array('tl_glossar', 'copyPage')
      ),
      'delete' => array
      (
        'label'               => &$GLOBALS['TL_LANG']['tl_glossar']['delete'],
        'href'                => 'act=delete',
        'icon'                => 'delete.gif',
        'attributes'          => 'onclick="if(!confirm(\'' . $GLOBALS['TL_LANG']['MSC']['deleteConfirm'] . '\'))return false;Backend.getScrollOffset()"'
      ),
      'export' => array
      (
        'label'               => &$GLOBALS['TL_LANG']['MSC']['export'],
        'href'                => 'key=exportTerms',
        'icon'                => 'theme_export.gif',
        'class'               => 'header_edit_all',
      )
    )
  ),

  // Palettes
  'palettes' => array
  (
    '__selector__'                => array('allowComments'),
    'default'                     => '{title_legend},title,alias,language,fallback,allowComments',
  ),

  // Subpalettes
  'subpalettes' => array
  (
    'allowComments'               => 'notify,sortOrder,perPage,moderate,bbcode,requireLogin,disableCaptcha'
  ),

  // Fields
  'fields' => array
  (
    'id' => array
    (
      'sql'                     => "int(10) unsigned NOT NULL auto_increment"
    ),
    'tstamp' => array
    (
      'sql'                     => "int(10) unsigned NOT NULL default '0'"
    ),
    'title' => array
    (
      'label'                   => &$GLOBALS['TL_LANG']['tl_glossar']['title'],
      'exclude'                 => true,
      'inputType'               => 'text',
      'eval'                    => array('mandatory'=>true, 'maxlength'=>255, 'tl_class'=>'w50'),
      'sql'                     => "varchar(255) NOT NULL default ''"
    ),
    'alias' => array
    (
      'label'                   => &$GLOBALS['TL_LANG']['tl_glossar']['alias'],
      'exclude'                 => true,
      'inputType'               => 'text',
      'search'                  => true,
      'eval'                    => array('rgxp'=>'alias', 'doNotCopy'=>true, 'maxlength'=>128, 'tl_class'=>'w50'),
      'sql'                     => "varchar(255) NOT NULL default ''",
      'save_callback' => array(
        array('tl_glossar', 'generateAlias')
      )
    ),
    'language' => array
    (
      'label'                   => &$GLOBALS['TL_LANG']['tl_glossar']['language'],
      'exclude'                 => true,
      'search'                  => true,
      'inputType'               => 'text',
      'eval'                    => array('mandatory'=>true, 'maxlength'=>255, 'tl_class'=>'w50'),
      'sql'                     => "varchar(255) NOT NULL default 'de'"
    ),
   'fallback' => array
    (
      'label'                   => &$GLOBALS['TL_LANG']['tl_glossar']['fallback'],
      'exclude'                 => true,
      'inputType'               => 'checkbox',
      'eval'                    => array('doNotCopy'=>true, 'tl_class'=>'w50 m12'),
      'save_callback' => array(
        array('tl_glossar', 'checkFallback')
      ),
      'sql'                     => "char(1) NOT NULL default ''"
    ),
    'allowComments' => array
    (
      'label'                   => &$GLOBALS['TL_LANG']['tl_glossar']['allowComments'],
      'exclude'                 => true,
      'filter'                  => true,
      'inputType'               => 'checkbox',
      'eval'                    => array('submitOnChange'=>true,'tl_class'=>'w50'),
      'sql'                     => "char(1) NOT NULL default ''"
    ),
    'notify' => array
    (
      'label'                   => &$GLOBALS['TL_LANG']['tl_glossar']['notify'],
      'default'                 => 'notify_admin',
      'exclude'                 => true,
      'inputType'               => 'select',
      'options'                 => array('notify_admin', 'notify_author', 'notify_both'),
      'reference'               => &$GLOBALS['TL_LANG']['tl_glossar'],
      'sql'                     => "varchar(16) NOT NULL default ''"
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
      'sql'                     => "varchar(32) NOT NULL default ''"
    ),
    'perPage' => array
    (
      'label'                   => &$GLOBALS['TL_LANG']['tl_glossar']['perPage'],
      'exclude'                 => true,
      'inputType'               => 'text',
      'eval'                    => array('rgxp'=>'natural', 'tl_class'=>'w50'),
      'sql'                     => "smallint(5) unsigned NOT NULL default '0'"
    ),
    'moderate' => array
    (
      'label'                   => &$GLOBALS['TL_LANG']['tl_glossar']['moderate'],
      'exclude'                 => true,
      'inputType'               => 'checkbox',
      'eval'                    => array('tl_class'=>'w50'),
      'sql'                     => "char(1) NOT NULL default ''"
    ),
    'bbcode' => array
    (
      'label'                   => &$GLOBALS['TL_LANG']['tl_glossar']['bbcode'],
      'exclude'                 => true,
      'inputType'               => 'checkbox',
      'eval'                    => array('tl_class'=>'w50'),
      'sql'                     => "char(1) NOT NULL default ''"
    ),
    'requireLogin' => array
    (
      'label'                   => &$GLOBALS['TL_LANG']['tl_glossar']['requireLogin'],
      'exclude'                 => true,
      'inputType'               => 'checkbox',
      'eval'                    => array('tl_class'=>'w50'),
      'sql'                     => "char(1) NOT NULL default ''"
    ),
    'disableCaptcha' => array
    (
      'label'                   => &$GLOBALS['TL_LANG']['tl_glossar']['disableCaptcha'],
      'exclude'                 => true,
      'inputType'               => 'checkbox',
      'eval'                    => array('tl_class'=>'w50'),
      'sql'                     => "char(1) NOT NULL default ''"
    ),
  )
);

class tl_glossar extends Backend {

  /**
   * Auto-generate an article alias if it has not been set yet
   * @param mixed
   * @param \DataContainer
   * @return string
   * @throws \Exception
   */
  public function generateAlias($varValue, DataContainer $dc) {
    $autoAlias = false; 

    // Generate an alias if there is none
    if($varValue == '') {
      $autoAlias = true;
      $varValue = standardize(StringUtil::restoreBasicEntities($dc->activeRecord->title));
    }

    $objAlias = $this->Database->prepare("SELECT id FROM tl_glossar WHERE id=? OR alias=?")
                   ->execute($dc->id, $varValue);

    // Check whether the page alias exists
    if($objAlias->numRows > 1) {
      if(!$autoAlias) {
        throw new Exception(sprintf($GLOBALS['TL_LANG']['ERR']['aliasExists'], $varValue));
      }

      $varValue .= '-' . $dc->id;
    }
    return $varValue;
  }

  /**
   * Make sure there is only one fallback per domain (thanks to Andreas Schempp)
   *
   * @param mixed         $varValue
   * @param DataContainer $dc
   *
   * @return mixed
   *
   * @throws Exception
   */
  public function checkFallback($varValue, DataContainer $dc) {
    if($varValue == '') {
      return '';
    }

    $objPage = $this->Database->prepare("SELECT id FROM tl_glossar WHERE fallback=1 AND id!=?")
                  ->execute($dc->activeRecord->id);

    if($objPage->numRows) {
      throw new Exception($GLOBALS['TL_LANG']['ERR']['multipleGlossarFallback']);
    }

    return $varValue;
  }

  /**
   * Return the copy page button
   *
   * @param array  $row
   * @param string $href
   * @param string $label
   * @param string $title
   * @param string $icon
   * @param string $attributes
   * @param string $table
   *
   * @return string
   */
  public function copyPage($row, $href, $label, $title, $icon, $attributes, $table) {
    if($GLOBALS['TL_DCA'][$table]['config']['closed']) {
      return '';
    }

    return '<a href="'.$this->addToUrl($href.'&amp;id='.$row['id']).'" title="'.specialchars($title).'"'.$attributes.'>'.Image::getHtml($icon, $label).'</a> ';
  }

  /**
   * Return the copy page with subpages button
   *
   * @param array  $row
   * @param string $href
   * @param string $label
   * @param string $title
   * @param string $icon
   * @param string $attributes
   * @param string $table
   *
   * @return string
   */
  public function copyPageWithSubpages($row, $href, $label, $title, $icon, $attributes, $table) {
    if($GLOBALS['TL_DCA'][$table]['config']['closed']) {
      return '';
    }

    $objSubpages = $this->Database->prepare("SELECT * FROM tl_page WHERE pid=?")
                    ->limit(1)
                    ->execute($row['id']);

    return '<a href="'.$this->addToUrl($href.'&amp;id='.$row['id']).'" title="'.specialchars($title).'"'.$attributes.'>'.Image::getHtml($icon, $label).'</a> ';
  }
}
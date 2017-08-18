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
    'ptable'                      => 'tl_glossar',
    'ctable'                      => array('tl_content'),
    'switchToEdit'                => true,
    'enableVersioning'            => true,
    'sql' => array
    (
      'keys' => array
      (
        'id' => 'primary',
        'pid' => 'index'
      )
    )
  ),

  // List
  'list' => array
  (
    'sorting' => array
    (
      'mode'                    => 4,
      'fields'                  => array('type,title'),
      'headerFields'            => array('title','language','tstamp'),
      'child_record_callback'   => array('tl_sw_glossar', 'listTerms'),
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
        'icon'                => 'edit.gif',
        'button_callback'     => array('tl_sw_glossar', 'editTerm'),
      ),
      'editheader' => array
      (
        'label'               => &$GLOBALS['TL_LANG']['tl_sw_glossar']['editheader'],
        'href'                => 'act=edit',
        'icon'                => 'header.gif',
      ),
      'copy' => array
      (
        'label'               => &$GLOBALS['TL_LANG']['tl_sw_glossar']['copy'],
        'href'                => 'act=copy',
        'icon'                => 'copy.gif'
      ),
      'cut' => array
      (
        'label'               => &$GLOBALS['TL_LANG']['tl_sw_glossar']['cut'],
        'href'                => 'act=paste&amp;mode=cut',
        'icon'                => 'cut.gif'
      ),
      'delete' => array
      (
        'label'               => &$GLOBALS['TL_LANG']['tl_sw_glossar']['delete'],
        'href'                => 'act=delete',
        'icon'                => 'delete.gif',
        'attributes'          => 'onclick="if(!confirm(\'' . $GLOBALS['TL_LANG']['MSC']['deleteConfirm'] . '\'))return false;Backend.getScrollOffset()"'
      ),
      'show' => array
      (
        'label'               => &$GLOBALS['TL_LANG']['tl_sw_glossar']['show'],
        'href'                => 'act=show',
        'icon'                => 'show.gif'
      )
    )
  ),

  // Palettes
  'palettes' => array
  (
    '__selector__'                => array('type','source','seo'),
    'default'                     => '{title_legend},type,title,alias,maxWidth,maxHeight,ignoreInTags,date,noPlural,strictSearch,termAsHeadline,jumpTo,teaser,description;{seo_legend},seo',
    'abbr'                        => '{title_legend},type,title,alias,ignoreInTags,explanation,source'
  ),

  'subpalettes' => array
  (
    'seo' => 'term_in_title_tag,term_description_tag,term_in_title_str_tag',
    'source_page' => 'jumpTo',
    'source_external' => 'url,target'
  ),

  // Fields
  'fields' => array
  (
    'id' => array
    (
     'sql'                     => "int(10) unsigned NOT NULL auto_increment"
    ),
    'pid' => array
    (
      'sql'                     => "int(10) unsigned NOT NULL default '1'",
      'foreignKey'              => 'tl_glossar.id',
      'relation'                => array('type'=>'hasOne','load'=>'lazy')
    ),
    'tstamp' => array
    (
      'sql'                     => "int(10) unsigned NOT NULL default '0'"
    ),
    'type' => array
    (
      'label'                   => &$GLOBALS['TL_LANG']['tl_sw_glossar']['type'],
      'default'                 => 'alias',
      'inputType'               => 'select',
      'options'                 => array_keys($GLOBALS['glossar']['types']),
      'reference'               => &$GLOBALS['glossar']['types'],
      'eval'                    => array('tl_class'=>'w50 clr long','submitOnChange'=>true,'gsIgnore'=>true),
      'sql'                     => "varchar(20) NOT NULL default ''"
    ),
    'title' => array
    (
      'label'                   => &$GLOBALS['TL_LANG']['tl_sw_glossar']['title'],
      'exclude'                 => true,
      'inputType'               => 'text',
      'eval'                    => array('mandatory'=>true,'maxlength'=>255,'tl_class'=>'w50','gsIgnore'=>true),
      'sql'                     => "varchar(255) NOT NULL default ''"
    ),
    'alias' => array
    (
      'label'                   => &$GLOBALS['TL_LANG']['tl_sw_glossar']['alias'],
      'exclude'                 => true,
      'inputType'               => 'text',
      'search'                  => true,
      'eval'                    => array('rgxp'=>'alias','doNotCopy'=>true,'maxlength'=>128,'tl_class'=>'w50','gsIgnore'=>true),
      'sql'                     => "varchar(255) NOT NULL default ''",
      'save_callback' => array
      (
        array('tl_sw_glossar', 'generateAlias')
      )
    ),
    'jumpTo' => array
    (
      'label'                   => &$GLOBALS['TL_LANG']['tl_sw_glossar']['jumpTo'],
      'exclude'                 => true,
      'inputType'               => 'pageTree',
      'foreignKey'              => 'tl_page.title',
      'eval'                    => array('fieldType'=>'radio','tl_class'=>'w50 clr','gsLabel'=>'jumpToGlossar'),
      'sql'                     => "int(10) unsigned NOT NULL default '0'",
      'relation'                => array('type'=>'belongsTo','load'=>'lazy')
    ),
    'maxWidth' => array
    (
      'label'                   => &$GLOBALS['TL_LANG']['tl_sw_glossar']['maxWidth'],
      'exclude'                 => true,
      'inputType'               => 'text',
      'eval'                    => array('maxlength'=>255, 'tl_class'=>'w50','gsLabel'=>'glossarMaxWidth'),
      'sql'                     => "varchar(255) NOT NULL default ''"
    ),
    'maxHeight' => array
    (
      'label'                   => &$GLOBALS['TL_LANG']['tl_sw_glossar']['maxHeight'],
      'exclude'                 => true,
      'inputType'               => 'text',
      'eval'                    => array('maxlength'=>255, 'tl_class'=>'w50','gsLabel'=>'glossarMaxHeight'),
      'sql'                     => "varchar(255) NOT NULL default ''"
    ),
    'ignoreInTags' => array
    (
      'label'                   => &$GLOBALS['TL_LANG']['tl_sw_glossar']['ignoreInTags'],
      'exclude'                 => true,
      'inputType'               => 'text',
      'eval'                    => array('maxlength'=>255,'tl_class'=>'clr long'),
      'sql'                     => "text NULL"
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
      'sql'                     => "int(10) unsigned NOT NULL default '0'"
    ),
    'noPlural' => array
    (
      'label'                   => &$GLOBALS['TL_LANG']['tl_sw_glossar']['noPlural'],
      'exclude'                 => true,
      'inputType'               => 'checkbox',
      'eval'                    => array('tl_class'=>'w50 clr'),
      'sql'                     => "char(1) NOT NULL default ''"
    ),
    'strictSearch' => array
    (
      'label'                   => &$GLOBALS['TL_LANG']['tl_sw_glossar']['strictSearch'],
      'default'                 => 'alias',
      'inputType'               => 'select',
      'options'                 => array_keys($GLOBALS['glossar']['strictSearch']),
      'reference'               => &$GLOBALS['glossar']['strictSearch'],
      'eval'                    => array('tl_class'=>'w50 clr','includeBlankOption'=>true),
      'sql'                     => "varchar(20) NOT NULL default ''"
    ),
    'termAsHeadline' => array
    (
      'label'                   => &$GLOBALS['TL_LANG']['tl_sw_glossar']['termAsHeadline'],
      'exclude'                 => true,
      'inputType'               => 'checkbox',
      'eval'                    => array('tl_class'=>'w50 clr'),
      'sql'                     => "char(1) NOT NULL default ''"
    ),
    'teaser' => array
    (
      'label'                   => &$GLOBALS['TL_LANG']['tl_sw_glossar']['teaser'],
      'exclude'                 => true,
      'search'                  => true,
      'inputType'               => 'textarea',
      'eval'                    => array('rte'=>'tinyMCE','tl_class'=>'clr long','gsIgnore'=>true),
      'sql'                     => "text NULL"
    ),
    'description' => array
    (
      'label'                   => &$GLOBALS['TL_LANG']['tl_sw_glossar']['description'],
      'exclude'                 => true,
      'search'                  => true,
      'inputType'               => 'textarea',
      'eval'                    => array('rte'=>'tinyMCE','style'=>'height: 50px;','tl_class'=>'clr long','gsIgnore'=>true),
      'sql'                     => "text NULL"
    ),
    'explanation' => array
    (
      'label'                   => &$GLOBALS['TL_LANG']['tl_sw_glossar']['explanation'],
      'exclude'                 => true,
      'search'                  => true,
      'inputType'               => 'text',
      'eval'                    => array('maxlength'=>255,'tl_class'=>'clr long','gsIgnore'=>true),
      'sql'                     => "varchar(255) NOT NULL default ''"
    ),
    'source' => array
    (
      'label'                   => &$GLOBALS['TL_LANG']['tl_sw_glossar']['source'],
      'default'                 => 'alias',
      'inputType'               => 'select',
      'options'                 => array_keys($GLOBALS['glossar']['sources']),
      'reference'               => &$GLOBALS['glossar']['sources'],
      'eval'                    => array('tl_class'=>'w50 clr','includeBlankOption'=>true,'submitOnChange'=>true,'gsIgnore'=>true),
      'sql'                     => "varchar(20) NOT NULL default ''"
    ),
    'url' => array
    (
      'label'                   => &$GLOBALS['TL_LANG']['tl_sw_glossar']['url'],
      'exclude'                 => true,
      'search'                  => true,
      'inputType'               => 'text',
      'eval'                    => array('maxlength'=>255, 'tl_class'=>'clr','gsIgnore'=>true),
      'sql'                     => "varchar(255) NOT NULL default ''"
    ),
    'target' => array
    (
      'label'                   => &$GLOBALS['TL_LANG']['MSC']['target'],
      'exclude'                 => true,
      'inputType'               => 'checkbox',
      'eval'                    => array('tl_class'=>'w50 m12','gsIgnore'=>true),
      'sql'                     => "char(1) NOT NULL default ''"
    ),
    'seo' => array
    (
      'label'                   => &$GLOBALS['TL_LANG']['tl_sw_glossar']['seo'],
      'exclude'                 => true,
      'inputType'               => 'checkbox',
      'eval'                    => array('submitOnChange'=>true),
      'sql'                     => "char(1) NOT NULL default ''"
    ),
    'term_in_title_tag' => array
    (
      'label'                   => &$GLOBALS['TL_LANG']['tl_sw_glossar']['term_in_title_tag'],
      'exclude'                 => true,
      'inputType'               => 'checkbox',
      'eval'                    => array('tl_class'=>'w50 clr'),
      'sql'                     => "char(1) NOT NULL default ''"
    ),
    'term_in_title_str_tag' => array
    (
      'label'                   => &$GLOBALS['TL_LANG']['tl_sw_glossar']['term_in_title_str_tag'],
      'exclude'                 => true,
      'inputType'               => 'text',
      'eval'                    => array('maxlength'=>255,'tl_class'=>'w50','gsIgnore'=>true),
      'sql'                     => "varchar(255) NOT NULL default ''"
    ),
    'replace_pageTitle' => array
    (
      'label'                   => &$GLOBALS['TL_LANG']['tl_sw_glossar']['replace_pageTitle'],
      'exclude'                 => true,
      'inputType'               => 'checkbox',
      'eval'                    => array('tl_class'=>'w50 clr'),
      'sql'                     => "char(1) NOT NULL default ''"
    ),
    'term_description_tag' => array
    (
      'label'                   => &$GLOBALS['TL_LANG']['tl_sw_glossar']['term_description_tag'],
      'exclude'                 => true,
      'inputType'               => 'text',
      'eval'                    => array('maxlength'=>255,'tl_class'=>'w50','gsIgnore'=>true),
      'sql'                     => "varchar(255) NOT NULL default ''"
    ),
    'tags' => array
    (
      'label'                   => &$GLOBALS['TL_LANG']['tl_sw_glossar']['tags'],
      'inputType'               => 'tag',
      'eval'                    => array('tl_class'=>'clr long'),
      'sql'                     => "char(1) NOT NULL default ''",
    ),
  )
);

if(in_array('tags', $this->Config->getActiveModules())) {
  foreach($GLOBALS['TL_DCA']['tl_sw_glossar']['palettes'] as $palette => &$fields) {
    if(is_array($fields)) {
      continue;
    }
    $fields = str_replace('alias','alias,tags',$fields);
  }
  unset($fields);
}

class tl_sw_glossar extends Backend {

  public function editTerm($row, $href, $label, $title, $icon, $attributes) {
    if(empty($row['type']) || $row['type'] == 'default' || $row['type'] == 'glossar') {
      return '<a href="'.$this->addToUrl($href.'&amp;id='.$row['id']).'" title="'.specialchars($title).'"'.$attributes.'>'.Image::getHtml($icon, $label).'</a> ';
    }
    return '';
  }

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

    $objAlias = $this->Database->prepare("SELECT id FROM tl_sw_glossar WHERE (id=? OR alias=?) AND pid = ?")
                   ->execute($dc->id, $varValue,$dc->activeRecord->pid);

    // Check whether the page alias exists
    if($objAlias->numRows > 1) {
      if(!$autoAlias) {
        throw new Exception(sprintf($GLOBALS['TL_LANG']['ERR']['aliasExists'], $varValue));
      }

      $varValue .= '-' . $dc->id;
    }

    return $varValue;
  }

  public function listTerms($arrRow) {
    return '<div class="tl_content_left">' . $arrRow['title'] . '</div>';
  }
}
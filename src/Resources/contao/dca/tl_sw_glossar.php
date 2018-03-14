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
    'ctable'                      => array('tl_content'),
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
      'flag'                    => 2,
      'fields'                  => array('title'),
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
        'button_callback'     => array('tl_sw_glossar', 'toggleIcon')
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
    '__selector__'                => array('type','source','seo','published'),
    'default'                     => '{title_legend},type,title,alias;{teaser_legend},teaser;{more_legend},ignoreInTags,maxWidth,maxHeight,strictSearch,date,noPlural,termAsHeadline;{source_legend},source;{seo_legend},seo;{publish_legend},published',
    'abbr'                        => '{title_legend},type,title,alias,ignoreInTags,explanation;{source_legend},source;{publish_legend},published'
  ),

  'subpalettes' => array
  (
    'seo'                 => 'term_in_title_tag,term_description_tag,term_in_title_str_tag',
    'source_page'         => 'jumpTo',
    'source_internal'     => 'jumpTo',
    'source_article'      => 'articleId',
    'source_external'     => 'url,target',
    'published'           => 'start,stop'
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
      'default'                 => 'default',
      'exclude'                 => true,
      'filter'                  => true,
      'inputType'               => 'select',
      'options'                 => array_keys((array)$GLOBALS['TL_LANG']['glossar']['types']),
      'reference'               => &$GLOBALS['TL_LANG']['glossar']['types'],
      'eval'                    => array('tl_class'=>'w50 clr long', 'chosen'=>true, 'submitOnChange'=>true),
      'sql'                     => "varchar(64) NOT NULL default ''"
    ),
    'title' => array
    (
      'label'                   => &$GLOBALS['TL_LANG']['tl_sw_glossar']['title'],
      'inputType'               => 'text',
      'exclude'                 => true,
      'filter'                  => true,
      'sorting'                 => true,
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
    'source' => array
    (
      'label'                   => &$GLOBALS['TL_LANG']['tl_sw_glossar']['source'],
      'default'                 => 'default',
      'exclude'                 => true,
      'filter'                  => true,
      'inputType'               => 'radio',
      'options_callback'        => array('tl_sw_glossar', 'getSourceOptions'),
      'reference'               => &$GLOBALS['TL_LANG']['glossar']['sources'],
      'eval'                    => array('submitOnChange'=>true, 'helpwizard'=>true),
      'sql'                     => "varchar(12) NOT NULL default ''"
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
    'articleId' => array
    (
      'label'                   => &$GLOBALS['TL_LANG']['tl_sw_glossar']['articleId'],
      'exclude'                 => true,
      'inputType'               => 'select',
      'options_callback'        => array('tl_sw_glossar', 'getArticleAlias'),
      'eval'                    => array('chosen'=>true, 'mandatory'=>true),
      'sql'                     => "int(10) unsigned NOT NULL default '0'"
    ),
    'url' => array
    (
      'label'                   => &$GLOBALS['TL_LANG']['MSC']['url'],
      'exclude'                 => true,
      'search'                  => true,
      'inputType'               => 'text',
      'eval'                    => array('mandatory'=>true, 'decodeEntities'=>true, 'maxlength'=>255, 'tl_class'=>'w50'),
      'sql'                     => "varchar(255) NOT NULL default ''"
    ),
    'target' => array
    (
      'label'                   => &$GLOBALS['TL_LANG']['MSC']['target'],
      'exclude'                 => true,
      'inputType'               => 'checkbox',
      'eval'                    => array('tl_class'=>'w50 m12'),
      'sql'                     => "char(1) NOT NULL default ''"
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
      'options'                 => array_keys((array)$GLOBALS['TL_LANG']['glossar']['strictSearch']),
      'reference'               => &$GLOBALS['TL_LANG']['glossar']['strictSearch'],
      'eval'                    => array('tl_class'=>'w50','includeBlankOption'=>true),
      'sql'                     => "varchar(20) NOT NULL default ''"
    ),
    'termAsHeadline' => array
    (
      'label'                   => &$GLOBALS['TL_LANG']['tl_sw_glossar']['termAsHeadline'],
      'exclude'                 => true,
      'inputType'               => 'checkbox',
      'eval'                    => array('tl_class'=>'w50'),
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
      'filter'                  => true,
      'sorting'                 => true,
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
    'published' => array
    (
      'label'                   => &$GLOBALS['TL_LANG']['tl_sw_glossar']['published'],
      'exclude'                 => true,
      'filter'                  => true,
      'flag'                    => 1,
      'inputType'               => 'checkbox',
      'eval'                    => array('submitOnChange'=>true, 'doNotCopy'=>true),
      'sql'                     => "char(1) NOT NULL default '1'"
    ),
    'start' => array
    (
      'label'                   => &$GLOBALS['TL_LANG']['tl_sw_glossar']['start'],
      'exclude'                 => true,
      'inputType'               => 'text',
      'eval'                    => array('rgxp'=>'datim', 'datepicker'=>true, 'tl_class'=>'w50 wizard'),
      'sql'                     => "varchar(10) NOT NULL default ''"
    ),
    'stop' => array
    (
      'label'                   => &$GLOBALS['TL_LANG']['tl_sw_glossar']['stop'],
      'exclude'                 => true,
      'inputType'               => 'text',
      'eval'                    => array('rgxp'=>'datim', 'datepicker'=>true, 'tl_class'=>'w50 wizard'),
      'sql'                     => "varchar(10) NOT NULL default ''"
    )
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

class tl_sw_glossar extends Backend {


  /**
   * Import the back end user object
   */
  public function __construct()
  {
    parent::__construct();
    $this->import('BackendUser', 'User');
  }

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
                   ->execute($dc->id, $varValue, $dc->activeRecord->pid);

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


  /**
   * Return the "toggle visibility" button
   *
   * @param array  $row
   * @param string $href
   * @param string $label
   * @param string $title
   * @param string $icon
   * @param string $attributes
   *
   * @return string
   */
  public function toggleIcon($row, $href, $label, $title, $icon, $attributes) {
    if(\strlen(Input::get('tid'))) {
      $this->toggleVisibility(Input::get('tid'), (Input::get('state') == 1), (@func_get_arg(12) ?: null));
      $this->redirect($this->getReferer());
    }

    // Check permissions AFTER checking the tid, so hacking attempts are logged
    if(!$this->User->hasAccess('tl_sw_glossar::published', 'alexf')) {
      return '';
    }

    $href .= '&amp;tid='.$row['id'].'&amp;state='.($row['published'] ? '' : 1);

    if(!$row['published']) {
      $icon = 'invisible.gif';
    }

    return '<a href="'.$this->addToUrl($href).'" title="'.StringUtil::specialchars($title).'"'.$attributes.'>'.Image::getHtml($icon, $label, 'data-state="' . ($row['published'] ? 1 : 0) . '"').'</a> ';
  }


  /**
   * Disable/enable a user group
   *
   * @param integer       $intId
   * @param boolean       $blnVisible
   * @param DataContainer $dc
   */
  public function toggleVisibility($intId, $blnVisible, DataContainer $dc=null)
  {
    // Set the ID and action
    Input::setGet('id', $intId);
    Input::setGet('act', 'toggle');

    if ($dc)
    {
      $dc->id = $intId;
    }

    // Trigger the onload_callback
    if (\is_array($GLOBALS['TL_DCA']['tl_sw_glossar']['config']['onload_callback']))
    {
      foreach ($GLOBALS['TL_DCA']['tl_sw_glossar']['config']['onload_callback'] as $callback)
      {
        if (\is_array($callback))
        {
          $this->import($callback[0]);
          $this->{$callback[0]}->{$callback[1]}($dc);
        }
        elseif (\is_callable($callback))
        {
          $callback($dc);
        }
      }
    }

    // Check the field access
    if (!$this->User->hasAccess('tl_sw_glossar::published', 'alexf'))
    {
      throw new Contao\CoreBundle\Exception\AccessDeniedException('Not enough permissions to publish/unpublish glossar item ID ' . $intId . '.');
    }

    // Set the current record
    if ($dc)
    {
      $objRow = $this->Database->prepare("SELECT * FROM tl_sw_glossar WHERE id=?")
                   ->limit(1)
                   ->execute($intId);

      if ($objRow->numRows)
      {
        $dc->activeRecord = $objRow;
      }
    }

    $objVersions = new Versions('tl_sw_glossar', $intId);
    $objVersions->initialize();

    // Trigger the save_callback
    if (\is_array($GLOBALS['TL_DCA']['tl_sw_glossar']['fields']['published']['save_callback']))
    {
      foreach ($GLOBALS['TL_DCA']['tl_sw_glossar']['fields']['published']['save_callback'] as $callback)
      {
        if (\is_array($callback))
        {
          $this->import($callback[0]);
          $blnVisible = $this->{$callback[0]}->{$callback[1]}($blnVisible, $dc);
        }
        elseif (\is_callable($callback))
        {
          $blnVisible = $callback($blnVisible, $dc);
        }
      }
    }

    $time = time();

    // Update the database
    $this->Database->prepare("UPDATE tl_sw_glossar SET tstamp=$time, published='" . ($blnVisible ? '1' : '') . "' WHERE id=?")
             ->execute($intId);

    if ($dc)
    {
      $dc->activeRecord->tstamp = $time;
      $dc->activeRecord->published = ($blnVisible ? '1' : '');
    }

    // Trigger the onsubmit_callback
    if (\is_array($GLOBALS['TL_DCA']['tl_sw_glossar']['config']['onsubmit_callback']))
    {
      foreach ($GLOBALS['TL_DCA']['tl_sw_glossar']['config']['onsubmit_callback'] as $callback)
      {
        if (\is_array($callback))
        {
          $this->import($callback[0]);
          $this->{$callback[0]}->{$callback[1]}($dc);
        }
        elseif (\is_callable($callback))
        {
          $callback($dc);
        }
      }
    }

    $objVersions->create();
  }


  /**
   * Add the source options depending on the allowed fields (see #5498)
   *
   * @param DataContainer $dc
   *
   * @return array
   */
  public function getSourceOptions(DataContainer $dc)
  {
    return array('default_source', 'page', 'internal', 'article', 'external');
  }


  /**
   * Get all articles and return them as array
   *
   * @param DataContainer $dc
   *
   * @return array
   */
  public function getArticleAlias(DataContainer $dc)
  {
    $arrPids = array();
    $arrAlias = array();

    if (!$this->User->isAdmin)
    {
      foreach ($this->User->pagemounts as $id)
      {
        $arrPids[] = $id;
        $arrPids = array_merge($arrPids, $this->Database->getChildRecords($id, 'tl_page'));
      }

      if (empty($arrPids))
      {
        return $arrAlias;
      }

      $objAlias = $this->Database->prepare("SELECT a.id, a.title, a.inColumn, p.title AS parent FROM tl_article a LEFT JOIN tl_page p ON p.id=a.pid WHERE a.pid IN(". implode(',', array_map('intval', array_unique($arrPids))) .") ORDER BY parent, a.sorting")
                     ->execute($dc->id);
    }
    else
    {
      $objAlias = $this->Database->prepare("SELECT a.id, a.title, a.inColumn, p.title AS parent FROM tl_article a LEFT JOIN tl_page p ON p.id=a.pid ORDER BY parent, a.sorting")
                     ->execute($dc->id);
    }

    if ($objAlias->numRows)
    {
      System::loadLanguageFile('tl_article');

      while ($objAlias->next())
      {
        $arrAlias[$objAlias->parent][$objAlias->id] = $objAlias->title . ' (' . ($GLOBALS['TL_LANG']['COLS'][$objAlias->inColumn] ?: $objAlias->inColumn) . ', ID ' . $objAlias->id . ')';
      }
    }

    return $arrAlias;
  }
}
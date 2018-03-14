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
        'icon'                => 'edit.svg'
      ),
      'editheader' => array
      (
        'label'               => &$GLOBALS['TL_LANG']['tl_glossar']['editheader'],
        'href'                => 'act=edit',
        'icon'                => 'header.svg',
        'button_callback'     => array('tl_glossar', 'editHeader')
      ),
      'copy' => array
      (
        'label'               => &$GLOBALS['TL_LANG']['tl_glossar']['copy'],
        'href'                => 'act=copy',
        'icon'                => 'copy.svg',
        'button_callback'     => array('tl_glossar', 'copyArchive')
      ),
      'delete' => array
      (
        'label'               => &$GLOBALS['TL_LANG']['tl_glossar']['delete'],
        'href'                => 'act=delete',
        'icon'                => 'delete.svg',
        'attributes'          => 'onclick="if(!confirm(\'' . $GLOBALS['TL_LANG']['MSC']['deleteConfirm'] . '\'))return false;Backend.getScrollOffset()"',
        'button_callback'     => array('tl_glossar', 'deleteArchive')
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
   * Import the back end user object
   */
  public function __construct()
  {
    parent::__construct();
    $this->import('BackendUser', 'User');
  }

  /**
   * Check permissions to edit table tl_glossar
   *
   * @throws Contao\CoreBundle\Exception\AccessDeniedException
   */
  public function checkPermission()
  {
    $bundles = System::getContainer()->getParameter('kernel.bundles');

    // HOOK: comments extension required
    if (!isset($bundles['ContaoCommentsBundle']))
    {
      unset($GLOBALS['TL_DCA']['tl_glossar']['fields']['allowComments']);
    }

    if ($this->User->isAdmin)
    {
      return;
    }

    // Set root IDs
    if (empty($this->User->news) || !\is_array($this->User->news))
    {
      $root = array(0);
    }
    else
    {
      $root = $this->User->news;
    }

    $GLOBALS['TL_DCA']['tl_glossar']['list']['sorting']['root'] = $root;

    // Check permissions to add archives
    if (!$this->User->hasAccess('create', 'glossarp'))
    {
      $GLOBALS['TL_DCA']['tl_glossar']['config']['closed'] = true;
    }

    /** @var Symfony\Component\HttpFoundation\Session\SessionInterface $objSession */
    $objSession = System::getContainer()->get('session');

    // Check current action
    switch (Input::get('act'))
    {
      case 'create':
      case 'select':
        // Allow
        break;

      case 'edit':
        // Dynamically add the record to the user profile
        if (!\in_array(Input::get('id'), $root))
        {
          /** @var Symfony\Component\HttpFoundation\Session\Attribute\AttributeBagInterface $objSessionBag */
          $objSessionBag = $objSession->getBag('contao_backend');

          $arrNew = $objSessionBag->get('new_records');

          if (\is_array($arrNew['tl_glossar']) && \in_array(Input::get('id'), $arrNew['tl_glossar']))
          {
            // Add the permissions on group level
            if ($this->User->inherit != 'custom')
            {
              $objGroup = $this->Database->execute("SELECT id, glossar, glossarp FROM tl_user_group WHERE id IN(" . implode(',', array_map('intval', $this->User->groups)) . ")");

              while ($objGroup->next())
              {
                $arrGlossarp = StringUtil::deserialize($objGroup->glossarp);

                if (\is_array($arrGlossarp) && \in_array('create', $arrGlossarp))
                {
                  $arrGlossar = StringUtil::deserialize($objGroup->glossar, true);
                  $arrGlossar[] = Input::get('id');

                  $this->Database->prepare("UPDATE tl_user_group SET glossar=? WHERE id=?")
                           ->execute(serialize($arrGlossar), $objGroup->id);
                }
              }
            }

            // Add the permissions on user level
            if ($this->User->inherit != 'group')
            {
              $objUser = $this->Database->prepare("SELECT glossar, glossarp FROM tl_user WHERE id=?")
                             ->limit(1)
                             ->execute($this->User->id);

              $arrGlossarp = StringUtil::deserialize($objUser->glossarp);

              if (\is_array($arrGlossarp) && \in_array('create', $arrGlossarp))
              {
                $arrGlossar = StringUtil::deserialize($objUser->glossar, true);
                $arrGlossar[] = Input::get('id');

                $this->Database->prepare("UPDATE tl_user SET glossar=? WHERE id=?")
                         ->execute(serialize($arrGlossar), $this->User->id);
              }
            }

            // Add the new element to the user object
            $root[] = Input::get('id');
            $this->User->glossar = $root;
          }
        }
        // No break;

      case 'copy':
      case 'delete':
      case 'show':
        if (!\in_array(Input::get('id'), $root) || (Input::get('act') == 'delete' && !$this->User->hasAccess('delete', 'glossarp')))
        {
          throw new Contao\CoreBundle\Exception\AccessDeniedException('Not enough permissions to ' . Input::get('act') . ' glossar ID ' . Input::get('id') . '.');
        }
        break;

      case 'editAll':
      case 'deleteAll':
      case 'overrideAll':
        $session = $objSession->all();
        if (Input::get('act') == 'deleteAll' && !$this->User->hasAccess('delete', 'glossarp'))
        {
          $session['CURRENT']['IDS'] = array();
        }
        else
        {
          $session['CURRENT']['IDS'] = array_intersect((array) $session['CURRENT']['IDS'], $root);
        }
        $objSession->replace($session);
        break;

      default:
        if (\strlen(Input::get('act')))
        {
          throw new Contao\CoreBundle\Exception\AccessDeniedException('Not enough permissions to ' . Input::get('act') . ' glossars.');
        }
        break;
    }
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


  /**
   * Return the edit header button
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
  public function editHeader($row, $href, $label, $title, $icon, $attributes)
  {
    return $this->User->canEditFieldsOf('tl_glossar') ? '<a href="'.$this->addToUrl($href.'&amp;id='.$row['id']).'" title="'.StringUtil::specialchars($title).'"'.$attributes.'>'.Image::getHtml($icon, $label).'</a> ' : Image::getHtml(preg_replace('/\.svg$/i', '_.svg', $icon)).' ';
  }


  /**
   * Return the copy archive button
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
  public function copyArchive($row, $href, $label, $title, $icon, $attributes)
  {
    return $this->User->hasAccess('create', 'glossarp') ? '<a href="'.$this->addToUrl($href.'&amp;id='.$row['id']).'" title="'.StringUtil::specialchars($title).'"'.$attributes.'>'.Image::getHtml($icon, $label).'</a> ' : Image::getHtml(preg_replace('/\.svg$/i', '_.svg', $icon)).' ';
  }


  /**
   * Return the delete archive button
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
  public function deleteArchive($row, $href, $label, $title, $icon, $attributes)
  {
    return $this->User->hasAccess('delete', 'glossarp') ? '<a href="'.$this->addToUrl($href.'&amp;id='.$row['id']).'" title="'.StringUtil::specialchars($title).'"'.$attributes.'>'.Image::getHtml($icon, $label).'</a> ' : Image::getHtml(preg_replace('/\.svg$/i', '_.svg', $icon)).' ';
  }

}
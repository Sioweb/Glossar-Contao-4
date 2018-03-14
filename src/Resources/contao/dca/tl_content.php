<?php

/**
 * Contao Open Source CMS
 */

/**
 * @file tl_content.php
 * @author Sascha Weidner
 * @version 3.0.0
 * @package sioweb.contao.extensions.glossar
 * @copyright Sascha Weidner, Sioweb
 */

/* Contao 3.2 support */
if(empty($GLOBALS['glossar']['headlineUnit'])) {
  $this->loadLanguageFile('default');
}

/**
 * Dynamically add the permission check and parent table
 */

if(Input::get('do') == 'glossar') {
  $GLOBALS['TL_DCA']['tl_content']['config']['ptable'] = 'tl_sw_glossar';
  $GLOBALS['TL_DCA']['tl_content']['list']['sorting']['headerFields'] = array('type','title','jumpTo','tstamp');
}

$GLOBALS['TL_DCA']['tl_content']['palettes']['glossar'] = '{type_legend},type,glossar,sortGlossarBy,termAsHeadline,glossarShowTags,glossarShowTagsDetails;{pagination_legend:hide},perPage;{alphapagination_legend:hide},addAlphaPagination';
$GLOBALS['TL_DCA']['tl_content']['palettes']['glossar_cloud'] = '{type_legend},type,glossar';

$GLOBALS['TL_DCA']['tl_content']['palettes']['__selector__'][] = 'addAlphaPagination';
$GLOBALS['TL_DCA']['tl_content']['palettes']['__selector__'][] = 'termAsHeadline';
$GLOBALS['TL_DCA']['tl_content']['subpalettes']['addAlphaPagination'] = 'addNumericPagination,showAfterChoose,addOnlyTrueLinks,paginationPosition';
$GLOBALS['TL_DCA']['tl_content']['subpalettes']['termAsHeadline'] = 'headlineUnit';

$GLOBALS['TL_DCA']['tl_content']['fields']['glossar'] = array(
  'label'                   => &$GLOBALS['TL_LANG']['tl_content']['glossar'],
  'default'                 => 'alias',
  'inputType'               => 'select',
  'foreignKey'              => 'tl_glossar.title',
  'eval'                    => array('tl_class'=>'w50','includeBlankOption'=>true),
  'sql'                     => "varchar(20) NOT NULL default ''"
);

$GLOBALS['TL_DCA']['tl_content']['fields']['sortGlossarBy'] = array(
  'label'                   => &$GLOBALS['TL_LANG']['tl_content']['sortGlossarBy'],
  'default'                 => 'alias',
  'inputType'               => 'select',
  'options'                 => array('id', 'id_desc', 'date', 'date_desc', 'alias', 'alias_desc' ),
  'reference'               => &$GLOBALS['glossar']['sortGlossarBy'],
  'eval'                    => array('tl_class'=>'w50'),
  'sql'                     => "varchar(20) NOT NULL default ''"
);

$GLOBALS['TL_DCA']['tl_content']['fields']['headlineUnit'] = array(
  'label'                   => &$GLOBALS['TL_LANG']['tl_content']['headlineUnit'],
  'inputType'               => 'select',
  'options'                 => array_keys((array)$GLOBALS['TL_LANG']['glossar']['headlineUnit']),
  'reference'               => &$GLOBALS['TL_LANG']['glossar']['headlineUnit'],
  'eval'                    => array('tl_class'=>'w50 clr'),
  'sql'                     => "varchar(20) NOT NULL default ''"
);

$GLOBALS['TL_DCA']['tl_content']['fields']['addAlphaPagination'] = array(
  'label'                   => &$GLOBALS['TL_LANG']['tl_content']['addAlphaPagination'],
  'exclude'                 => true,
  'inputType'               => 'checkbox',
  'eval'                    => array('submitOnChange'=>true),
  'sql'                     => "char(1) NOT NULL default ''"
);

$GLOBALS['TL_DCA']['tl_content']['fields']['addNumericPagination'] = array(
  'label'                   => &$GLOBALS['TL_LANG']['tl_content']['addNumericPagination'],
  'exclude'                 => true,
  'inputType'               => 'checkbox',
  'sql'                     => "char(1) NOT NULL default ''"
);

$GLOBALS['TL_DCA']['tl_content']['fields']['paginationPosition'] = array(
  'label'                   => &$GLOBALS['TL_LANG']['tl_content']['paginationPosition'],
  'default'                 => 'after',
  'inputType'               => 'select',
  'options'                 => array_keys((array)$GLOBALS['TL_LANG']['glossar']['paginationPositions']),
  'reference'               => &$GLOBALS['TL_LANG']['glossar']['paginationPositions'],
  'eval'                    => array('tl_class'=>'w50 clr'),
  'sql'                     => "varchar(20) NOT NULL default 'after'"
);

$GLOBALS['TL_DCA']['tl_content']['fields']['addOnlyTrueLinks'] = array(
  'label'                   => &$GLOBALS['TL_LANG']['tl_content']['addOnlyTrueLinks'],
  'exclude'                 => true,
  'inputType'               => 'checkbox',
  'eval'                    => array('submitOnChange'=>true),
  'sql'                     => "char(1) NOT NULL default ''"
);

$GLOBALS['TL_DCA']['tl_content']['fields']['showAfterChoose'] = array(
  'label'                   => &$GLOBALS['TL_LANG']['tl_content']['showAfterChoose'],
  'exclude'                 => true,
  'inputType'               => 'checkbox',
  'eval'                    => array('submitOnChange'=>true),
  'sql'                     => "char(1) NOT NULL default ''"
);

$GLOBALS['TL_DCA']['tl_content']['fields']['termAsHeadline'] = array(
  'label'                   => &$GLOBALS['TL_LANG']['tl_content']['termAsHeadline'],
  'exclude'                 => true,
  'inputType'               => 'checkbox',
  'eval'                    => array('tl_class'=>'w50 clr','submitOnChange'=>true),
  'sql'                     => "char(1) NOT NULL default ''"
);

$GLOBALS['TL_DCA']['tl_content']['fields']['glossarShowTags'] = array(
  'label'                   => &$GLOBALS['TL_LANG']['tl_content']['glossarShowTags'],
  'exclude'                 => true,
  'inputType'               => 'checkbox',
  'eval'                    => array('tl_class'=>'w50 clr','submitOnChange'=>true),
  'sql'                     => "char(1) NOT NULL default ''"
);

$GLOBALS['TL_DCA']['tl_content']['fields']['glossarShowTagsDetails'] = array(
  'label'                   => &$GLOBALS['TL_LANG']['tl_content']['glossarShowTagsDetails'],
  'exclude'                 => true,
  'inputType'               => 'checkbox',
  'eval'                    => array('tl_class'=>'w50','submitOnChange'=>true),
  'sql'                     => "char(1) NOT NULL default ''"
);
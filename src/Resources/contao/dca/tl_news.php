<?php

/**
 * Contao Open Source CMS
 */

/**
 * @file tl_news.php
 * @author Sascha Weidner
 * @version 3.0.0
 * @package sioweb.contao.extensions.glossar
 * @copyright Sascha Weidner, Sioweb
 */

$GLOBALS['TL_DCA']['tl_news']['fields']['glossar'] = array(
  'sql' => "text NULL"
);

$GLOBALS['TL_DCA']['tl_news']['fields']['fallback_glossar'] = array(
  'sql' => "text NULL"
);

$GLOBALS['TL_DCA']['tl_news']['fields']['glossar_time'] = array(
  'sql' => "int(10) unsigned NOT NULL default '0'"
);
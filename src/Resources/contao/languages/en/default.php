<?php

/*
 * Contao Open Source CMS
 * Copyright (C) 2005-2012 Leo Feyer
 *
 */

/**
* @file default.php
* @author Sascha Weidner
* @version 3.0.0
* @package sioweb.contao.extensions.glossar
* @copyright Sioweb - Sascha Weidner
*/

$GLOBALS['glossar']['to_article'] = 'Weiterlesen ...';
$GLOBALS['TL_LANG']['glossar']['showAllLabel'] = 'Alle anzeigen';

$GLOBALS['TL_LANG']['CTE']['glossar'] = array('Glossar','Erzeugt eine Liste mit Glossar-Begriffen.');
$GLOBALS['TL_LANG']['CTE']['glossar_cloud'] = array('Glossar-Cloud','Erzeugt eine Liste mit Seiten, auf denen gleiche oder verwandte Begriffe stehen.');

$GLOBALS['TL_LANG']['ERR']['multipleGlossarFallback'] = 'Sie können nur einen Glossar als Fallback einstellen!';

$GLOBALS['TL_LANG']['MSC']['import'] = 'Importieren';
$GLOBALS['TL_LANG']['MSC']['export'] = 'Exportieren';

$GLOBALS['TL_LANG']['tl_maintenance_jobs']['glossar'] = array('Glossar zurücksetzen','Setzt den kompletten Glossar (Seiteninhalte, News, Events, FAQ, ...) zurück. Alle Daten müssen danach neu generiert werden.');

$GLOBALS['glossar']['sortGlossarBy'] = array(
  'id' => 'ID aufsteigend',
  'id_desc' => 'ID absteigend',
  'date' => 'Datum aufsteigend',
  'date_desc' => 'Datum absteigend',
  'alias' => 'Alphabetisch aufsteigend',
  'alias_desc' => 'Alphabetisch absteigend',
);

$GLOBALS['glossar']['sources'] = array(
  'page' => 'Seite',
  'external' => 'Externer Link'
);

$GLOBALS['glossar']['headlineUnit'] = array(
  'h1' => 'H1',
  'h2' => 'H2',
  'h3' => 'H3',
  'h4' => 'H4',
  'h5' => 'H5',
  'h6' => 'H6',
);

$GLOBALS['glossar']['types'] = array(
  'default' => 'Glossar',
  'abbr' => 'ABBR - Abkürzung',
);

$GLOBALS['glossar']['paginationPositions'] = array(
  'both' => 'Über und unter den Begriffen',
  'before' => 'Nur über den Begriffen',
  'after' => 'Nur unter den Begriffen',
);

$GLOBALS['glossar']['strictSearch'] = array(
  '1' => 'Nur alleinstehende Wörter finden (Im schwimmer Becken)',
  '2' => 'Alles finden (Im Schwimmerbecken, im schwimmer Becken, nicht|schwimmer|becken)',
  '3' => 'Begriff kann das Startwort sein (Im Schwimmer|becken, im schwimmer Becken)',
);

$GLOBALS['glossar']['glossar_archives'] = array('news'=>'News','faq'=>'FAQ','events'=>'Events',);

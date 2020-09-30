<?php

/**
 * Contao Open Source CMS
 */

/**
 * @file tl_faq.php
 * @author Sascha Weidner
 * @version 3.0.0
 * @package sioweb.contao.extensions.glossar
 * @copyright Sascha Weidner, Sioweb
 */

$GLOBALS['TL_DCA']['tl_faq']['fields']['glossar'] = [
    'sql' => "text NULL",
];

$GLOBALS['TL_DCA']['tl_faq']['fields']['fallback_glossar'] = [
    'sql' => "text NULL",
];

$GLOBALS['TL_DCA']['tl_faq']['fields']['glossar_time'] = [
    'sql' => "int(10) unsigned NOT NULL default '0'",
];

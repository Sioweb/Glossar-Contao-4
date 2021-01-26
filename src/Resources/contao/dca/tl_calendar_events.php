<?php

/**
 * Contao Open Source CMS
 */

/**
 * @file tl_calendar_events.php
 * @author Sascha Weidner
 * @version 2.3
 * @package sioweb.contao.extensions.glossar
 * @copyright Sascha Weidner, Sioweb
 */

use Contao\CalendarBundle\ContaoCalendarBundle;

if (class_exists(ContaoCalendarBundle::class)) {
	$GLOBALS['TL_DCA']['tl_calendar_events']['fields']['glossar'] = [
		'sql' => "text NULL",
	];

	$GLOBALS['TL_DCA']['tl_calendar_events']['fields']['fallback_glossar'] = [
		'sql' => "text NULL",
	];

	$GLOBALS['TL_DCA']['tl_calendar_events']['fields']['glossar_time'] = [
		'sql' => "int(10) unsigned NOT NULL default '0'",
	];
}

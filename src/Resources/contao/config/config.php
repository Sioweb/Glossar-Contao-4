<?php

/**
 * Contao Open Source CMS
 */

/**
 * @file config.php
 * @author Sascha Weidner
 * @package sioweb.contao.extensions.gitter
 * @copyright Sascha Weidner, Sioweb
 */


$GLOBALS['TL_MODELS']['tl_gitter_license'] = 'Sioweb\Gitter\Models\GitterLicenseModel';

/* Modul in die Backend-Navigation einbinden, für die DCA-Datei /dca/tl_gitter.php */
array_insert($GLOBALS['BE_MOD']['system'], 4, array(
	/* Aus der DCA generieren */
	'gitter' => array(
		'tables' => array('tl_gitter_license'),
		'icon' => 'system/modules/gitter/assets/sioweb16x16.png'
	)
));

array_insert($GLOBALS['TL_CTE'],2,array (
	'gitter' => array (
		'gitter_licenses' => 'Sioweb\Gitter\ContentElements\ContentGitterLicenses',
	),
));
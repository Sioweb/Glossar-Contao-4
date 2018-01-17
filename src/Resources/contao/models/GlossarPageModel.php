<?php

/**
 * Contao Open Source CMS
 */

/**
 * @file GlossarPageModel.php
 * @class GlossarPageModel
 * @author Sascha Weidner
 * @version 3.0.0
 * @package sioweb.contao.extensions.glossar
 * @copyright Sascha Weidner, Sioweb
 */

if(!class_exists('GlossarPageModel')) {
class GlossarPageModel extends PageModel {
	public static function findActiveAndEnabledGlossarPages($arrOptions = array()) {
		$t = static::$strTable;
		$arrValues = array(1,'regular');
		$arrColumns = array("published = ? AND (type = 'root' OR type = ?) AND disableGlossar = 0");
		return static::findBy($arrColumns, $arrValues, $arrOptions);
	}
}}
<?php

/**
 * Contao Open Source CMS
 */

/**
 * @file GlossarModel.php
 * @class GlossarModel
 * @author Sascha Weidner
 * @version 3.0.0
 * @package sioweb.contao.extensions.glossar
 * @copyright Sascha Weidner, Sioweb
 */

if(!class_exists('GlossarModel')) {
class GlossarModel extends \Model {

	/**
	 * Table name
	 * @var string
	 */
	protected static $strTable = 'tl_glossar';

	public static function findAllByAlias($arrAlias, $arrOptions = array()) {
		$t = static::$strTable;
		$arrColumns = array("alias IN('".implode("','", $arrAlias)."')");
		return static::findBy($arrColumns, array(), $arrOptions);
	}
}}
<?php

/**
 * Contao Open Source CMS
 */

/**
 * @file GlossarFaqCategoryModel.php
 * @class GlossarFaqCategoryModel
 * @author Sascha Weidner
 * @version 3.0.0
 * @package sioweb.contao.extensions.glossar
 * @copyright Sascha Weidner, Sioweb
 */

class GlossarFaqCategoryModel extends FaqCategoryModel {
	public static function findByPidsAndInactiveGlossar($arrPid, $arrOptions = array()) {
		$t = static::$strTable;
		$arrColumns = array("$t.id IN('".implode("','", $arrPid)."') AND $t.glossar_disallow = 1");
		return static::findBy($arrColumns, array(), $arrOptions);
	}
}
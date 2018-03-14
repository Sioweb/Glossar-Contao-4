<?php

/**
 * Contao Open Source CMS
 */

/**
 * @file GlossarContentModel.php
 * @class GlossarContentModel
 * @author Sascha Weidner
 * @version 3.0.0
 * @package sioweb.contao.extensions.glossar
 * @copyright Sascha Weidner, Sioweb
 */

if(!class_exists('GlossarContentModel')) {
class GlossarContentModel extends ContentModel {
	public static function findByPidsAndTable($arrPids, $table, $type, $arrOptions = array()) {
		$t = static::$strTable;

		if(empty($arrPids) || empty($table)) {
			return array();
		}

		$time = \Date::floorToMinute();

		$arrValues = array($table);
		$arrColumns = array("pid IN('".implode("','", $arrPids)."') AND ptable = ?".($type !== 'all'?' AND type = ?':''));

		if($type !== 'all') {
			$arrValues[] = $type;
		}

		return static::findBy($arrColumns, $arrValues, $arrOptions);
	}
}}
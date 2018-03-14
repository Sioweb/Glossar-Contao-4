<?php

/**
 * Contao Open Source CMS
 */

/**
 * @file STDModel.php
 * @class STDModel
 * @author Sascha Weidner
 * @version 3.0.0
 * @package sioweb.contao.extensions.glossar
 * @copyright Sascha Weidner, Sioweb
 */

if(!class_exists('StdModel')) {
class StdModel {

	private $arrData = array();

	public function __get($var) {
		if(!empty($this->arrData[$var])) {
			return $this->arrData[$var];
		}
		return null;
	}

	public function __set($var, $val) {
		$this->arrData[$var] = $val;
	}

	public function row() {
		return $this->arrData;
	}
}};
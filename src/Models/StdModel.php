<?php

/**
 * Contao Open Source CMS
 */

declare(strict_types=1);
namespace Sioweb\Glossar\Models;

/**
 * @file STDModel.php
 * @class STDModel
 * @author Sascha Weidner
 * @version 3.0.0
 * @package sioweb.contao.extensions.glossar
 * @copyright Sascha Weidner, Sioweb
 */

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
}
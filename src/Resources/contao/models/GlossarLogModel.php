<?php

/**
 * Contao Open Source CMS
 */

/**
 * @file GlossarLogModel.php
 * @class GlossarLogModel
 * @author Sascha Weidner
 * @version 3.0.0
 * @package sioweb.contao.extensions.glossar
 * @copyright Sascha Weidner, Sioweb
 */


if(!class_exists('GlossarLogModel')) {
class GlossarLogModel extends \Model {

	/**
	 * Table name
	 * @var string
	 */
	protected static $strTable = 'tl_glossar_log';
}}
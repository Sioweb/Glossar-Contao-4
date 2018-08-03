<?php

/**
 * Contao Open Source CMS
 */

namespace Sioweb\Gitter\Classes;
use Contao;

/**
 * @file Gitter.php
 * @class Gitter
 * @author Sascha Weidner
 * @package sioweb.gitter
 * @copyright Sascha Weidner, Sioweb
 */

class Gitter {

	private $varNames = array();
	private $funcNames = array();
	private $usedFuncNames = null;
	private $usedVarNames = null;

	public function __construct() {
		mt_srand(crc32(microtime()));
	}

	public function removeKommentare($str) {
		$i = 0;
		while($i < strlen($str)) {

			$char = $str{$i};

			if($char == '"') {
				$i = $this->gotoNext($str, '"', ($i+1));
				continue;
			} else if($char == "'") {
				$i = $this->gotoNext($str, "'", ($i+1));
				continue;
			} else if($char == "#") {// #-Kommentare
				$end = $this->gotoNext($str, "\n", ($i+1));
				$str = substr($str, 0, $i-1).substr($str, $end);

			} else if($char == "/" && $str{$i+1} == "/") {// //Kommentare
				$end = $this->gotoNext($str, "\n", ($i+1));
				$str = substr($str, 0, $i-1).substr($str, $end);
			} else if($char == "/" && $str{$i+1} == "*") {// /* Kommentare
				$end = $i+1;

				while(strlen($str{$i}) != 0 && $str{$end} != "/") {
					$end = $this->gotoNext($str, "*", $end);
				}

				$str = substr($str, 0, $i-1).substr($str, $end+1);
			}

			$i++;
		}

		return $str;
	}

	public function replaceVariablen($str) {
		$this->varNames = array();
		$i = 0;

		//'...' per base64-kodieren

		$str = preg_replace_callback("/(\")(.*?)\"/si",array($this,"base64_encode_string"),$str);
		$str = preg_replace_callback("/(')(.*?)'/si",array($this,"base64_encode_string"),$str);
		$str = preg_replace_callback("/(\")(.*?)\"/si",array($this,"base64_decode_string"),$str);

		//$erg = preg_match('/\\$([a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*)/si', $str, $treffer, PREG_OFFSET_CAPTURE);
		preg_replace_callback('/\\$([a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*)/si', array($this,"variable"), $str);

		krsort($this->varNames);

		foreach($this->varNames AS $key => $name) {
			$str = str_replace('$'.$key, '$'.$name, $str);
		}

		//'...' dekodieren
		$str = preg_replace_callback("/(\")(.*?)\"/si",array($this,"base64_encode_string"),$str);
		$str = preg_replace_callback("/(')(.*?)'/si",array($this,"base64_decode_string"),$str);
		$str = preg_replace_callback("/(\")(.*?)\"/si",array($this,"base64_decode_string"),$str);

		return $str;
	}

	public function replaceFunction($str) {
		$this->funcNames = array();

		$str = preg_replace_callback("/(\")(.*?)\"/si",array($this,"base64_encode_string"),$str);
		$str = preg_replace_callback("/(')(.*?)'/si",array($this,"base64_encode_string"),$str);

		preg_replace_callback('/private function ([a-zA-Z_][a-zA-Z0-9_-]*)/si', array($this,"functionName"), $str);

		krsort($this->funcNames);

		foreach($this->funcNames AS $key=>$name) {
			$str = str_replace('private function '.$key, 'private function '.$name, $str);
			$str = preg_replace("/".$key."[ ]*\(/si", "$name(", $str);
		}

		$str = preg_replace_callback("/(')(.*?)'/si",array($this,"base64_decode_string"),$str);
		$str = preg_replace_callback("/(\")(.*?)\"/si",array($this,"base64_decode_string"),$str);

		return $str;
	}

	public function removeWhitespaces($str) {
		$sprachkonstrukte = array();
		$sprachkonstrukte[] = "public function";
		$sprachkonstrukte[] = "echo";
		$sprachkonstrukte[] = "class";
		$sprachkonstrukte[] = "private";
		$sprachkonstrukte[] = "protected";
		$sprachkonstrukte[] = "public";
		$sprachkonstrukte[] = "var";
		$sprachkonstrukte[] = "throw";
		$sprachkonstrukte[] = "new";
		$sprachkonstrukte[] = "return";

		$str = preg_replace_callback("/(\")(.*?)\"/si",array($this,"base64_encode_string"),$str);
		$str = preg_replace_callback("/(')(.*?)'/si",array($this,"base64_encode_string"),$str);

		//Leerzeichen
		$str = preg_replace('/([ ]{2,})/si', " ", $str);
		$str = preg_replace('/[ ]*([=.,<>!+;*\/(){}-]+)[ ]*/si', "\\1", $str);

		$str = str_replace("\r", "", $str);
		$str = str_replace("\n", "", $str);
		$str = str_replace("<?php", "<?php ", $str);

		$str = preg_replace_callback("/(')(.*?)'/si",array($this,"base64_decode_string"),$str);
		$str = preg_replace_callback("/(\")(.*?)\"/si",array($this,"base64_decode_string"),$str);

		return $str;
	}

	public function base64_encode_string($arg) {
		return $arg[1].base64_encode($arg[2]).$arg[1];
	}

	public function base64_decode_string($arg) {
		return $arg[1].base64_decode($arg[2]).$arg[1];
	}

	public function variable($arg) {
		$reservierteVariablen = array("_GET", "_POST", "_SESSION", "_COOKIE", "GLOBALS", "_SERVER", "_FILES", "_ENV", "_REQUEST", "this");

		if(!in_array($arg[1], $reservierteVariablen) && $this->varNames[$arg[1]] == null) {
			$this->varNames[$arg[1]] = $this->nextVarName();
		}
	}

	public function functionName($arg) {

		if($this->funcNames[$arg[1]] == null) {
			$this->funcNames[$arg[1]] = $this->nextFuncName();
		}
	}

	public function nextVarName() {

		if($this->usedVarNames == null) {
			$rand = $this->randStr(12);
		} else {
			$rand = key($this->usedVarNames);
			$buchstaben = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
			$buchstaben_lng = strlen($buchstaben)-1;

			do {
				$pos = mt_rand(0, strlen($rand)-1);
				$rand{$pos} = $buchstaben{ mt_rand(0, ($buchstaben_lng - (($pos > 0) ? 0 : 10)) )};
			} while($this->usedVarNames[$rand] != null);

		}

		$this->usedVarNames[$rand] = true;
		return $rand;
	}

	public function nextFuncName() {

		if($this->usedFuncNames == null) {
			$rand = $this->randStr(12);
		} else {
			$rand = key($this->usedFuncNames);
			$buchstaben = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
			$buchstaben_lng = strlen($buchstaben)-1;

			do {
				$pos = mt_rand(0, strlen($rand)-1);
				$rand{$pos} = $buchstaben{ mt_rand(0, ($buchstaben_lng - (($pos > 0) ? 0 : 10)) )};
			} while($this->usedFuncNames[$rand] != null);

		}

		$this->usedFuncNames[$rand] = true;

		return $rand;
	}

	public function randStr($lng) {

		//Welche Buchstaben benutzt werden sollen (Charset)
		$buchstaben = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";

		$str_lng = strlen($buchstaben)-1;
		$rand = $buchstaben{mt_rand(0, ($str_lng-10))};

		for($i=1;$i<$lng;$i++) {
			$rand.= $buchstaben{mt_rand(0, $str_lng)};
		}

		return $rand;
	}

	public function gotoNext($str, $sign, $offset) {
		while(strlen($str{$offset}) != 0)
		{
			$char = $str{$offset};

			if($char == "\\") {
				$offset++;
			} else if($char == $sign) {
				return $offset+1;
			}
			$offset++;
		}
	}
}
<?php

/**
 * Contao Open Source CMS
 */

namespace Sioweb\Glossar\Modules;

use Contao\Input;
use Contao\Module;
use Contao\PageModel;

/**
 * @file ModuleGlossarCloud.php
 * @class ModuleGlossarCloud
 * @author Sascha Weidner
 * @version 3.0.0
 * @package sioweb.contao.extensions.glossar
 * @copyright Sascha Weidner, Sioweb
 */
class Cloud extends Module {

	/**
	 * Template
	 * @var string
	 */
	protected $strTemplate = 'ce_glossar_cloud';

	private $pages = array();

	private $countTerms = array();

	private $maxLevel = 4;

	/**
	 * Return if there are no files
	 * @return string
	 */
	public function generate() {

		global $objPage;

		$this->glossar_disable_domains = deserialize($this->glossar_disable_domains);
		$arrLevels = $this->loadPagesByTerms(array_filter((explode('|', $objPage->glossar))));

		foreach($arrLevels as $level => $arrPages) {
			// Max ist die Anzahl gefundener Seiten pro Begriff
			$max = 0;

			foreach($arrPages as &$p_page) {
				foreach($this->countTerms as $term => $count) {
					if(in_array($term, $p_page['glossar'])) {
						$p_page['weight']++;
					}
				}

				if($p_page['weight'] > $max) {
					$max = $p_page['weight'];
				}
			}

			$this->max = $max;

			foreach($arrPages as &$p_page) {
				$x = $p_page['weight'] / $max;
				$p_page['weight'] = 1 + $x;
			}

			usort($arrPages, function($a, $b) {
				return $a['weight']<$b['weight'];
			});


			if(!empty($this->glossar_items)) {
				$arrPages = array_slice($arrPages,0, $this->glossar_items);
			}

			$this->pages = array_merge($this->pages, $arrPages);
		}


		if(!isset($_GET['items']) && $GLOBALS['TL_CONFIG']['useAutoItem'] && isset($_GET['auto_item'])) {
			Input::setGet('items', Input::get('auto_item'));
		}

		if(!isset($_GET['alpha']) && $GLOBALS['TL_CONFIG']['useAutoItem'] && isset($_GET['auto_item'])) {
			Input::setGet('alpha', Input::get('auto_item'));
		}

		return parent::generate();
	}

	private function loadPagesByTerms($pageGlossar, $level = 0, &$idNotIn = array()) {

		global $objPage;

		if(empty($pageGlossar)) {
			return array();
		}

		$idNotIn[] = $objPage->id;
		if(!empty($this->glossar_disable_domains)) {
			$idNotIn = array_merge($idNotIn, $this->glossar_disable_domains);
		}

		$idNotIn = array_unique($idNotIn);
		$sql = "id NOT IN (".str_repeat("?,",count($idNotIn)-1)."?) AND disableGlossarCloud != 1 AND (glossar LIKE '%%".implode("%%' OR tl_page.glossar LIKE '%%", $pageGlossar)."%%')";

		$Page = PageModel::findBy(array($sql), $idNotIn);

		if(empty($Page)) {
			return array();
		}

		$countTerms = $termLevel = $arrPages = array();

		if(empty($arrPages[$level])) {
			$arrPages[$level] = array();
		}



		while($Page->next()) {
			$_Page = $Page->current()->loadDetails();

			if(!empty($this->glossar_disable_domains)) {
				if(in_array($_Page->rootId, $this->glossar_disable_domains)) {
					continue;
				}
			}

			$idNotIn[] = $_Page->id;

			$ap = array(
				'id' => $_Page->id,
				'weight' => 0,
				'title' => $_Page->title,
				'description' => $_Page->description,
				'glossar' => explode('|', $_Page->glossar),
				'fallback_glossar' => explode('|', $_Page->fallback_glossar),
				'url' => $this->generateFrontendUrl($_Page->row()),
				'level' => $level
			);

			foreach($ap['glossar'] as $term) {
				if($term != '') {
					$countTerms[$term] = !empty($term) ? ($countTerms[$term]+1) : 1;
				}
			}

			if($ap['id'] != $objPage->id) {
				$arrPages[$level][] = $ap;
			}
		}

		asort($countTerms);
		$this->countTerms = array_merge($this->countTerms, $countTerms);

		foreach($arrPages[$level] as $key => $Page) {
			$termLevel = array_merge($termLevel, $Page['glossar']);
		}

		$termLevel = array_diff(array_unique((array_filter($termLevel))), $pageGlossar);

		if($level < $this->glossar_max_level) {
			if(!empty($nextLevelResult)) {
				$arrPages = array_merge($arrPages, $nextLevelResult);
			}
		}

		return array_filter($arrPages);
	}

	public function compile()  {
		if($this->glossar_terms) {
			// echo 'Terms: '.$this->glossar_terms;
		} elseif(Input::get('items') != '') {
			// echo 'Items: '.Input::get('items');
		} else {
			$this->Template->pages = $this->pages;
		}

		$this->Template->unique = count(array_unique($this->countTerms));
		$this->Template->countTerms = $this->countTerms;
	}

	private function getRootPage($id) {
		$Page = PageModel::findByPk($id);

		if(empty($Page)) {
			return;
		}

		if($Page->type == 'root') {
			return $Page;
		}

		return $this->getRootPage($Page->pid);
	}
}
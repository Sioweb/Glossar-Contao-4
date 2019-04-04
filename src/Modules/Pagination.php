<?php

/**
 * Contao Open Source CMS
 */

namespace Sioweb\Glossar\Modules;

use Contao\Input;
use Contao\Module;
use Contao\BackendTemplate;
use Sioweb\Glossar\Models\SwGlossarModel;

/**
 * @file ModuleGlossarPagination.php
 * @class ModuleGlossarPagination
 * @author Sascha Weidner
 * @version 3.0.0
 * @package sioweb.contao.extensions.glossar
 * @copyright Sascha Weidner, Sioweb
 */
class Pagination extends Module {

	/**
	 * Template
	 * @var string
	 */
	protected $strTemplate = 'glossar_pagination';

	/**
	 * Display a wildcard in the back end
	 *
	 * @return string
	 */
	public function generate() {
		$scopeMatcher = $this->getContainer()->get('contao.routing.scope_matcher');
		$requestStack = $this->getContainer()->get('request_stack');
		
        if ($scopeMatcher->isBackendRequest($requestStack->getCurrentRequest())) {
			/** @var BackendTemplate|object $objTemplate */
			$objTemplate = new BackendTemplate('be_wildcard');
			$objTemplate->wildcard = '### Glossar Pagination ###';
			$objTemplate->title = $this->headline;
			$objTemplate->id = $this->id;
			$objTemplate->link = $this->name;
			$objTemplate->href = 'contao/main.php?do=themes&amp;table=tl_module&amp;act=edit&amp;id=' . $this->id;

			return $objTemplate->parse();
		}

		return parent::generate();
	}


	/**
	 * Generate the module
	 */
	protected function compile() {

		global $objPage;

		if(empty($this->glossar)) {
			$Glossar = SwGlossarModel::findAll();
		} else {
			$Glossar = SwGlossarModel::findByPid($this->glossar);
		}

		$filledLetters = array();
		if($Glossar) {
			while ($Glossar->next()) {
				$filledLetters[] = substr($Glossar->alias, 0, 1);
			}
		}

		$letters = array();
		for($c=65;$c<=90;$c++) {
			if(($this->addOnlyTrueLinks && in_array(strtolower(chr($c)), $filledLetters)) || !$this->addOnlyTrueLinks)
				$letters[] = array(
					'href' => $this->addToUrl('pag='.strtolower(chr($c)).'&amp;alpha=&amp;items=&amp;auto_item='),
					'initial' => chr($c),
					'active'=>(Input::get('pag') == strtolower(chr($c))),
					'trueLink'=>(in_array(strtolower(chr($c)), $filledLetters) && !$this->addOnlyTrueLinks)
				);
		}

		$this->Template->showAllHref = $this->generateFrontendUrl($objPage->row());
		$this->Template->showAllLabel = $GLOBALS['TL_LANG']['glossar']['showAllLabel'];
		$this->Template->alphaPagination = $letters;
	}
}


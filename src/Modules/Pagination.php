<?php

/**
 * Contao Open Source CMS
 */

namespace Sioweb\Glossar\Modules;

use Contao\Input;
use Contao\Module;
use Contao\BackendTemplate;
use Sioweb\Glossar\Entity\Glossar as GlossarEntity;
use Sioweb\Glossar\Entity\Terms as TermsEntity;

/**
 * @file ModuleGlossarPagination.php
 * @class ModuleGlossarPagination
 * @author Sascha Weidner
 * @version 2.3
 * @package sioweb.contao.extensions.glossar
 * @copyright Sascha Weidner, Sioweb
 */
class Pagination extends Module
{

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
	public function generate()
	{
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
	protected function compile()
	{

		global $objPage;

		$EntityManager = $this->getContainer()->get('doctrine.orm.default_entity_manager');
		$TermRepository = $EntityManager->getRepository(TermsEntity::class);

		if (empty($this->glossar)) {
			$Glossar = $TermRepository->findAll();
		} else {
			$Glossar = $TermRepository->findBy(['pid' => $this->glossar]);
		}

		$filledLetters = [];
		if ($Glossar) {
			foreach ($Glossar as $glossar) {
				$filledLetters[] = substr($glossar->getAlias(), 0, 1);
			}
		}

		$numbers = $letters = [];

		if ($this->addAlphaPagination) {
			for ($c = 65; $c <= 90; $c++) {
				if (($this->addOnlyTrueLinks && in_array(strtolower(chr($c)), $filledLetters)) || !$this->addOnlyTrueLinks) {
					$letters[] = [
						'href' => $this->addToUrl('pag=' . strtolower(chr($c)) . '&amp;alpha=&amp;items=&amp;auto_item='),
						'initial' => chr($c),
						'active' => (Input::get('pag') == strtolower(chr($c))),
						'trueLink' => (in_array(strtolower(chr($c)), $filledLetters)),
						'onlyTrueLinks' => $this->addOnlyTrueLinks,
					];
				}
			}
		}

		if ($this->addNumericPagination) {
			for ($n = 0; $n < 10; $n++) {
				if (($this->addOnlyTrueLinks && in_array(strtolower((string)$n), $filledLetters)) || !$this->addOnlyTrueLinks) {
					$numbers[] = [
						'href' => $this->addToUrl('pag=' . strtolower((string)$n) . '&amp;alpha=&amp;items=&amp;auto_item='),
						'initial' => $n,
						'active' => (Input::get('pag') == strtolower((string)$n)),
						'trueLink' => (in_array(strtolower((string)$n), $filledLetters)),
						'onlyTrueLinks' => $this->addOnlyTrueLinks,
					];
				}
			}
		}

		$letters[0]['class'] = 'first';
		$letters[count($letters) - 1]['class'] = 'last';

		$numbers[0]['class'] = 'first';
		$numbers[count($numbers) - 1]['class'] = 'last';

		$this->Template->showAllHref = $this->generateFrontendUrl($objPage->row());
		$this->Template->showAllLabel = $GLOBALS['TL_LANG']['glossar']['showAllLabel'];
		$this->Template->alphaPagination = $letters;
		$this->Template->numericPagination = $numbers;
	}
}

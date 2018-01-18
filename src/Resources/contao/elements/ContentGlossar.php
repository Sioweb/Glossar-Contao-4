<?php

/**
 * Contao Open Source CMS
 */
namespace Sioweb;
use Contao;
use Sioweb\License\Glossar as GlossarLicense;

/**
 * @file ContentGlossar.php
 * @class ContentGlossar
 * @author Sascha Weidner
 * @version 3.0.0
 * @package sioweb.contao.extensions.glossar
 * @copyright Sascha Weidner, Sioweb
 */

class ContentGlossar extends \ContentElement {

	/**
	 * Template 
	 * @var string
	 */
	protected $strTemplate = 'ce_glossar';

	private $license = false;

	/**
	 * Return if there are no files
	 * @return string
	 */
	public function generate() {

		if(class_exists('Sioweb\License\Glossar')) {
			$this->license = new GlossarLicense();
		}

		if(!isset($_GET['items']) && \Config::get('useAutoItem') && isset($_GET['auto_item'])) {
			\Input::setGet('items', \Input::get('auto_item'));
		}

		if(!isset($_GET['alpha']) && \Config::get('useAutoItem') && isset($_GET['auto_item'])) {
			\Input::setGet('alpha', \Input::get('auto_item'));
		}

		return parent::generate();
	}
	
	public function compile()  {
		global $objPage;
		$this->loadLanguageFile('glossar_errors');
		$glossarErrors = array();


		if(empty($this->headlineUnit)) {
			$this->headlineUnit = 'h2';
		}

		if(!$this->sortGlossarBy) {
			$this->sortGlossarBy = 'alias';
		}

		$this->sortGlossarBy = explode('_', $this->sortGlossarBy);
		$this->sortGlossarBy = $this->sortGlossarBy[0].($this->sortGlossarBy[1] ? ' '.strtoupper($this->sortGlossarBy[1]) : '');

		if(\Input::get('items') == '') {
			if(empty($this->glossar)) {
				$Glossar = \SwGlossarModel::findAll(array('order'=>$this->sortGlossarBy));
			} else {
				$Glossar = \SwGlossarModel::findByPid($this->glossar,array('order'=>$this->sortGlossarBy));
			}
		} else {
			$Glossar = \SwGlossarModel::findByAlias(\Input::get('items'),array(),array('order'=>$this->sortGlossarBy));
		}

		/* Gefundene Begriffe durch Links zum Glossar ersetzen */
		$arrGlossar = array();
		$filledLetters = array();
		if($Glossar) {

			if(\Input::get('items') == '') {
				$arrGlossarIDs = array();

				while($Glossar->next()) {
					$arrGlossarIDs[] = $Glossar->id;
				}

				if(in_array('tags', \Config::getInstance()->getActiveModules())) {
					$arrTags = $this->Database->prepare("SELECT * FROM tl_tag WHERE from_table = ? AND tid IN ('".implode("','", $arrGlossarIDs)."') ORDER BY tag ASC");
				}
			} else {
				if(in_array('tags', \Config::getInstance()->getActiveModules())) {
					$arrTags = $this->Database->prepare("SELECT * FROM tl_tag WHERE from_table = ? AND tid = ".intval($Glossar->id)." ORDER BY tag ASC");
				}
			}

			if(!empty($arrTags)) {
				$arrTags = $arrTags->execute('tl_sw_glossar')->fetchAllAssoc();

				$_arrTags = array();
				foreach ($arrTags as $key => $value) {
					$_arrTags[$value['tid']][] = $value;
				}

				$arrTags = $_arrTags;
				unset($_arrTags);
			}

			$Glossar->reset();
			while($Glossar->next()) {
				$initial = substr(str_replace('id-','',$Glossar->alias),0,1);
				$filledLetters[] = $initial;
				if(\Input::get('items') != '' || (!$this->showAfterChoose || !$this->addAlphaPagination) || ($this->addAlphaPagination && $this->showAfterChoose && \Input::get('pag') != '')) {
					if(\Input::get('pag') == '' || $initial == \Input::get('pag') ) {

						$newGlossarObj = new \FrontendTemplate('glossar_default');
						$newGlossarObj->setData($Glossar->row());

						if(!empty($arrTags) && !empty($arrTags[$newGlossarObj->id])) {
							$newGlossarObj->showTags = $this->glossarShowTags;
							$newGlossarObj->tags = $arrTags[$newGlossarObj->id];
						}

						// if(\Input::get('items') != '') {
						//   $newGlossarObj->teaser = null;
						// }

						$link = null;
						$Content = \ContentModel::findPublishedByPidAndTable($newGlossarObj->id,'tl_sw_glossar');
						if(!empty($Content) || (!empty($Glossar->teaser) && \Config::get('acceptTeasersAsContent'))) {
							if(!$newGlossarObj->jumpTo || $newGlossarObj->source != 'page') {
								$newGlossarObj->jumpTo = \Config::get('jumpToGlossar');
							}

							if($newGlossarObj->jumpTo) {
								$link = \PageModel::findByPk($newGlossarObj->jumpTo);
							}

							if($link) {
								$newGlossarObj->link = $this->generateFrontendUrl($link->row(), ((\Config::get('useAutoItem') && !\Config::get('disableAlias')) ?  '/' : '/items/').$newGlossarObj->alias);
							}
						} else {
							$newGlossarObj->link = false;
						}

						if(\Input::get('items') == '') {
							$arrGlossar[] = $newGlossarObj->parse();
						} else {
							if(!empty($arrTags) && !empty($arrTags[$Glossar->id])) {
								$this->Template->showTags = $this->glossarShowTags && $this->glossarShowTagsDetails;
								$this->Template->tags = $arrTags[$Glossar->id];
							}

							$this->addCommentsToTerm($Glossar);
							$elements = $this->getGlossarElements($newGlossarObj->id);

							if(empty($elements) && $Glossar->description) {
								$descriptionObj = new \FrontendTemplate('glossar_description');
								$descriptionObj->content = $Glossar->description;
								$elements = array($descriptionObj->parse());
							}
							if(empty($elements)) {
								$elements = [$Glossar->teaser];
							}
							$arrGlossar[] = $elements;
						}
					}
				}
			}
		}

		$numbers = $letters = array();

		if($this->addAlphaPagination) {
			for($c=65;$c<=90;$c++) {
				if(($this->addOnlyTrueLinks && in_array(strtolower(chr($c)), $filledLetters)) || !$this->addOnlyTrueLinks) {
					$letters[] = array(
						'href' => $this->addToUrl('pag='.strtolower(chr($c)).'&amp;alpha=&amp;items=&amp;auto_item='),
						'initial' => chr($c),
						'active'=>(\Input::get('pag') == strtolower(chr($c))),
						'trueLink'=>(in_array(strtolower(chr($c)), $filledLetters)),
						'onlyTrueLinks'=>$this->addOnlyTrueLinks
					);
				}
			}
		}

		if($this->addNumericPagination) {
			for($n=0;$n<10;$n++) {
				if(($this->addOnlyTrueLinks && in_array(strtolower($n), $filledLetters)) || !$this->addOnlyTrueLinks) {
					$numbers[] = array(
						'href' => $this->addToUrl('pag='.strtolower($n).'&amp;alpha=&amp;items=&amp;auto_item='),
						'initial' => $n,
						'active'=>(\Input::get('pag') == strtolower($n)),
						'trueLink'=>(in_array(strtolower($n), $filledLetters)),
						'onlyTrueLinks'=>$this->addOnlyTrueLinks
					);
				}
			}
		}

		$letters[0]['class'] = 'first';
		$letters[count($letters)-1]['class'] = 'last';

		$numbers[0]['class'] = 'first';
		$numbers[count($numbers)-1]['class'] = 'last';
		
		$objPagination = new \FrontendTemplate('glossar_pagination');

		if($objPage) {
			$objPagination->showAllHref = $this->generateFrontendUrl($objPage->row());
			$objPagination->showAllLabel = $GLOBALS['TL_LANG']['glossar']['showAllLabel'];
			$objPagination->alphaPagination = $letters;
			$objPagination->numericPagination = $numbers;
			$strAlphaPagination = $objPagination->parse();
		}

		$this->Template->alphaPagination = $strAlphaPagination;

		if(\Input::get('items') != '' || (!$this->showAfterChoose || !$this->addAlphaPagination) || ($this->addAlphaPagination && $this->showAfterChoose && \Input::get('pag') != '')) {
			if(!$arrGlossar && $GLOBALS['glossar']['errors']['no_content']) {
				$glossarErrors[] = $GLOBALS['glossar']['errors']['no_content'];
			}
		}

		$termAsHeadline = false;
		if(\Input::get('items') != '') {
			if(($this->termAsHeadline || \Config::get('termAsHeadline')) && !$Glossar->termAsHeadline) {
				$Headline = new \StdModel();
				$Headline->headline = serialize(array('value'=>$Glossar->title,'unit'=>$this->headlineUnit));
				// $Headline->cssID = serialize(array('','glossar_headline'));
				$Headline->type = 'glossar_headline';
				$objHeadline = new \ContentHeadline($Headline);
				$termAsHeadline = $objHeadline->generate();
			}

			$this->Template->termAsHeadline = $termAsHeadline;
			$this->Template->content = 1;
			$arrGlossar = array_shift($arrGlossar);

			if($Glossar->seo) {
				if($Glossar->term_in_title_tag) {
					$Title = $objPage->pageTitle;

					if(empty($Glossar->term_in_title_str_tag)) {
						$pageTitle = strip_tags(strip_insert_tags($Glossar->title));
					} else {
						$pageTitle = strip_tags($this->replaceInsertTags($Glossar->term_in_title_str_tag));
					}
					$objPage->pageTitle = $pageTitle;
				}

				if($Glossar->term_description_tag) {
					$objPage->description = $this->prepareMetaDescription($Glossar->term_description_tag);
				}
			}
		}

		$this->Template->ppos = $this->paginationPosition;
		$this->Template->glossar = $arrGlossar;
		$this->Template->back = $GLOBALS['TL_LANG']['MSC']['goBack'];

		if($glossarErrors) {
			$errorObj = new \FrontendTemplate('glossar_error');
			$errorObj->msg = $glossarErrors;
			$this->Template->errors = $errorObj->parse();
		}
	}

	private function addCommentsToTerm($term) {
		$objGlossar = $term->getRelated('pid');
		$this->Template->allowComments = $objGlossar->allowComments;

		// Comments are not allowed
		if(!$objGlossar->allowComments) {
			return;
		}

		// Adjust the comments headline level
		$intHl = min(intval(str_replace('h', '', $this->hl)), 5);
		$this->Template->hlc = 'h' . ($intHl + 1);

		$this->import('Comments');
		$arrNotifies = array();

		// Notify the system administrator
		if($objGlossar->notify != 'notify_author') {
			$arrNotifies[] = $GLOBALS['TL_ADMIN_EMAIL'];
		}

		// Notify the author
		if($objGlossar->notify != 'notify_admin') {
			/** @var \UserModel $objAuthor */
			if(($objAuthor = $objArticle->getRelated('author')) !== null && $objAuthor->email != '') {
				$arrNotifies[] = $objAuthor->email;
			}
		}

		$objConfig = new \stdClass();
		$objConfig->perPage = $objGlossar->perPage;
		$objConfig->order = $objGlossar->sortOrder;
		$objConfig->template = $this->com_template;
		$objConfig->requireLogin = $objGlossar->requireLogin;
		$objConfig->disableCaptcha = $objGlossar->disableCaptcha;
		$objConfig->bbcode = $objGlossar->bbcode;
		$objConfig->moderate = $objGlossar->moderate;

		$this->Comments->addCommentsToTemplate($this->Template, $objConfig, 'tl_sw_glossar', $term->id, $arrNotifies);
	}

	private function getGlossarElements($id) {
		$arrElements = array();
		$objCte = \ContentModel::findPublishedByPidAndTable($id, 'tl_sw_glossar');

		if($objCte !== null) {
			$intCount = 0;
			$intLast = $objCte->count() - 1;

			while ($objCte->next()) {
				$arrCss = array();
				/** @var \ContentModel $objRow */
				$objRow = $objCte->current();

				// Add the "first" and "last" classes (see #2583)
				if($intCount == 0 || $intCount == $intLast) {
					if($intCount == 0) {
						$arrCss[] = 'first';
					}

					if($intCount == $intLast) {
						$arrCss[] = 'last';
					}
				}

				$objRow->classes = $arrCss;
				$arrElements[] = $this->getContentElement($objRow, $this->strColumn);
				++$intCount;
			}
		}
		return $arrElements;
	}
}
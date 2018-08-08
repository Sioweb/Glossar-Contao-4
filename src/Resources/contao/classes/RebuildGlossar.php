<?php

/**
 * Contao Open Source CMS
 */
namespace Sioweb;
use Contao;

/**
 * @file RebuildGlossar.php
 * @class RebuildGlossar
 * @author Sascha Weidner
 * @version 3.0.0
 * @package sioweb.contao.extensions.glossar
 * @copyright Sascha Weidner, Sioweb
 */

class RebuildGlossar extends \Backend implements \executable {

	private function replaceGlossarTags($strContent, $tags = array()) {
			// Strip non-indexable areas
			while (($intStart = strpos($strContent, $tags[1])) !== false) {
				if(($intEnd = strpos($strContent, $tags[0], $intStart)) !== false) {
					$intCurrent = $intStart;

					// Handle nested tags
					while (($intNested = strpos($strContent, $tags[1], $intCurrent + 22)) !== false && $intNested < $intEnd) {
						if(($intNewEnd = strpos($strContent, $tags[0], $intEnd + 26)) !== false) {
							$intEnd = $intNewEnd;
							$intCurrent = $intNested;
						} else {
							break;
						}
					}

					$strContent = substr($strContent, 0, $intStart) . substr($strContent, $intEnd + 26);
				} else {
					break;
				}
			}

			return $strContent;
	}

	public function prepareRebuild($strContent, $strTemplate) {
		return str_replace(array('<!-- indexer::stop -->','<!-- indexer::continue -->'),array('',''), $strContent);
	}

	public function rebuild($strContent, $arrData) {
		global $objPage;

		$time = \Input::get('time');
		$strContent = preg_replace('#<script(.*?)>(.*?)</script>#is', '', $strContent);
		$strContent = preg_replace('#<style(.*?)>(.*?)</style>#is', '', $strContent);

		$this->import('Database');
		$this->Database->prepare("UPDATE tl_page SET glossar = NULL, fallback_glossar = NULL,glossar_time = ? WHERE glossar_time != ?")->execute($time, $time);

		if(isset($GLOBALS['TL_HOOKS']['clearGlossar']) && is_array($GLOBALS['TL_HOOKS']['clearGlossar'])) {
			foreach ($GLOBALS['TL_HOOKS']['clearGlossar'] as $type => $callback) {
				$this->import($callback[0]);
				$this->{$callback[0]}->{$callback[1]}(\Input::get('time'));
			}
		}

		if(\Config::get('activateGlossarTags') == 1) {
			if(isset($GLOBALS['TL_HOOKS']['beforeGlossarTags']) && is_array($GLOBALS['TL_HOOKS']['beforeGlossarTags'])) {
				foreach ($GLOBALS['TL_HOOKS']['beforeGlossarTags'] as $type => $callback) {
					$this->import($callback[0]);
					$strContent = $this->{$callback[0]}->{$callback[1]}($strContent, $strTemplate);
				}
			}

			$strContent = $this->replaceGlossarTags($strContent,['<!-- glossar::continue -->','<!-- glossar::stop -->']);

			if(isset($GLOBALS['TL_HOOKS']['afterGlossarTags']) && is_array($GLOBALS['TL_HOOKS']['afterGlossarTags'])) {
				foreach ($GLOBALS['TL_HOOKS']['afterGlossarTags'] as $type => $callback) {
					$this->import($callback[0]);
					$strContent = $this->{$callback[0]}->{$callback[1]}($strContent, $strTemplate);
				}
			}
		}

		if(!isset($_GET['items']) && \Config::get('useAutoItem') && isset($_GET['auto_item'])) {
			\Input::setGet('items', \Input::get('auto_item'));
		}

		$Glossar = \GlossarModel::findAll();

		if(empty($Glossar)) {
			$this->clearGlossar();
			return;
		}

		$arrGlossar = array(); 

		while($Glossar->next()) {
			$arrGlossar[$Glossar->id] = $Glossar->language;
		}

		$Term = \SwGlossarModel::findAll(array('order'=>' CHAR_LENGTH(title) DESC'));

		if(empty($Term)) {
			$this->clearGlossar();
			return;
		}

		$arrTerms = array('glossar'=>array(),'fallback'=>array(),'both'=>array()); 

		while($Term->next()) {
			if($arrGlossar[$Term->pid] == $objPage->language) {
				$arrTerms['glossar'][] = $Term->title;
			} else {
				$arrTerms['fallback'][] = $Term->title;
			}
			$arrTerms['both'][] = $Term->title;
		}

		foreach($arrTerms as &$pointer_terms) {
			$pointer_terms = array_unique($pointer_terms);
		}

		// HOOK: take additional pages
		if(isset($GLOBALS['TL_HOOKS']['cacheGlossarTerms']) && is_array($GLOBALS['TL_HOOKS']['cacheGlossarTerms'])) {
			foreach ($GLOBALS['TL_HOOKS']['cacheGlossarTerms'] as $type => $callback) {
				if(\Input::get('rebuild_'.$type.'_glossar') == '1') {
					$this->import($callback[0]);
					$this->{$callback[0]}->{$callback[1]}(\Input::get('items'), $arrTerms, $strContent, $type);
				}
			}
		}

		if(\Input::get('rebuild_glossar') == 1) {
			$strFallback = $strGlossar = '';
			if(!empty($arrTerms['glossar'])) {
				$matches = array();
				foreach($arrTerms['glossar'] as $key => $term) {
					if(preg_match('#'.str_replace('.','\.',html_entity_decode($term)).'#is',strip_tags($strContent))) {
						$matches[] = $term;
					}
				}

				$matches = array_unique(array_map("strtolower", $matches));
				$strGlossar = '|'.implode('|', $matches).'|';
			}

			if(!empty($arrTerms['fallback'])) {
				$matches = array();
				foreach($arrTerms['fallback'] as $key => $term) {
					if(preg_match('#'.str_replace('.','\.', $term).'#is',strip_tags($strContent))) {
						$matches[] = $term;
					}
				}

				$matches = array_unique(array_map("strtolower", $matches));
				$strFallback = '|'.implode('|', $matches).'|';
			}

			$this->Database->prepare("UPDATE tl_page SET glossar = ?, fallback_glossar = ?,glossar_time = ? WHERE id = ?")->execute($strGlossar, $strFallback, $time, $objPage->id);
		}

		return $strContent;
	}

	public function clearGlossar() {
		/** @todo Clear all Glossar caches */
	}

	/**
	 * Return true if the module is active
	 * @return boolean
	 */
	public function isActive() {
		return (\Config::get('enableGlossar') && \Input::get('act') == 'glossar');
	}

	/**
	 * Generate the module
	 * @return string
	 */
	public function run() {
		if(!\Config::get('enableGlossar')) {
			return '';
		}

		$time = time();
		$objTemplate = new \BackendTemplate('be_rebuild_glossar');
		$objTemplate->action = ampersand(\Environment::get('request'));
		$objTemplate->indexHeadline = $GLOBALS['TL_LANG']['tl_maintenance']['glossarIndex'];
		$objTemplate->isActive = $this->isActive();

		// Add the error message
		if($_SESSION['REBUILD_INDEX_ERROR'] != '') {
			$objTemplate->indexMessage = $_SESSION['REBUILD_INDEX_ERROR'];
			$_SESSION['REBUILD_INDEX_ERROR'] = '';
		}

		// Rebuild the index
		if(\Input::get('act') == 'glossar') {
			if(!isset($_GET['rt']) || !\RequestToken::validate(\Input::get('rt'))) {
				$this->Session->set('INVALID_TOKEN_URL', \Environment::get('request'));
				$this->redirect('contao/confirm.php');
			}

			$arrPages['regular'] = $this->findGlossarPages();

			// HOOK: take additional pages
			$InactiveArchives = (array)deserialize(\Config::get('glossar_archive'));
			if(isset($GLOBALS['TL_HOOKS']['getGlossarPages']) && is_array($GLOBALS['TL_HOOKS']['getGlossarPages'])) {
				foreach($GLOBALS['TL_HOOKS']['getGlossarPages'] as $type => $callback) {
					if(in_array($type, $InactiveArchives)) {
						continue;
					}

					$this->import($callback[0]);
					$cb_return = $this->{$callback[0]}->{$callback[1]}($arrPages);

					if(is_numeric($type)) {
						$arrPages[] = $cb_return;
					} else {
						$arrPages[$type] = $cb_return;
					}
				}
			}

			// Truncate the search tables
			$this->import('Automator');
			$this->Automator->purgeSearchTables();

			// Hide unpublished elements
			$this->setCookie('FE_PREVIEW', 0, ($time - 86400));

			// Calculate the hash
			$strHash = sha1(session_id() . (!\Config::get('disableIpCheck') ? \Environment::get('ip') : '') . 'FE_USER_AUTH');

			// Remove old sessions
// 			$this->Database->prepare("DELETE FROM tl_session WHERE tstamp<? OR hash=?")
// 							 ->execute(($time - \Config::get('sessionTimeout')), $strHash);

			// Log in the front end user
			if(is_numeric(\Input::get('user')) && \Input::get('user') > 0) {
				// Insert a new session
// 				$this->Database->prepare("INSERT INTO tl_session (pid, tstamp, name, sessionID, ip, hash) VALUES (?, ?, ?, ?, ?, ?)")
// 								 ->execute(\Input::get('user'), $time, 'FE_USER_AUTH', session_id(), \Environment::get('ip'), $strHash);

				// Set the cookie
				$this->setCookie('FE_USER_AUTH', $strHash, ($time + \Config::get('sessionTimeout')), null, null, false, true);
			} else {
				// Unset the cookies
				$this->setCookie('FE_USER_AUTH', $strHash, ($time - 86400), null, null, false, true);
				$this->setCookie('FE_AUTO_LOGIN', \Input::cookie('FE_AUTO_LOGIN'), ($time - 86400), null, null, false, true);
			}

			$strBuffer = '';
			$rand = rand();
			$time = time();

			foreach($arrPages as $type => $pages) {
				foreach($pages as $lang => $arrPage) {
					for ($i=0, $c=count($arrPage); $i<$c; $i++) {
						$strBuffer .= '<span class="get_'.$type.'_url" data-time="'.$time.'" data-type="'.$type.'" data-language="'.$lang.'" data-url="' . $arrPage[$i] . '#' . $rand . $i . '">' . \StringUtil::substr($arrPage[$i], 100) . '</span><br>';
						unset($arrPages[$type][$lang][$i]);
					}
				}
			}

			$objTemplate->content = $strBuffer;
			$objTemplate->note = $GLOBALS['TL_LANG']['tl_maintenance']['glossarNote'];
			$objTemplate->loading = $GLOBALS['TL_LANG']['tl_maintenance']['glossarLoading'];
			$objTemplate->complete = $GLOBALS['TL_LANG']['tl_maintenance']['glossarComplete'];
			$objTemplate->indexContinue = $GLOBALS['TL_LANG']['MSC']['continue'];
			$objTemplate->theme = \Backend::getTheme();
			$objTemplate->isRunning = true;

			return $objTemplate->parse();
		}

		$arrUser = array(''=>'-');

		// Get active front end users
		$objUser = $this->Database->execute("SELECT id, username FROM tl_member WHERE disable!=1 AND (start='' OR start<$time) AND (stop='' OR stop>$time) ORDER BY username");

		while ($objUser->next()) {
			$arrUser[$objUser->id] = $objUser->username . ' (' . $objUser->id . ')';
		}

		// Default variables
		$objTemplate->user = $arrUser;
		$objTemplate->indexLabel = $GLOBALS['TL_LANG']['tl_maintenance']['frontendUser'][0];
		$objTemplate->indexHelp = (\Config::get('showHelp') && strlen($GLOBALS['TL_LANG']['tl_maintenance']['rebuildGlossarHelp'][1])) ? $GLOBALS['TL_LANG']['tl_maintenance']['rebuildGlossarHelp'][1] : '';
		$objTemplate->indexSubmit = $GLOBALS['TL_LANG']['tl_maintenance']['glossarSubmit'];

		return $objTemplate->parse();
	}

	private function getRootPage($id) {
		$Page = \PageModel::findByPk($id);
		if($Page->type !== 'root') {
			$Page = $this->getRootPage($Page->pid);
		}

		return $Page;
	}

	protected function findGlossarPages() {
		$time = time();
		$arrPages = array();
		$objPages = \GlossarPageModel::findActiveAndEnabledGlossarPages();
		$domain = rtrim(\Environment::get('base'),'/').'/';

		if(!empty($objPages)) {
			while($objPages->next()) {
				if($objPages->pid) {
					$RootPage = $this->getRootPage($objPages->pid);
				}

				if($RootPage->dns) {
					$domain = rtrim('http'.($RootPage->useSSL?'s':'').'://'.str_replace(array('http://','https://'),'', $RootPage->dns),'/').'/';
				} else {
					$domain = rtrim(\Environment::get('base'),'/').'/';
				}
				
				$strLanguage = $RootPage->language;

				if((!$objPages->start || $objPages->start < $time) && (!$objPages->stop || $objPages->stop > $time)) { 
					$arrPages[$strLanguage][] = $domain . static::generateFrontendUrl($objPages->row(), null, $strLanguage);

					$objArticle = \ArticleModel::findBy(array("tl_article.pid=? AND (tl_article.start='' OR tl_article.start<$time) AND (tl_article.stop='' OR tl_article.stop>$time) AND tl_article.published=1 AND tl_article.showTeaser=1"),array($objPages->id),array('order'=>'sorting'));

					if(!empty($objArticle)) {
						while ($objArticle->next()) {
							$arrPages[$strLanguage][] = $domain . static::generateFrontendUrl($objPages->row(), '/articles/' . (($objArticle->alias != '' && !\Config::get('disableAlias')) ? $objArticle->alias : $objArticle->id), $strLanguage);
						}
					}
				}
			}
		}

		return $arrPages;
	}
}

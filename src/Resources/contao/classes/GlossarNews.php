<?php

/**
 * Contao Open Source CMS
 */
namespace Sioweb;
use Contao;

/**
 * @file GlossarNews.php
 * @class GlossarNews
 * @author Sascha Weidner
 * @version 3.0.0
 * @package sioweb.contao.extensions.glossar
 * @copyright Sascha Weidner, Sioweb
 */
class GlossarNews extends \ModuleNews {

	public function __construct() {}

	public function compile() {}

	public function clearGlossar($time) {
		$this->import('Database');
		$this->Database->prepare("UPDATE tl_news SET glossar = NULL,fallback_glossar = NULL,glossar_time = ? WHERE glossar_time != ?")->execute($time, $time);
	}

	public function glossarContent($item, $strContent, $template) {
		if(empty($item)) {
			return array();
		}

		$News = \NewsModel::findByIdOrAlias(\Input::get('items'));
		return $News->glossar;
	}

	public function updateCache($item, $arrTerms, $strContent) {
		$matches = array();
		foreach($arrTerms['both'] as $term) {
			if(preg_match('#('.$term.')#is', $strContent, $match)) {
				$matches[] = $match[1];
			}
		}

		$matches = array_unique($matches);

		if(empty($matches)) {
			return;
		}

		$News = \NewsModel::findByIdOrAlias($item);
		$News->glossar = implode('|', $matches);
		$News->save();
	}

	public function generateUrl($arrPages) {
		$arrPages = array();

		$News = \NewsModel::findAll();
		if(empty($News)) {
			return array();
		}

		$arrNews = array();
		while($News->next()) {
			if(!empty($News)) {
				$arrNews[$News->pid][] = $this->generateNewsUrl($News);
			}
		}

		$InactiveArchives = \GlossarNewsArchiveModel::findByPidsAndInactiveGlossar(array_keys($arrNews));

		if(!empty($InactiveArchives)) {
			while($InactiveArchives->next()) {
				unset($arrNews[$InactiveArchives->id]);
			}
		}

		if(empty($arrNews)) {
			return array();
		}

		$NewsReader = \ModuleModel::findByType('newsreader');

		if(empty($NewsReader)) {
			return array();
		}

		$arrReader = array();

		while($NewsReader->next()) {
			$arrReader[$NewsReader->id] = deserialize($NewsReader->news_archives);
		}

		$Content = \ContentModel::findBy(array("module IN ('".implode("','",array_keys($arrReader))."')"),array());

		if(empty($Content)) {
			return array();
		}

		$_content = $arrContent = array();

		while($Content->next()) {
			$_content[] = $arrContent[$Content->module][] = $Content->pid;
		}


		$Article = \ArticleModel::findBy(array("tl_article.id IN ('".implode("','", $_content)."')"),array());

		if(empty($Article)) {
			return array();
		}

		$finishedIDs = $arrPages = array();

		while($Article->next()) {
			$RootPage = $this->getRootPage($Article->pid);

			if($RootPage->dns) {
				$domain = rtrim('http://'.str_replace(array('http://','https://'),'', $RootPage->dns),'/').'/';
			} else {
				$domain = rtrim(\Environment::get('base'),'/').'/';
			}

			$domain = \Environment::get('base');
			$strLanguage = 'de';
			$objPages = $Article->getRelated('pid');

			$ReaderId = false;

			foreach($arrContent as $module => $mid) {
				if(in_array($Article->id, $mid)) {
					$ReaderId = $module;
				}
			}

			foreach($arrReader[$ReaderId] as $news_id) {
				if(!empty($arrNews[$news_id])) {
					foreach($arrNews[$news_id] as $news_domain) {
						$news_domain = end((explode('/',str_replace('.html','', $news_domain))));
						$arrPages['de'][] = $domain . static::generateFrontendUrl($objPages->row(), '/'.$news_domain, $strLanguage);
					}
				}
				$finishedIDs[] = $news_id;
			}
		}

		return $arrPages;
	}

	private function getRootPage($id) {
		$Page = \PageModel::findByPk($id);
		if($Page->type !== 'root') {
			$Page = $this->getRootPage($Page->pid);
		}
		return $Page;
	}
}
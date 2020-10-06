<?php


/**
 * Contao Open Source CMS
 */

declare(strict_types=1);

namespace Sioweb\Glossar\Classes;

use FOS\HttpCache\ResponseTagger;

/**
 * Provide methods to get all events of a certain period from the database.
 *
 * @property bool $cal_noSpan
 *
 * @author Leo Feyer <https://github.com/leofeyer>
 */
abstract class Terms
{

	/**
	 * Current URL
	 * @var string
	 */
	protected $strUrl;


	/**
	 * Current events
	 * @var array
	 */
	protected $arrTerms = [];

	/**
	 * URL cache array
	 * @var array
	 */
	private static $arrUrlCache = [];

	/**
	 * Generate a URL and return it as string
	 *
	 * @param TermsModel $objTerm
	 * @param boolean             $blnAbsolute
	 *
	 * @return string
	 */
	public static function generateTermUrl($objTerm, $blnAbsolute = false)
	{
		$strCacheKey = 'id_' . $objTerm->getId() . ($blnAbsolute ? '_absolute' : '');

		// Load the URL from cache
		if (isset(self::$arrUrlCache[$strCacheKey])) {
			return self::$arrUrlCache[$strCacheKey];
		}

		// Initialize the cache
		self::$arrUrlCache[$strCacheKey] = null;

		switch ($objTerm->getSource()) {
				// Link to an external page
			case 'external':
				if (substr($objTerm->getUrl(), 0, 7) == 'mailto:') {
					self::$arrUrlCache[$strCacheKey] = \StringUtil::encodeEmail($objTerm->getUrl());
				} else {
					self::$arrUrlCache[$strCacheKey] = ampersand($objTerm->getUrl());
				}
				break;

				// Link to an internal page
			case 'internal':
				if (($objTarget = $objTerm->getRelated()('jumpTo')) instanceof PageModel) {
					/** @var PageModel $objTarget */
					self::$arrUrlCache[$strCacheKey] = ampersand($blnAbsolute ? $objTarget->getAbsoluteUrl() : $objTarget->getFrontendUrl());
				}
				break;

				// Link to an article
			case 'article':
				if (($objArticle = \ArticleModel::findByPk($objTerm->getArticleId())) !== null && ($objPid = $objArticle->getRelated('pid')) instanceof PageModel) {
					$params = '/articles/' . ($objArticle->alias ?: $objArticle->id);

					/** @var PageModel $objPid */
					self::$arrUrlCache[$strCacheKey] = ampersand($blnAbsolute ? $objPid->getAbsoluteUrl($params) : $objPid->getFrontendUrl($params));
				}
				break;
		}

		// Link to the default page
		if (self::$arrUrlCache[$strCacheKey] === null) {
			$objPage = \PageModel::findByPk($objTerm->getRelated()->jumpTo);

			if (!$objPage instanceof PageModel) {
				self::$arrUrlCache[$strCacheKey] = ampersand(\Environment::get('request'));
			} else {
				$params = (\Config::get('useAutoItem') ? '/' : '/events/') . ($objTerm->getAlias() ?: $objTerm->getId());

				self::$arrUrlCache[$strCacheKey] = ampersand($blnAbsolute ? $objPage->getAbsoluteUrl($params) : $objPage->getFrontendUrl($params));
			}
		}

		return self::$arrUrlCache[$strCacheKey];
	}
}

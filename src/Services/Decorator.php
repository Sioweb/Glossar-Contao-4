<?php

/**
 * Contao Open Source CMS
 */

declare (strict_types = 1);

namespace Sioweb\Glossar\Services;

use Contao\Config;
use Contao\FrontendTemplate;
use Contao\Input;
use Contao\PageModel;
use Contao\StringUtil;
use Contao\CoreBundle\Framework\ContaoFramework;
use Sioweb\Glossar\Services\License as GlossarLicense;
use Contao\CoreBundle\Routing\UrlGenerator;
use Sioweb\Glossar\Models\ContentModel as GlossarContentModel;
use Sioweb\Glossar\Models\PageModel as GlossarPageModel;

/**
 * @file Decorator.php
 * @class Decorator
 * @author Sascha Weidner
 * @package sioweb.contao.extensions.glossar
 * @copyright Sascha Weidner, Sioweb
 */

class Decorator
{

    private $term_glossar;

    /**
     * @var UrlGenerator
     */
    private $urlGenerator;

    private static $arrUrlCache = array();

    public function __construct(ContaoFramework $contaoFramework, GlossarLicense $Lizenz, UrlGenerator $urlGenerator) {
        $this->urlGenerator = $urlGenerator;
    }

    /* replace found tags with links and abbr */
    public function replace($strContent, $arrTerm, $Glossar = null)
    {
        global $objPage;
        $this->term_glossar = $Glossar;
        if (!$strContent || empty($arrTerm)) {
            return $strContent;
        }

        // $IgnoreTags = $this->replaceGlossarIgnoreTags($strContent);
        foreach ($arrTerm as $Term) {
            $this->term = (object) $Term->getData();

            if (!$this->term->maxWidth) {
                $this->term->maxWidth = array_filter([Config::get('glossarMaxWidth'), $GLOBALS['glossar']['css']['maxWidth']])[0];
            }

            if (!$this->term->maxHeight) {
                $this->term->maxHeight = array_filter([Config::get('glossarMaxHeight'), $GLOBALS['glossar']['css']['maxHeight']])[0];
            }

            $Content = GlossarContentModel::findPublishedByPidAndTable($this->term->id, 'tl_sw_glossar');

            $replaceFunction = 'replaceTitle2Link';

            if (
                ((!$this->term->source || $this->term->source === 'default') && !Config::get('jumpToGlossar')) || (empty($Content) && empty(Config::get('disableToolTips')))
            ) {
                if (!empty($Content) || (!empty($Term->getTeaser()) && Config::get('acceptTeasersAsContent'))) {
                    $replaceFunction = 'replaceTitle2Link';

                    if (!$this->term->jumpTo || $this->term->source != 'page') {
                        $this->term->jumpTo = Config::get('jumpToGlossar');
                    }

                    if ($this->term->jumpTo) {
                        $link = \PageModel::findByPk($this->term->jumpTo);
                    }
                    $this->term->content = 1;
                    if ($link) {
                        $this->term->link = $link->getAbsoluteUrl('/' . $this->term->alias);
                    }
                } else {
                    $replaceFunction = 'replaceTitle2Span';
                }
            }

            $ignoredTags = array('a');
            if (Config::get('ignoreInTags')) {
                $ignoredTags = explode(',', str_replace(' ', '', Config::get('ignoreInTags')));
            }

            if ($this->term->ignoreInTags) {
                $ignoredTags = explode(',', str_replace(' ', '', $this->term->ignoreInTags));
            }

            if (Config::get('strictSearch') !== null && empty($this->term->strictSearch)) {
                $this->term->strictSearch = Config::get('strictSearch');
            }

            if (empty($this->term->type) || $this->term->type == 'default' || $this->term->type == 'glossar') {
                $IllegalPlural = '';

                $lastIstDot = false;
                if (substr($this->term->title, -1) === '.') {
                    $strContent = str_replace($this->term->title, $this->term->title . 'X', $strContent);
                    $this->term->title .= 'X';
                    $lastIstDot = true;
                }
                
                if (!empty($this->term->illegalChars)) {
                    $IllegalPlural = $this->term->illegalChars;
                } elseif (Config::get('illegalChars')) {
                    $IllegalPlural = Config::get('illegalChars');
                }

                $IllegalPlural = html_entity_decode($IllegalPlural);

                if ($this->term->strictSearch == 1) {
                    $this->term->noPlural = true;
                }

                $MaxReplacement = -1;
                
                if (!empty(Config::get('glossar_max_replacements'))) {
                    $MaxReplacement = intval(Config::get('glossar_max_replacements'));
                }
                
                if (!empty($objPage->glossar_max_replacements)) {
                    $MaxReplacement = intval($objPage->glossar_max_replacements);
                }

                $plural = preg_replace('/[.]+(?<!\\.)/is', '\\.', $IllegalPlural . (!$this->term->noPlural ? $GLOBALS['glossar']['illegal'] : '')) . '<';
                
                $preg_query = '/(?!(?:[^<]+>|[^>]+(<!--|-->)))(' . ($this->term->strictSearch == 1 || $this->term->strictSearch == 3 ? '\b' : '') . $this->term->title . (!$this->term->noPlural ? '[^ ' . $plural . ']*' : '') . ($this->term->strictSearch == 1 ? '\b' : '') . ')/is';
                $no_preg_query = '/(?!(?:[^<]+>|[^>]+(<\/' . implode('>|<\/', $ignoredTags) . '>)))(?:<(?:a|span|abbr) (?!class="glossar")[^>]*>)(' . ($this->term->strictSearch == 1 || $this->term->strictSearch == 3 ? '\b' : '') . $this->term->title . (!$this->term->noPlural ? '[^ ' . $plural . ']*' : '') . ($this->term->strictSearch == 1 ? '\b' : '') . ')/is';
                // die('<pre>' . print_r($preg_query, true));
                // die('<pre>' . print_r($replaceFunction, true));
                if ($this->term->title && preg_match_all($preg_query, $strContent, $third)) {
                    $strContent = preg_replace_callback($preg_query, array($this, $replaceFunction), $strContent, $MaxReplacement);
                    if ($lastIstDot) {
                        $strContent = str_replace($this->term->title, substr($this->term->title, 0, -1), $strContent);
                    }
                }
            }

            if ($this->term->type == 'abbr') {
                $lastIstDot = false;
                if (substr($this->term->title, -1) === '.') {
                    $strContent = str_replace($this->term->title, $this->term->title . 'X', $strContent);
                    $this->term->title .= 'X';
                    $lastIstDot = true;
                }

                $preg_query = '/(?!(?:[^<]+>|[^>]+(<!--|-->)))\b(' . $this->term->title . ')\b/is';

                if ($this->term->title && preg_match_all($preg_query, $strContent, $third)) {
                    $strContent = preg_replace_callback($preg_query, array($this, 'replaceAbbr'), $strContent);
                    if ($lastIstDot) {
                        $strContent = str_replace($this->term->title, substr($this->term->title, 0, -1), $strContent);
                    }
                }
            }
        }

        // $strContent = $this->insertTagIgnoreContent($strContent, $IgnoreTags);
        return $strContent;
    }

    private function replaceAbbr($treffer)
    {
        $href = $data = '';
        if ($this->term->source == 'page' && $this->term->jumpTo) {
            $Page = PageModel::findByPk($this->term->jumpTo);
            $href = $Page->getAbsoluteUrl();
        }

        if ($this->term->source == 'external' && $this->term->url) {
            $href = $this->term->url;
        }

        $lang = '';

        if (!empty($this->term_glossar->language)) {
            $lang = ' lang="' . $this->term_glossar->language . '"';
        }

        $abbrObj = new FrontendTemplate('term_abbr');
        $abbrObj->setData(array(
            'lang' => $lang,
            'class' => 'glossar glossar_' . $this->term->pid['id'],
            'label' => $treffer[2],
            'title' => $this->term->explanation,
        ));

        if ($href) {
            $linkObj = new FrontendTemplate('term_link');
            $linkObj->setData(array(
                'lang' => $lang,
                'class' => 'glossar_abbr glossar_' . $this->term->pid['id'],
                'id' => $this->term->id,
                'link' => $href,
                'label' => $abbrObj->parse(),
                'maxWidth' => $this->term->maxWidth,
                'maxHeight' => $this->term->maxHeight,
            ));

            return $linkObj->parse();
        }
        return str_replace("\n", '', trim($abbrObj->parse()));
    }

    private function replaceTitle2Span($treffer)
    {
        $lang = '';
        if (!empty($this->term_glossar->language)) {
            $lang = ' lang="' . $this->term_glossar->language . '"';
        }

        $spanObj = new FrontendTemplate('term_span');
        $spanObj->setData(array(
            'lang' => $lang,
            'class' => 'glossar glossar_no_content glossar_' . $this->term->pid['id'],
            'id' => $this->term->id,
            'label' => $treffer[2],
            'maxWidth' => $this->term->maxWidth,
            'maxHeight' => $this->term->maxHeight,
        ));

        return str_replace("\n", '', trim($spanObj->parse()));
    }

    private function replaceTitle2Link($treffer)
    {
        $link = $this->generateUrl();
        $lang = '';
        if (!empty($this->term_glossar->language)) {
            $lang = ' lang="' . $this->term_glossar->language . '"';
        }

        $linkObj = new FrontendTemplate('term_link');
        $linkObj->setData(array(
            'lang' => $lang,
            'class' => 'glossar glossar_link glossar_' . $this->term->pid['id'],
            'id' => $this->term->id,
            'label' => $treffer[2],
            'link' => $link,
            'maxWidth' => $this->term->maxWidth,
            'maxHeight' => $this->term->maxHeight,
        ));
        return str_replace("\n", '', trim($linkObj->parse()));
    }

    /**
     * Generate a URL and return it as string
     *
     * @param \NewsModel $this->term
     * @param boolean    $blnAddArchive
     *
     * @return string
     */
    protected function generateUrl($blnAddArchive = false)
    {
        $strCacheKey = 'id_' . $this->term->id;

        // Load the URL from cache
        if (isset(self::$arrUrlCache[$strCacheKey])) {
            return self::$arrUrlCache[$strCacheKey];
        }

        // Initialize the cache
        self::$arrUrlCache[$strCacheKey] = null;

        switch ($this->term->source) {
            case 'page':
                $link = '';
                if ($this->term->jumpTo) {
                    $link = GlossarPageModel::findByPk($this->term->jumpTo);
                }
                if ($link) {
                    $link = $link->getAbsoluteUrl((Config::get('useAutoItem') && !Config::get('disableAlias')) ? '/' : '/items/') . standardize(StringUtil::restoreBasicEntities($this->term->alias));
                }
                if ($link !== '') {
                    self::$arrUrlCache[$strCacheKey] = $link;
                }
                break;
            // Link to an external page
            case 'external':
                if (substr($this->term->url, 0, 7) == 'mailto:') {
                    self::$arrUrlCache[$strCacheKey] = StringUtil::encodeEmail($this->term->url);
                } else {
                    self::$arrUrlCache[$strCacheKey] = ampersand($this->term->url);
                }
                break;

            // Link to an internal page
            case 'internal':
                if (($objTarget = $this->term->getRelated('jumpTo')) !== null) {
                    /** @var PageModel $objTarget */
                    self::$arrUrlCache[$strCacheKey] = ampersand($objTarget->getFrontendUrl());
                }
                break;
            // Link to an article
            case 'article':
                if (($objArticle = \ArticleModel::findByPk($this->term->articleId, array('eager' => true))) !== null && ($objPid = $objArticle->getRelated('pid')) !== null) {
                    /** @var PageModel $objPid */
                    self::$arrUrlCache[$strCacheKey] = ampersand($objPid->getFrontendUrl('/articles/' . ((!Config::get('disableAlias') && $objArticle->alias != '') ? $objArticle->alias : $objArticle->id)));
                }
                break;
            default:
                if (!empty($this->term->link)) {
                    self::$arrUrlCache[$strCacheKey] = $this->term->link; 
                } else {
                    $link = '';
                    if (Config::get('jumpToGlossar')) {
                        $link = GlossarPageModel::findByPk(Config::get('jumpToGlossar'));
                    }
                    if ($link) {
                        $link = $link->getAbsoluteUrl('/'.$this->term->alias);
                    }
                    if ($link !== '') {
                        self::$arrUrlCache[$strCacheKey] = $link;
                    }
                }
                break;
        }


        // Link to the default page
        if (self::$arrUrlCache[$strCacheKey] === null) {
            return null;
            $objPage = PageModel::findWithDetails($this->term->jumpTo);

            if ($objPage === null) {
                self::$arrUrlCache[$strCacheKey] = ampersand(Environment::get('request'), true);
            } else {
                self::$arrUrlCache[$strCacheKey] = ampersand($objPage->getFrontendUrl(((Config::get('useAutoItem') && !Config::get('disableAlias')) ? '/' : '/items/') . ((!Config::get('disableAlias') && $this->term->alias != '') ? $this->term->alias : $this->term->id)));
            }

            // Add the current archive parameter (news archive)
            if ($blnAddArchive && Input::get('month') != '') {
                self::$arrUrlCache[$strCacheKey] .= (Config::get('disableAlias') ? '&amp;' : '?') . 'month=' . Input::get('month');
            }
        }

        return self::$arrUrlCache[$strCacheKey];
    }
}

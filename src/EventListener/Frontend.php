<?php

/**
 * Contao Open Source CMS
 */

declare (strict_types = 1);

namespace Sioweb\Glossar\EventListener;

use Contao\Config;
use Contao\Input;
use Contao\System;
use Date;
use Sioweb\Glossar\Services\Decorator;
use Sioweb\Glossar\Services\License as GlossarLicense;
use Contao\CoreBundle\Framework\ContaoFramework;
use Sioweb\Glossar\Entity\Glossar as GlossarEntity;
use Sioweb\Glossar\Entity\Terms as TermsEntity;

/**
 * @file Frontend.php
 * @class Frontend
 * @author Sascha Weidner
 * @package sioweb.contao.extensions.glossar
 * @copyright Sascha Weidner, Sioweb
 */

class Frontend
{
    /**
     * @var ContaoFramework
     */
    private $framework;

    /**
     * @var GlossarLicense
     */
    private $license;

    private $entityManager;

    /**
     * @var Decorator
     */
    private $termDecorator;

    private $replaceIndex = [];

    public function __construct(ContaoFramework $framework, GlossarLicense $license, $entityManager, Decorator $termDecorator) {
        $this->framework = $framework;
        $this->license = $license;
        $this->entityManager = $entityManager;
        $this->termDecorator = $termDecorator;
    }

    public function searchGlossarTerms($strContent, $strTemplate)
    {
        global $objPage;

        $GlossarRepository = $this->entityManager->getRepository(GlossarEntity::class);
        $TermRepository = $this->entityManager->getRepository(TermsEntity::class);
        
        if (!isset($_GET['items']) && Config::get('useAutoItem') && isset($_GET['auto_item'])) {
            Input::setGet('items', Input::get('auto_item'));
        }

        if ($objPage->disableGlossar == 1) {
            return $strContent;
        }

        $this->replaceIndex = [];
        $strContent = $this->cleanUpContent($strContent);

        $time = Date::floorToMinute();

        // HOOK: search for terms in Events, faq and news
        $arrGlossar = array($objPage->glossar);
        if (!empty($objPage->fallback_glossar) && (Config::get('glossar_no_fallback') != 1 || $objPage->glossar_no_fallback != 1)) {
            $arrGlossar[] = $objPage->fallback_glossar;
        }
        
        if (isset($GLOBALS['TL_HOOKS']['glossarContent']) && is_array($GLOBALS['TL_HOOKS']['glossarContent'])) {
            foreach ($GLOBALS['TL_HOOKS']['glossarContent'] as $type => $callback) {
                
                $cb_output = System::importStatic($callback[0])->{$callback[1]}(Input::get('items'), $strContent, $template, $objPage->language);

                if (!empty($cb_output)) {
                    $arrGlossar[] = $cb_output;
                }
            }
        }

        if (!empty($arrGlossar)) {
            $this->term = implode('|', $arrGlossar);
        }

        $this->term = addslashes(str_replace('||', '|', $this->term));

        $Glossar = $GlossarRepository->findByLanguage($objPage->language);
        if (empty($Glossar)) {
            $langSlug = explode('-', $objPage->language);
            $Glossar = $GlossarRepository->findByLanguage($langSlug[0]);
        }
        if (empty($Glossar)) {
            $Glossar = $GlossarRepository->findByFallback(1);
        }

        $arrGlossar = array();
        if (empty($Glossar)) {
            return $this->rebaseContent($strContent);
        }

        foreach($Glossar as $glossar) {
            $arrGlossar[] = $glossar->getId();
        }

        /* replace content with tags to stop glossary replacement */
        $GlossarIgnoreTags = $this->replaceGlossarIgnoreTags($strContent);
        if (Config::get('activateGlossarTags') == 1) {
            $GlossarTags = $this->replaceGlossarTags($strContent);
        }

        $Term = $TermRepository->findTermBy($time, $time + 60, $this->term, $arrGlossar);
        
        $strContent = $this->termDecorator->replace($strContent, $Term, $Glossar);

        if (empty($objPage->fallback_glossar) || Config::get('glossar_no_fallback') == 1 || $objPage->glossar_no_fallback == 1) {
            /* reinsert glossar hidden content */
            $strContent = $this->insertTagIgnoreContent($strContent, $GlossarIgnoreTags);
            if (Config::get('activateGlossarTags') == 1) {
                $strContent = $this->insertTagContent($strContent, $GlossarTags);
            }

            return $this->rebaseContent($strContent);
        }

        /* Replace the fallback languages */
        $Term = $TermRepository->findTermBy($time, $time + 60, $this->term, $objPage->fallback_glossar);
        $strContent = $this->termDecorator->replace($strContent, $Term);

        /* reinsert glossar hidden content */
        $strContent = $this->insertTagIgnoreContent($strContent, $GlossarIgnoreTags);
        if (Config::get('activateGlossarTags') == 1) {
            $strContent = $this->insertTagContent($strContent, $GlossarTags);
        }

        return $this->rebaseContent($strContent);
    }

    private function outerHTML($e) {
        $doc = new \DOMDocument();
        $doc->appendChild($doc->importNode($e, true));
        return str_ireplace(['%7B', '%7D'], ['{', '}'], $doc->saveHTML());
    }

    private function rebaseContent($strContent)
    {
        foreach(array_reverse($this->replaceIndex) as $indexer => $nodeObject) {
            $strContent = str_replace('<!--' . $indexer . '-->', $this->outerHTML($nodeObject), $strContent);
        }
        
        /** @see #12 */
        $strContent = preg_replace('|<!--GLOSSAR::REPLACE::EXTREA::(.*?)-->|is', '[[$1]]', $strContent);
        
        return $strContent;
    }

    private function cleanUpContent($strContent)
    {
        $ignoredTags = array('a');
        if (Config::get('ignoreInTags')) {
            $ignoredTags = explode(',', str_replace(' ', '', Config::get('ignoreInTags')));
        }
        
        /** @see #12 */
        $strContent = preg_replace('|\[\[(.*?)\]\]|is', '<!--GLOSSAR::REPLACE::EXTREA::$1-->', $strContent);

        libxml_use_internal_errors(true);
        $dom = new \DOMDocument();
        $dom->loadHTML($strContent, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        $xpath = new \DOMXPath($dom);

        foreach($ignoredTags as $tag) {
            foreach($xpath->query('//' . $tag) as $tagObj) {
                $indexer = 'GLOSSAR::REPLACE::' . strtoupper($tag) . '::' . count($this->replaceIndex);
                $CommentNode = $dom->createComment($indexer);
                $this->replaceIndex[$indexer] = $tagObj;
                $tagObj->parentNode->replaceChild($CommentNode, $tagObj);
            }
        }
        return $dom->saveHTML();
    }

    /* Replace content between the tags with placeholder */
    private function replaceGlossarIgnoreTags(&$strContent)
    {
        $arrTagContent = array();
        $cIndex = 0;

        while (($intStart = strpos($strContent, '<!-- glossar::ignore -->')) !== false) {
            if (($intEnd = strpos($strContent, '<!-- glossar::unignore -->', $intStart)) !== false) {
                $intCurrent = $intStart;
                // Handle nested tags
                while (($intNested = strpos($strContent, '<!-- glossar::ignore -->', $intCurrent + 22)) !== false && $intNested < $intEnd) {
                    if (($intNewEnd = strpos($strContent, '<!-- glossar::unignore -->', $intEnd + 26)) !== false) {
                        $intEnd = $intNewEnd;
                        $intCurrent = $intNested;
                    } else {
                        break;
                    }
                }

                /**
                 * Save all cut content to reinsert it in insertTagContent
                 * to hide content from Glossar.
                 */
                $arrTagContent[] = substr($strContent, $intStart, $intEnd + 26 - $intStart);
                $strContent = substr($strContent, 0, $intStart) . '###GLOSSAR_IGNORE_CONTENT_' . $cIndex . '###' . substr($strContent, $intEnd + 26);
                $cIndex++;
            } else {
                break;
            }
        }

        return $arrTagContent;
    }

    /* Replace content between the tags with placeholder */
    private function replaceGlossarTags(&$strContent)
    {
        $arrTagContent = array();
        $cIndex = 0;
        while (($intStart = strpos($strContent, '<!-- glossar::stop -->')) !== false) {
            if (($intEnd = strpos($strContent, '<!-- glossar::continue -->', $intStart)) !== false) {
                $intCurrent = $intStart;
                // Handle nested tags
                while (($intNested = strpos($strContent, '<!-- glossar::stop -->', $intCurrent + 22)) !== false && $intNested < $intEnd) {
                    if (($intNewEnd = strpos($strContent, '<!-- glossar::continue -->', $intEnd + 26)) !== false) {
                        $intEnd = $intNewEnd;
                        $intCurrent = $intNested;
                    } else {
                        break;
                    }
                }

                /**
                 * Save all cut content to reinsert it in insertTagContent
                 * to hide content from Glossar.
                 */
                $arrTagContent[] = substr($strContent, $intStart, $intEnd + 26 - $intStart);
                $strContent = substr($strContent, 0, $intStart) . '###GLOSSAR_CONTENT_' . $cIndex . '###' . substr($strContent, $intEnd + 26);
                $cIndex++;
            } else {
                break;
            }
        }

        return $arrTagContent;
    }

    /* replace placeholder with glossar-tag content */
    private function insertTagContent($strContent, $tags = array())
    {
        if (!empty($tags)) {
            foreach ($tags as $key => $tag) {
                $strContent = str_replace('###GLOSSAR_CONTENT_' . $key . '###', $tag, $strContent);
            }
        }
        return $strContent;
    }

    /* replace placeholder with glossar-tag content */
    private function insertTagIgnoreContent($strContent, $tags = array())
    {
        if (!empty($tags)) {
            foreach ($tags as $key => $tag) {
                $strContent = str_replace('###GLOSSAR_IGNORE_CONTENT_' . $key . '###', $tag, $strContent);
            }
        }
        return $strContent;
    }
}

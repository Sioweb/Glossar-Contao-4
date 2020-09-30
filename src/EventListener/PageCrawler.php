<?php

/**
 * Contao Open Source CMS
 */

declare(strict_types=1);

namespace Sioweb\Glossar\EventListener;

use Contao\ArticleModel;
use Contao\Backend;
use Contao\BackendTemplate;
use Contao\Config;
use Contao\Environment;
use Contao\Input;
use Contao\System;
use Contao\RequestToken;
use Doctrine\DBAL\Connection;
use Symfony\Component\HttpFoundation\RequestStack;
use Sioweb\Glossar\Models\PageModel as GlossarPageModel;
use Sioweb\Glossar\Entity\Glossar as GlossarEntity;
use Sioweb\Glossar\Entity\Terms as TermsEntity;
use Contao\CoreBundle\Framework\ContaoFramework;

/**
 * @file PageCrawler.php
 * @class PageCrawler
 * @author Sascha Weidner
 * @package sioweb.contao.extensions.glossar
 * @copyright Sascha Weidner, Sioweb
 */

class PageCrawler
{

    private $Database;

    /**
     * @var Connection
     */
    private $connection;

    private $entityManager;
    /**
     * @var ScopeMatcher
     */
    private $scopeMatcher;

    /**
     * @var RequestStack
     */
    private $requestStack;

    public function __construct(ContaoFramework $framework, Connection $connection, $entityManager, RequestStack $requestStack)
    {
        $framework->initialize();
        $this->entityManager = $entityManager;
        $this->connection = $connection;

        $this->requestStack = $requestStack;
    }

    public function onModifyFrontendPage($strContent, $arrData)
    {
        $request = $this->requestStack->getCurrentRequest();

        if ($request->query->get('rebuild_glossar') != 1 || Config::get('disableGlossarCache') == 1) {
            return $strContent;
        }

        global $objPage;

        $GlossarRepository = $this->entityManager->getRepository(GlossarEntity::class);
        $TermRepository = $this->entityManager->getRepository(TermsEntity::class);

        $strContent = str_replace([
            '<!-- indexer::stop -->',
            '<!-- indexer::continue -->'
        ], ['', ''], $strContent);

        $time = Input::get('time') ?? time();
        $strContent = preg_replace('#<script(.*?)>(.*?)</script>#is', '', $strContent);
        $strContent = preg_replace('#<style(.*?)>(.*?)</style>#is', '', $strContent);


        $this->connection->prepare("UPDATE
            tl_page SET
                glossar = NULL, fallback_glossar = NULL, glossar_time = :glossar_time
            WHERE glossar_time != :glossar_time
        ")->execute([':glossar_time' => $time]);

        if (isset($GLOBALS['TL_HOOKS']['clearGlossar']) && is_array($GLOBALS['TL_HOOKS']['clearGlossar'])) {
            foreach ($GLOBALS['TL_HOOKS']['clearGlossar'] as $type => $callback) {
                $this->{$callback[0]} = System::importStatic($callback[0]);
                $this->{$callback[0]}->{$callback[1]}($time);
            }
        }

        if (Config::get('activateGlossarTags') == 1) {
            if (isset($GLOBALS['TL_HOOKS']['beforeGlossarTags']) && is_array($GLOBALS['TL_HOOKS']['beforeGlossarTags'])) {
                foreach ($GLOBALS['TL_HOOKS']['beforeGlossarTags'] as $type => $callback) {
                    $this->{$callback[0]} = System::importStatic($callback[0]);
                    $strContent = $this->{$callback[0]}->{$callback[1]}($strContent);
                }
            }

            $strContent = $this->replaceGlossarTags($strContent, ['<!-- glossar::continue -->', '<!-- glossar::stop -->']);

            if (isset($GLOBALS['TL_HOOKS']['afterGlossarTags']) && is_array($GLOBALS['TL_HOOKS']['afterGlossarTags'])) {
                foreach ($GLOBALS['TL_HOOKS']['afterGlossarTags'] as $type => $callback) {
                    $this->{$callback[0]} = System::importStatic($callback[0]);
                    $strContent = $this->{$callback[0]}->{$callback[1]}($strContent);
                }
            }
        }

        if (!isset($_GET['items']) && Config::get('useAutoItem') && isset($_GET['auto_item'])) {
            Input::setGet('items', Input::get('auto_item'));
        }

        $Glossar = $GlossarRepository->findAll();

        if (empty($Glossar)) {
            return;
        }

        $arrGlossar = [];

        foreach ($Glossar as $glossar) {
            $arrGlossar[$glossar->getId()] = $glossar->getLanguage();
        }

        $TermResult = $TermRepository->findAll(['LENGTH(title)' => 'DESC']);

        if (empty($TermResult)) {
            return;
        }

        $arrTerms = ['glossar' => [], 'fallback' => [], 'both' => []];
        foreach ($TermResult as $Term) {
            if ($arrGlossar[$Term->getPid()->getId()] == $objPage->language) {
                $arrTerms['glossar'][] = $Term->getTitle();
            } else {
                $arrTerms['fallback'][] = $Term->getTitle();
            }
            $arrTerms['both'][] = $Term->getTitle();
        }

        foreach ($arrTerms as &$pointer_terms) {
            $pointer_terms = array_unique($pointer_terms);
        }

        // HOOK: take additional pages
        if (isset($GLOBALS['TL_HOOKS']['cacheGlossarTerms']) && is_array($GLOBALS['TL_HOOKS']['cacheGlossarTerms'])) {
            foreach ($GLOBALS['TL_HOOKS']['cacheGlossarTerms'] as $type => $callback) {
                if (Input::get('rebuild_' . $type . '_glossar') !== null) {
                    $this->{$callback[0]} = System::importStatic($callback[0]);
                    $this->{$callback[0]}->{$callback[1]}(Input::get('items'), $arrTerms, $strContent, $type);
                }
            }
        }

        if ($request->query->get('rebuild_glossar') == 1) {
            $strFallback = $strGlossar = '';
            if (!empty($arrTerms['glossar'])) {
                $matches = [];
                foreach ($arrTerms['glossar'] as $term) {
                    if (preg_match('#' . str_replace('.', '\.', html_entity_decode($term)) . '#is', strip_tags($strContent))) {
                        $matches[] = $term;
                    }
                }

                $matches = array_unique(array_map("strtolower", $matches));
                $strGlossar = '|' . implode('|', $matches) . '|';
            }

            if (!empty($arrTerms['fallback'])) {
                $matches = [];
                foreach ($arrTerms['fallback'] as $key => $term) {
                    if (preg_match('#' . str_replace('.', '\.', $term) . '#is', strip_tags($strContent))) {
                        $matches[] = $term;
                    }
                }

                $matches = array_unique(array_map("strtolower", $matches));
                $strFallback = '|' . implode('|', $matches) . '|';
            }

            $this->connection->prepare("UPDATE
                tl_page SET
                    glossar = :glossar, fallback_glossar = :fallback_glossar, glossar_time = :glossar_time
                WHERE id = :id
            ")->execute([':glossar' => $strGlossar, ':fallback_glossar' => $strFallback, ':glossar_time' => $time, ':id' => $objPage->id]);
        }

        return $strContent;
    }

    private function replaceGlossarTags($strContent, $tags = [])
    {
        // Strip non-indexable areas
        while (($intStart = strpos($strContent, $tags[1])) !== false) {
            if (($intEnd = strpos($strContent, $tags[0], $intStart)) !== false) {
                $intCurrent = $intStart;

                // Handle nested tags
                while (($intNested = strpos($strContent, $tags[1], $intCurrent + 22)) !== false && $intNested < $intEnd) {
                    if (($intNewEnd = strpos($strContent, $tags[0], $intEnd + 26)) !== false) {
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
}

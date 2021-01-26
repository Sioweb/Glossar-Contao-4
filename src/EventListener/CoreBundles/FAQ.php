<?php

/**
 * Contao Open Source CMS
 */

declare(strict_types=1);

namespace Sioweb\Glossar\EventListener\CoreBundles;

use Contao\ArticleModel;
use Contao\ContentModel;
use Contao\Environment;
use Contao\FaqBundle\ContaoFaqBundle;
use Contao\FaqModel;
use Contao\Input;
use Contao\System;
use Contao\ModuleModel;
use Doctrine\DBAL\Connection;
use Contao\CoreBundle\Framework\ContaoFramework;
use Sioweb\Glossar\Models\FaqCategoryModel as GlossarFaqCategoryModel;
use Sioweb\Glossar\Modules\ModuleFaqList;

/**
 * @file FAQ.php
 * @class FAQ
 * @author Sascha Weidner
 * @package sioweb.contao.extensions.glossar
 * @copyright Sascha Weidner, Sioweb
 */

class FAQ //extends ModuleFaqList
{
    /**
     * @var ModuleFaqList
     */
    private $faq;

    /**
     * @var Connection
     */
    private $connection;

    /**
     * Target pages
     * @var array
     */
    protected $arrTargets = [];

    public function __construct(ContaoFramework $framework, Connection $connection)
    {
        $this->faq = $framework->getAdapter(ModuleFaqList::class);
        $this->connection = $connection;
    }

    public function compile()
    {
    }

    public function clearGlossar($time)
    {
        if (!class_exists(ContaoFaqBundle::class)) {
            return;
        }

        $this->connection->prepare("UPDATE tl_faq SET
            glossar = NULL, fallback_glossar = NULL, glossar_time = :glossar_time WHERE glossar_time != :glossar_time
        ")->execute([':glossar_time' => $time]);
    }

    public function glossarContent($item, $strContent, $template)
    {
        if (!class_exists(ContaoFaqBundle::class)) {
            return null;
        }

        if (empty($item)) {
            return [];
        }

        $Faq = FaqModel::findByAlias(Input::get('items'));
        return $Faq->glossar;
    }

    public function updateCache($item, $arrTerms, $strContent)
    {
        if (!class_exists(ContaoFaqBundle::class)) {
            return;
        }

        preg_match_all('#' . implode('|', $arrTerms['both']) . '#is', $strContent, $matches);
        $matches = array_unique($matches[0]);

        if (empty($matches)) {
            return;
        }

        $Faq = FaqModel::findByAlias($item);
        $Faq->glossar = implode('|', $matches);
        $Faq->save();
    }

    public function generateUrl($arrPages)
    {
        if (!class_exists(ContaoFaqBundle::class)) {
            return [];
        }

        $arrPages = [];

        $Faq = FaqModel::findAll();

        if (empty($Faq)) {
            return [];
        }

        while ($Faq->next()) {
            $arrPages[$Faq->pid][] = $this->generateFaqLink($Faq, false, true);
        }
        $InactiveArchives = GlossarFaqCategoryModel::findByPidsAndInactiveGlossar(array_keys($arrPages));
        if (!empty($InactiveArchives)) {
            while ($InactiveArchives->next()) {
                unset($arrPages[$InactiveArchives->id]);
            }
        }

        $_arrPages = [];
        foreach ($arrPages as $pages) {
            $_arrPages = array_merge($_arrPages, $pages);
        }

        $arrPages = ['faq' => $_arrPages];
        unset($_arrPages);

        return $arrPages;
    }

    /**
     * Create links and remember pages that have been processed
     *
     * @param FaqModel $objFaq
     *
     * @return string
     *
     * @throws \Exception
     */
    public function generateFaqLink($objFaq)
    {
        /** @var FaqCategoryModel $objCategory */
        $objCategory = $objFaq->getRelated('pid');
        $jumpTo = (int) $objCategory->jumpTo;

        // A jumpTo page is not mandatory for FAQ categories (see #6226) but required for the FAQ list module
        if ($jumpTo < 1) {
            throw new \Exception("FAQ categories without redirect page cannot be used in an FAQ list");
        }

        // Get the URL from the jumpTo page of the category
        if (!isset($this->arrTargets[$jumpTo])) {
            $this->arrTargets[$jumpTo] = ampersand(\Environment::get('request'), true);

            if ($jumpTo > 0 && ($objTarget = \PageModel::findByPk($jumpTo)) !== null) {
                /** @var PageModel $objTarget */
                $this->arrTargets[$jumpTo] = ampersand($objTarget->getAbsoluteUrl(\Config::get('useAutoItem') ? '/%s' : '/items/%s'));
            }
        }

        return sprintf(preg_replace('/%(?!s)/', '%%', $this->arrTargets[$jumpTo]), ($objFaq->alias ?: $objFaq->id));
    }
}

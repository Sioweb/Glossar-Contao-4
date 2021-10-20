<?php

/**
 * Contao Open Source CMS
 */

declare(strict_types=1);

namespace Sioweb\Glossar\EventListener\CoreBundles;

use Contao\ArticleModel;
use Contao\ContentModel;
use Contao\Environment;
use Contao\Input;
use Contao\News as BaseNews;
use Contao\ModuleModel;
use Contao\ModuleNews;
use Contao\NewsBundle\ContaoNewsBundle;
use Contao\NewsModel;
use Contao\System;
use Contao\PageModel;
use Doctrine\DBAL\Connection;
use Contao\CoreBundle\Framework\ContaoFramework;
use Sioweb\Glossar\Models\NewsArchiveModel as GlossarNewsArchiveModel;

/**
 * @file News.php
 * @class News
 * @author Sascha Weidner
 * @package sioweb.contao.extensions.glossar
 * @copyright Sascha Weidner, Sioweb
 */

class News //extends BaseNews
{
    /**
     * @var BaseNews
     */
    private $news;

    /**
     * @var Connection
     */
    private $connection;

    public function __construct(ContaoFramework $framework, Connection $connection)
    {
        $this->news = $framework->getAdapter(BaseNews::class);
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

        $this->connection->prepare("UPDATE tl_news SET
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

        $News = NewsModel::findByIdOrAlias(Input::get('items'));
        return $News->glossar;
    }

    public function updateCache($item, $arrTerms, $strContent)
    {
        if (!class_exists(ContaoFaqBundle::class)) {
            return;
        }

        $matches = [];
        foreach ($arrTerms['both'] as $term) {
            if (preg_match('#(' . $term . ')#is', $strContent, $match)) {
                $matches[] = $match[1];
            }
        }

        $matches = array_unique($matches);

        if (empty($matches)) {
            return;
        }

        $News = NewsModel::findByIdOrAlias($item);
        $News->glossar = implode('|', $matches);
        $News->save();
    }

    public function generateUrl($arrPages)
    {
        if (!class_exists(ContaoFaqBundle::class)) {
            return [];
        }

        $arrPages = [];

        $News = NewsModel::findAll();

        if (empty($News)) {
            return [];
        }

        while ($News->next()) {
            $arrPages[$News->pid][] = $this->news->generateNewsUrl($News, false, true);
        }
        $InactiveArchives = GlossarNewsArchiveModel::findByPidsAndInactiveGlossar(array_keys($arrPages));
        if (!empty($InactiveArchives)) {
            while ($InactiveArchives->next()) {
                unset($arrPages[$InactiveArchives->id]);
            }
        }

        $_arrPages = [];
        foreach ($arrPages as $pages) {
            $_arrPages = array_merge($_arrPages, $pages);
        }

        $arrPages = ['news' => $_arrPages];
        unset($_arrPages);

        return $arrPages;
    }

    private function getRootPage($id)
    {
        $Page = PageModel::findByPk($id);
        if ($Page->type !== 'root') {
            $Page = $this->getRootPage($Page->pid);
        }
        return $Page;
    }
}

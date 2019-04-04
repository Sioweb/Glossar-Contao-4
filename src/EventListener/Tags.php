<?php

/**
 * Contao Open Source CMS
 */

declare (strict_types = 1);

namespace Sioweb\Glossar\EventListener;

use Contao\Config;
use Contao\StringUtil;
use Sioweb\Glossar\Models\GlossarPageModel;
use Sioweb\Glossar\Entity\Terms as TermsEntity;

/**
 * @file Tags.php
 * @class Tags
 * @author Sascha Weidner
 * @package sioweb.contao.extensions.glossar
 * @copyright Sascha Weidner, Sioweb
 */

class Tags
{
    private $entityManager;

    public function __construct($entityManager) {
        $this->entityManager = $entityManager;
    }

    public function addSourceTable($sourceTable, $tags)
    {
        $TermRepository = $this->entityManager->getRepository(TermsEntity::class);
        $Terms = $TermRepository->findBy(['id' => $tags]);
        
        if (!empty($Terms)) {
            $arrLinks = array();
            foreach($Terms as $Term) {
                if (Config::get('jumpToGlossar')) {
                    $link = GlossarPageModel::findByPk(Config::get('jumpToGlossar'));
                }

                if ($this->term->jumpTo) {
                    $link = GlossarPageModel::findByPk($Term->jumpTo);
                }

                if ($link) {
                    $link = $link->getAbsoluteUrl() . ((Config::get('useAutoItem') && !Config::get('disableAlias')) ? '/' : '/items/') . standardize(StringUtil::restoreBasicEntities($Term->getAlias()));
                }

                $arrLinks[] = '<a href="' . $link . '" title="' . $Term->title . '">' . $Term->title . '</a>';
            }
        }

        return $arrLinks;
    }
}

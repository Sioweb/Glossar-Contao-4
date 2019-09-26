<?php

/**
 * Contao Open Source CMS
 */

declare (strict_types = 1);

namespace Sioweb\Glossar\EventListener;

use Contao\Config;
use Contao\Controller;
use Contao\Environment;
use Sioweb\Glossar\Classes\Terms;
use Sioweb\Glossar\Services\License as GlossarLicense;
use Contao\CoreBundle\Framework\ContaoFramework;
use Sioweb\Glossar\Models\PageModel as GlossarPageModel;
use Sioweb\Glossar\Entity\Terms as TermsEntity;

/**
 * @file Backend.php
 * @class Backend
 * @author Sascha Weidner
 * @package sioweb.contao.extensions.glossar
 * @copyright Sascha Weidner, Sioweb
 */

class Backend
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

    /**
     * @var Terms
     */
    private $terms;

    public function __construct(ContaoFramework $framework, GlossarLicense $license, $entityManager) {
        $this->framework = $framework;
        $this->license = $license;
        $this->entityManager = $entityManager;
        $this->terms = $framework->getAdapter(Terms::class);
    }

    public function getSearchablePages($arrPages, $intRoot = 0, $blnIsSitemap = false)
    {
        $TermsRepository = $this->entityManager->getRepository(TermsEntity::class);
        $objTerms = $TermsRepository->findAll();
        
        if (empty($objTerms)) {
            return $arrPages;
        }

        foreach($objTerms as $Term) {
            $url = Config::get('jumpToGlossar');
            if ($Term->getJumpTo()) {
                $url = $Term->getJumpTo();
            }

            $objParent = GlossarPageModel::findWithDetails($url);
            $domain = ($objParent->rootUseSSL ? 'https://' : 'http://') . ($objParent->domain ?: Environment::get('host')) . TL_PATH . '/';

            if (!empty($url)) {
                $link = GlossarPageModel::findByPk($url);
                if($link !== null) {
                    $arrPages[] = $domain . Controller::generateFrontendUrl($link->row(), ((Config::get('useAutoItem') && !Config::get('disableAlias')) ? '/' : '/items/') . $Term->getAlias());
                }
            }
        }

        return $arrPages;
    }
}

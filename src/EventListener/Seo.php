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
use Contao\Controller;
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
use Sioweb\Glossar\Models\ContentModel;

/**
 * @file Seo.php
 * @class Seo
 * @author Sascha Weidner
 * @package sioweb.contao.extensions.glossar
 * @copyright Sascha Weidner, Sioweb
 */

class Seo
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

    public function onOutputFrontendTemplate($strContent, $arrData)
    {
        $request = $this->requestStack->getCurrentRequest();

        global $objPage;


        if(!Input::get('items')) {
            return $strContent;
        }


        $GlossarRepository = $this->entityManager->getRepository(GlossarEntity::class);
        $TermRepository = $this->entityManager->getRepository(TermsEntity::class);
        $TermObj = $TermRepository->findOneByAlias(Input::get('items'));
        
        if($TermObj === null) {
            return $strContent;
        }
        
        $Glossar = $TermObj->getPid();

        $useObject = null;
        if(!empty($Glossar->getCanonicalType()) && $Glossar->getCanonicalType() !== 'donotset') {
            $useObject = &$Glossar;
        }
        if(!empty($TermObj->getCanonicalType()) && $TermObj->getCanonicalType() !== 'donotset') {
            $useObject = &$TermObj;
        }

        if(method_exists($useObject, 'getCanonicalType')) {
            switch($useObject->getCanonicalType()) {
                case 'internal':
                    $Link = '<link rel="canonical" href="' . static::generateLink($useObject->getCanonicalJumpTo(), true) . '"' . $objPage->outputFormat . '>';
                    break;
                case 'external':
                    $Link = '<link rel="canonical" href="' . $useObject->getCanonicalWebsite() . '">';
                    break;
                case 'self':
                default:
                    $Link = '<link rel="canonical" href="' . static::generateLink($objPage->id) . '"' . $objPage->outputFormat . '>';
                    break;
            }
        }

        $strContent = str_replace('</head>', $Link . "\n</head>", $strContent);

        return $strContent;
    }


    /**
     * Generate the canonical link
     *
     * @param $objPage
     * @return string
     */
    private static function generateLink($id, $intern = false)
    {
        $strUrl = '';

        $strDomain = Environment::get('base');

        $objCanonicalPage = GlossarPageModel::findWithDetails($id);
        
        if ($objCanonicalPage !== null) {
            if ($objCanonicalPage->domain != '') {
                $strDomain = (Environment::get('ssl') ? 'https://' : 'http://') . $objCanonicalPage->domain . TL_PATH . '/';
            }
            $Parameters = null;
            if(!$intern) {
                $Parameters = ((Config::get('useAutoItem') && !Config::get('disableAlias')) ? '/' : '/items/') . Input::get('items');
            }
            $strUrl = $strDomain . Controller::generateFrontendUrl($objCanonicalPage->row(), $Parameters, $objCanonicalPage->language);
        }

        return $strUrl;
    }
}

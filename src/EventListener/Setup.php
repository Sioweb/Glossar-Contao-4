<?php

/**
 * Contao Open Source CMS
 */

declare (strict_types = 1);

namespace Sioweb\Glossar\EventListener;

use Contao\Config;
use Contao\CoreBundle\Framework\ContaoFramework;
use Contao\CoreBundle\Routing\ScopeMatcher;
use Contao\System;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * @file Setup.php
 * @class Setup
 * @author Sascha Weidner
 * @package sioweb.contao.extensions.glossar
 * @copyright Sascha Weidner, Sioweb
 */

class Setup
{

    /**
     * @var ScopeMatcher
     */
    private $scopeMatcher;

    /**
     * @var ContaoFramework
     */
    private $contaoFramework;

    private $Database;

    /**
     * @var RequestStack
     */
    private $requestStack;

    public function __construct(ContaoFramework $framework, ScopeMatcher $scopeMatcher, RequestStack $requestStack)
    {
        $this->contaoFramework = $framework;
        $this->contaoFramework->initialize();

        $this->Database = System::importStatic('Database');

        $this->scopeMatcher = $scopeMatcher;
        $this->requestStack = $requestStack;
    }

    /* InitializeSystem */
    public function initializeSystem()
    {
        $GLOBALS['glossar'] = System::getContainer()->getParameter('glossar.config');
        $GLOBALS['TL_PERMISSIONS'][] = 'glossar';
        $GLOBALS['TL_PERMISSIONS'][] = 'glossarp';

        array_insert($GLOBALS['TL_MAINTENANCE_EXTENDED'], 1, [
            'sioweb.glossar.rebuild',
        ]);

        if (empty($GLOBALS['TL_HOOKS']['getGlossarPages'])) {
            $GLOBALS['TL_HOOKS']['getGlossarPages'] = [];
        }

        if (empty($GLOBALS['tags_extension'])) {
            $GLOBALS['tags_extension'] = ['sourcetable' => []];
        }

        $GLOBALS['tags_extension']['sourcetable'][] = 'tl_sw_glossar';

        if (!isset($GLOBALS['TL_CONFIG']['ignoreInTags'])) {
            Config::persist('ignoreInTags', 'title,a,h1,h2,h3,h4,h5,h6,nav,script,style,abbr,input,button,select,option,optgroup,applet,area,map,base,meta,canvas,head,legend,menu,menuitem,noframes,noscript,object,progress,source,time,video,audio,pre,iframe');
        }

        if (!isset($GLOBALS['TL_CONFIG']['illegalChars'])) {
            Config::persist('illegalChars', '")(=?.,;~:\'\>+\/!$€`´\'%&');
        }

        if ($this->requestStack->getCurrentRequest() === null) {
            return;
        }
    }

    /**
     * Backend settings
     */
    public function initializeBackend()
    {
        $Request = $this->requestStack->getCurrentRequest();

        if ($Request === null) {
            return;
        }

        if ($this->scopeMatcher->isBackendRequest($Request)) {
            $GLOBALS['TL_CSS'][] = 'bundles/siowebglossar/css/be_main.css';
        }

        array_insert($GLOBALS['BE_MOD']['content'], 1, [
            'glossar' => [
                'tables' => ['tl_glossar', 'tl_sw_glossar', 'tl_content'],
                'icon' => 'system/modules/Glossar/assets/sioweb16x16.png',
                'importGlossar' => ['sioweb.glossar.import', 'run'],
                'exportGlossar' => ['sioweb.glossar.export', 'run'],
                'importTerms' => ['sioweb.glossar.import', 'import'],
                'exportTerms' => ['sioweb.glossar.export', 'export'],
            ],
        ]);

        array_insert($GLOBALS['BE_MOD']['system'], 1, [
            'glossar_log' => [
                'callback' => 'Sioweb\Glossar\Classes\Log',
            ],
            'glossar_status' => [
                'callback' => 'Sioweb\Glossar\Classes\Status',
            ],
        ]);

        if (Config::get('glossarPurgable') == 1) {
            $GLOBALS['TL_PURGE']['custom']['glossar'] = [
                'callback' => ['sioweb.glossar.purge', 'run'],
            ];
        }
    }

    /**
     * Frontend settings
     */
    public function initializeFrontend()
    {
        $GLOBALS['TL_CTE']['texts']['glossar'] = 'Sioweb\Glossar\ContentElements\Glossar';
        $GLOBALS['TL_CTE']['texts']['glossar_cloud'] = 'Sioweb\Glossar\ContentElements\Cloud';

        /**
         * Front end modules
         */
        array_insert($GLOBALS['FE_MOD'], 2, [
            'glossar' => [
                'glossar_pagination' => 'Sioweb\Glossar\Modules\Pagination',
                'glossar_cloud' => 'Sioweb\Glossar\Modules\Cloud',
            ],
        ]);
    }

    // Default settings which are required by glossar
    public function initializeGlossar()
    {
        if (empty($GLOBALS['glossar'])) {
            $GLOBALS['glossar'] = [];
        }

        if (Config::get('enableGlossar') == 1) {

            $uploadTypes = Config::get('uploadTypes');
            if (strpos($uploadTypes, 'json') === false) {
                $uploadTypes .= (strlen($uploadTypes) > 0 ? ',' : '') . 'json';

                if (method_exists('Contao\Config', 'set')) {
                    Config::set('uploadTypes', $uploadTypes);
                } elseif (method_exists('Contao\Config', 'add')) {
                    Config::add('$GLOBALS[\'TL_CONFIG\'][\'uploadTypes\']', $uploadTypes);
                }
            }
            if ($this->scopeMatcher->isFrontendRequest($this->requestStack->getCurrentRequest())) {
                $GLOBALS['TL_CSS'][] = 'bundles/siowebglossar/css/glossar.css|static';
                if (empty($GLOBALS['TL_CONFIG']['disableToolTips'])) {
                    $GLOBALS['TL_JAVASCRIPT'][] = 'bundles/siowebglossar/js/glossar.js|static';
                }
            }
        }
    }
}

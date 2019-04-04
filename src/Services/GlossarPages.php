<?php

/**
 * Contao Open Source CMS
 */

declare (strict_types = 1);

namespace Sioweb\Glossar\Services;

use Contao\Config;
use Contao\System;
use Contao\Environment;
use Sioweb\Glossar\Services\License as GlossarLicense;
use Contao\CoreBundle\Framework\ContaoFramework;
use Sioweb\Glossar\Models\PageModel as GlossarPageModel;
use Sioweb\Glossar\Entity\Terms as TermsEntity;

/**
 * @file GlossarPages.php
 * @class GlossarPages
 * @author Sascha Weidner
 * @package sioweb.contao.extensions.glossar
 * @copyright Sascha Weidner, Sioweb
 */

class GlossarPages
{
    /**
     * @var ContaoFramework
     */
    private $framework;

    public function __construct(ContaoFramework $framework) {
        $this->framework = $framework;
    }

    /* Delete all cached glossary data*/
    public function run()
    {
        $arrPages = [];
        if (isset($GLOBALS['TL_HOOKS']['getGlossarPages']) && is_array($GLOBALS['TL_HOOKS']['getGlossarPages'])) {
            foreach ($GLOBALS['TL_HOOKS']['getGlossarPages'] as $type => $callback) {
                $this->{$callback[0]} = System::importStatic($callback[0]);
                $cb_return = $this->{$callback[0]}->{$callback[1]}($arrPages);
                if (empty($cb_return)) {
                    continue;
                }

                $arrPages = array_merge($cb_return, $arrPages);
            }
        }

        return $arrPages;
    }
}

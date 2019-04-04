<?php

/**
 * Contao Open Source CMS
 */

declare (strict_types = 1);

namespace Sioweb\Glossar\Services;

use Contao\Config;
use Contao\System;
use Contao\Environment;
use Doctrine\DBAL\Connection;
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

class Purge
{
    /**
     * @var ContaoFramework
     */
    private $framework;

    /**
     * @var Connection
     */
    private $connection;

    /**
     * @var Decorator
     */
    private $termDecorator;

    public function __construct(ContaoFramework $framework, Connection $connection) {
        $this->framework = $framework;
        $this->connection = $connection;
    }

    /* Delete all cached glossary data*/
    public function run()
    {
        $time = time();
        $this->connection->prepare("UPDATE tl_page SET
            glossar = NULL, fallback_glossar = NULL,glossar_time = :glossar_time WHERE glossar_time != :glossar_time
        ")->execute([':glossar_time' => $time]);
        if (isset($GLOBALS['TL_HOOKS']['clearGlossar']) && is_array($GLOBALS['TL_HOOKS']['clearGlossar'])) {
            foreach ($GLOBALS['TL_HOOKS']['clearGlossar'] as $type => $callback) {
                $this->{$callback[0]} = System::importStatic($callback[0]);
                $this->{$callback[0]}->{$callback[1]}($time);
            }
        }
    }
}

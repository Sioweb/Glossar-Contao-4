<?php

/**
 * Contao Open Source CMS
 */

declare(strict_types=1);

namespace Sioweb\Glossar\Polyfill\Contao44\Services;

use Contao\System;

/**
 * @file Rebuild.php
 * @class Rebuild
 * @author Sascha Weidner
 * @package sioweb.contao.extensions.glossar
 * @copyright Sascha Weidner, Sioweb
 */

class Rebuild extends \Sioweb\Glossar\Services\Rebuild
{

    public function __construct() {
        $framework = System::getContainer()->get('contao.framework');
        $framework->initialize();

        parent::__construct($framework);
    }
}

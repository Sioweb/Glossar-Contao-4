<?php

/**
 * Contao Open Source CMS
 */

declare (strict_types = 1);

namespace Sioweb\Glossar\Services;

use Sioweb\License\Glossar as GlossarLicense;

/**
 * @file License.php
 * @class License
 * @author Sascha Weidner
 * @package sioweb.contao.extensions.glossar
 * @copyright Sascha Weidner, Sioweb
 */

class License {
    
    private $valid = false;

    public function __construct()
    {
        if (class_exists('Sioweb\License\Glossar')) {
            $license = new GlossarLicense();
            $this->valid = $license->checkLocalLicense();
        } else{
            $this->valid = false;
        }
    }

    public function valid() {
        return $this->valid;
    }
}
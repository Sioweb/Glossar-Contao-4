<?php

namespace Sioweb\Glossar\Polyfill\Contao44\EventListener;

use Contao\System;

class InsertTags extends \Sioweb\Glossar\EventListener\InsertTags
{
    public function __construct()
    {
        parent::__construct(
            System::getContainer()->get('doctrine.orm.default_entity_manager')
        );
    }
}
<?php

namespace Sioweb\Glossar\Polyfill\Contao44\EventListener;

use Contao\System;

class Frontend extends \Sioweb\Glossar\EventListener\Frontend
{
    public function __construct()
    {
        parent::__construct(
            System::getContainer()->get('contao.framework'),
            System::getContainer()->get('sioweb.glossar.license'),
            System::getContainer()->get('doctrine.orm.default_entity_manager'),
            System::getContainer()->get('sioweb.glossar.decorator')
        );
    }
}

<?php

namespace Sioweb\Glossar\Polyfill\Contao44\EventListener;

use Contao\System;

class PageCrawler extends \Sioweb\Glossar\EventListener\PageCrawler
{
    public function __construct()
    {
        parent::__construct(
            System::getContainer()->get('contao.framework'),
            System::getContainer()->get('database_connection'),
            System::getContainer()->get('doctrine.orm.default_entity_manager'),
            System::getContainer()->get('request_stack')
        );
    }
}
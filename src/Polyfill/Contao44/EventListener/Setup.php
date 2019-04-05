<?php

namespace Sioweb\Glossar\Polyfill\Contao44\EventListener;

use Contao\System;

class Setup extends \Sioweb\Glossar\EventListener\Setup
{

    public function __construct()
    {
        parent::__construct(
            System::getContainer()->get('contao.framework'),
            System::getContainer()->get('contao.routing.scope_matcher'),
            System::getContainer()->get('request_stack')
        );
    }
}

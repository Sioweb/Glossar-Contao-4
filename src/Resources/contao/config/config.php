<?php

/**
 * Contao Open Source CMS
 */

/**
 * @file config.php
 * @author Sascha Weidner
 * @version 3.0.0
 * @package sioweb.contao.extensions.glossar
 * @copyright Sascha Weidner, Sioweb
 */

if(VERSION <= 4.5) {
    $GLOBALS['TL_HOOKS']['initializeSystem'][]          = array('Sioweb\Glossar\Polyfill\Contao44\EventListener\Setup', 'initializeSystem');
    $GLOBALS['TL_HOOKS']['initializeSystem'][]          = array('Sioweb\Glossar\Polyfill\Contao44\EventListener\Setup', 'initializeBackend');
    $GLOBALS['TL_HOOKS']['initializeSystem'][]          = array('Sioweb\Glossar\Polyfill\Contao44\EventListener\Setup', 'initializeFrontend');
    $GLOBALS['TL_HOOKS']['initializeSystem'][]          = array('Sioweb\Glossar\Polyfill\Contao44\EventListener\Setup', 'initializeGlossar');

    $GLOBALS['TL_HOOKS']['replaceInsertTags'][]         = array('Sioweb\Glossar\Polyfill\Contao44\EventListener\InsertTags', 'replaceInsertTags');
    $GLOBALS['TL_HOOKS']['modifyFrontendPage'][]        = array('Sioweb\Glossar\Polyfill\Contao44\EventListener\PageCrawler', 'onModifyFrontendPage');
    $GLOBALS['TL_HOOKS']['tagsourcetable'][]            = array('Sioweb\Glossar\Polyfill\Contao44\EventListener\Tags', 'addSourceTable');
    $GLOBALS['TL_HOOKS']['getSearchablePages'][]        = array('Sioweb\Glossar\Polyfill\Contao44\EventListener\Backend', 'getSearchablePages');
    $GLOBALS['TL_HOOKS']['outputFrontendTemplate'][]    = array('Sioweb\Glossar\Polyfill\Contao44\EventListener\Frontend', 'searchGlossarTerms');

    $GLOBALS['TL_HOOKS']['clearGlossar'][]              = array('Sioweb\Glossar\Polyfill\Contao44\EventListener\CoreBundles\News', 'clearGlossar');
    $GLOBALS['TL_HOOKS']['cacheGlossarTerms'][]         = array('Sioweb\Glossar\Polyfill\Contao44\EventListener\CoreBundles\News', 'cacheGlossarTerms');
    $GLOBALS['TL_HOOKS']['glossarContent'][]            = array('Sioweb\Glossar\Polyfill\Contao44\EventListener\CoreBundles\News', 'glossarContent');
    
    $GLOBALS['TL_HOOKS']['clearGlossar'][]              = array('Sioweb\Glossar\Polyfill\Contao44\EventListener\CoreBundles\Events', 'clearGlossar');
    $GLOBALS['TL_HOOKS']['cacheGlossarTerms'][]         = array('Sioweb\Glossar\Polyfill\Contao44\EventListener\CoreBundles\Events', 'cacheGlossarTerms');
    $GLOBALS['TL_HOOKS']['glossarContent'][]            = array('Sioweb\Glossar\Polyfill\Contao44\EventListener\CoreBundles\Events', 'glossarContent');
    
    $GLOBALS['TL_HOOKS']['clearGlossar'][]              = array('Sioweb\Glossar\Polyfill\Contao44\EventListener\CoreBundles\FAQ', 'clearGlossar');
    $GLOBALS['TL_HOOKS']['cacheGlossarTerms'][]         = array('Sioweb\Glossar\Polyfill\Contao44\EventListener\CoreBundles\FAQ', 'cacheGlossarTerms');
    $GLOBALS['TL_HOOKS']['glossarContent'][]            = array('Sioweb\Glossar\Polyfill\Contao44\EventListener\CoreBundles\FAQ', 'glossarContent');
}
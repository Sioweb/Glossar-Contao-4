<?php

/**
 * Contao Open Source CMS
 */

declare(strict_types=1);

namespace Sioweb\Glossar\EventListener;

use Contao\Input;
use Sioweb\Glossar\Entity\Terms as TermsEntity;

/**
 * @file InsertTags.php
 * @class InsertTags
 * @author Sascha Weidner
 * @package sioweb.contao.extensions.glossar
 * @copyright Sascha Weidner, Sioweb
 */

class InsertTags
{
    private $entityManager;

    public function replaceInsertTags(string $tag)
    {
        return $this->onReplaceInsertTags($tag);
    }

    public function __construct($entityManager)
    {
        $this->entityManager = $entityManager;
    }
    
    /**
     * Replaces the "glossar" insert tag.
     *
     * @return string|false
     */
    public function onReplaceInsertTags(string $tag)
    {
        $TermRepository = $this->entityManager->getRepository(TermsEntity::class);
        $tag = explode('::', $tag);
        if ($tag[0] !== 'glossar') {
            return false;
        }

        switch ($tag[1]) {
            case 'term':
                if (($Item = Input::get('items')) != '') {
                    $Glossar = $TermRepository->findOneByAlias($Item);
                    if (!empty($Glossar)) {
                        return $Glossar->getTitle();
                    }
                }
                break;
        }
        return false;
    }
}

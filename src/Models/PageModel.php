<?php

/**
 * Contao Open Source CMS
 */

declare(strict_types=1);

namespace Sioweb\Glossar\Models;

/**
 * @file PageModel.php
 * @class PageModel
 * @author Sascha Weidner
 * @package sioweb.contao.extensions.glossar
 * @copyright Sascha Weidner, Sioweb
 */

class PageModel extends \Contao\PageModel
{
    public static function findActiveAndEnabledGlossarPages($arrOptions = [])
    {
        $t = static::$strTable;
        $arrValues = [1, 'regular'];
        $arrColumns = ["published = ? AND (type = 'root' OR type = ?) AND disableGlossar = 0"];
        return static::findBy($arrColumns, $arrValues, $arrOptions);
    }
}

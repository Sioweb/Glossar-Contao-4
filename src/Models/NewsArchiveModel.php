<?php

/**
 * Contao Open Source CMS
 */

declare(strict_types=1);

namespace Sioweb\Glossar\Models;

/**
 * @file NewsArchiveModel.php
 * @class NewsArchiveModel
 * @author Sascha Weidner
 * @package sioweb.contao.extensions.glossar
 * @copyright Sascha Weidner, Sioweb
 */

class NewsArchiveModel extends \Contao\NewsArchiveModel
{
    public static function findByPidsAndInactiveGlossar($arrPid, $arrOptions = [])
    {
        $t = static::$strTable;
        $arrColumns = ["$t.id IN('" . implode("','", $arrPid) . "') AND $t.glossar_disallow = 1"];
        return static::findBy($arrColumns, [], $arrOptions);
    }
}

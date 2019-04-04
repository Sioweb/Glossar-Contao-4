<?php

/**
 * Contao Open Source CMS
 */

declare (strict_types = 1);

namespace Sioweb\Glossar\Models;

/**
 * @file FaqCategoryModel.php
 * @class FaqCategoryModel
 * @author Sascha Weidner
 * @package sioweb.contao.extensions.glossar
 * @copyright Sascha Weidner, Sioweb
 */

class FaqCategoryModel extends \Contao\FaqCategoryModel
{
    public static function findByPidsAndInactiveGlossar($arrPid, $arrOptions = array())
    {
        $t = static::$strTable;
        $arrColumns = array("$t.id IN('" . implode("','", $arrPid) . "') AND $t.glossar_disallow = 1");
        return static::findBy($arrColumns, array(), $arrOptions);
    }
}

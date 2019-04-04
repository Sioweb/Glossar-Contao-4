<?php

/**
 * Contao Open Source CMS
 */

declare (strict_types = 1);

namespace Sioweb\Glossar\Models;

/**
 * @file ContentModel.php
 * @class ContentModel
 * @author Sascha Weidner
 * @package sioweb.contao.extensions.glossar
 * @copyright Sascha Weidner, Sioweb
 */

class ContentModel extends \Contao\ContentModel
{

    public static function findByPidsAndTable($arrPids, $table, $type, $arrOptions = array())
    {
        $t = static::$strTable;

        if (empty($arrPids) || empty($table)) {
            return array();
        }

        $time = \Date::floorToMinute();

        $arrValues = array($table);
        $arrColumns = array("pid IN('" . implode("','", $arrPids) . "') AND ptable = ?");


        return static::findBy($arrColumns, $arrValues, $arrOptions);
    }
}

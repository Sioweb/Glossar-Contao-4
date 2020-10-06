<?php

/**
 * Contao Open Source CMS
 */

declare(strict_types=1);

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
    public static function findByPidsAndTable($arrPids, $table, $type, $arrOptions = [])
    {
        $t = static::$strTable;

        if (empty($arrPids) || empty($table)) {
            return [];
        }

        $arrValues = [$table];
        $arrColumns = ["pid IN('" . implode("','", $arrPids) . "') AND ptable = ?"];

        return static::findBy($arrColumns, $arrValues, $arrOptions);
    }

    public static function findByPidAndType($pid, $types, $arrOptions = [])
    {
        $t = static::$strTable;

        if (empty($pid) || empty($types)) {
            return [];
        }

        if(!is_array($types)) {
            $types = [];
        }

        return static::findBy(["$t.pid = $pid AND $t.type IN('" . implode("','", $types) . "')"], [], $arrOptions);
    }
}

<?php

/**
 * Contao Open Source CMS
 */

declare (strict_types = 1);

namespace Sioweb\Glossar\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * @file TermsRepository.php
 * @class TermsRepository
 * @author Sascha Weidner
 * @package sioweb.contao.extensions.glossar
 * @copyright Sascha Weidner, Sioweb
 */

class TermsRepository extends EntityRepository
{
    public function findAll(array $orderBy = [], $limit = null, $offset = null)
    {
        if(empty($orderBy)) {
            $orderBy = ['LENGTH(t.title)' => 'DESC'];
        }
        return parent::findAll(null, $orderBy, $limit, $offset);
        return $this->createQueryBuilder('t')
                    ->orderBy('LENGTH(t.title)', 'DESC')
                        ->getQuery()
                            ->execute();
    }

    public function findTermBy($start, $stop, $term, $glossarPid) {
        return $this->createQueryBuilder('t')
            ->andWhere("(t.start = '' OR t.start <= :start)")
            ->andWhere("(t.stop = ''  OR t.stop <= :stop)")
            ->andWhere("t.published=1")
            ->andWhere("t.title IN (:title)")
            ->andWhere("t.pid IN (:pid)")
                ->setParameter('start', $start)
                ->setParameter('stop', $stop)
                ->setParameter('title', explode('|', $term))
                ->setParameter('pid', implode(',', $glossarPid))
                    ->getQuery()
                    ->execute();
    }

    public function findAllInitial($arrOptions, $initial)
    {
        // $t = static::$strTable;
        // $arrColumns = array("left($t.alias,1) = ?");
        // return static::findBy($arrColumns, $initial, $arrOptions);
    }

    public function findByPids($pids, $arrOptions = array())
    {
        // $t = static::$strTable;
        // $arrColumns = array("pid IN('" . implode("','", $pids) . "')");
        // return static::findBy($arrColumns, array(), $arrOptions);
    }

    public function findAllByAlias($arrAlias, $pid, $arrOptions = array())
    {
        // $t = static::$strTable;
        // $arrColumns = array("pid = ? AND alias IN('" . implode("','", $arrAlias) . "')");
        // return static::findBy($arrColumns, array($pid), $arrOptions);
    }
}
<?php

/**
 * Contao Open Source CMS
 */

declare (strict_types = 1);

namespace Sioweb\Glossar\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * @file GlossarRepository.php
 * @class GlossarRepository
 * @author Sascha Weidner
 * @package sioweb.contao.extensions.glossar
 * @copyright Sascha Weidner, Sioweb
 */

class GlossarRepository extends EntityRepository
{
    public function findAll(array $orderBy = [])
    {
        return $this->createQueryBuilder('t')
                    ->orderBy('LENGTH(t.title)', 'DESC')
                        ->getQuery()
                            ->execute();
    }

    public function findTermBy($start, $stop, $term, $glossarPid) {
        return $this->createQueryBuilder('t')
            ->andWhere("t.start = ''  OR t.start <= :start")
            ->andWhere("t.stop = ''  OR t.stop <= :stop")
            ->andWhere("t.published=1")
            ->andWhere("t.title IN (:title)")
            ->andWhere("t.pid IN (:pid)")
                ->setParameter('start', $start)
                ->setParameter('stop', $stop)
                ->setParameter('title', explode('|', $term))
                ->setParameter('pid', $arrGlossar)
                    ->getQuery()
                    ->execute();
    }

    public function findAllByAlias($arrAlias, $arrOptions = array())
    {
        // $t = static::$strTable;
        // $arrColumns = array("alias IN('" . implode("','", $arrAlias) . "')");
        // return static::findBy($arrColumns, array(), $arrOptions);
    }
}
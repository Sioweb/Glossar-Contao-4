<?php

/**
 * Contao Open Source CMS
 */

declare (strict_types = 1);

namespace Sioweb\Glossar\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * @file LogsRepository.php
 * @class LogsRepository
 * @author Sascha Weidner
 * @package sioweb.contao.extensions.glossar
 * @copyright Sascha Weidner, Sioweb
 */


class LogsRepository extends EntityRepository
{
    public function findAll(array $orderBy = [])
    {
        return $this->findBy([], $orderBy);
    }

    public function findLogs($tstamp, $limit = 500)
    {
        return $this->createQueryBuilder('t')
            ->andWhere("t.tstamp >= :tstamp")
            ->setParameter('tstamp', $tstamp)
                ->orderBy('t.tstamp', 'DESC')
                ->setMaxResults($limit)
                    ->getQuery()
                    ->execute();
    }
}

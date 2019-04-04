<?php

/**
 * Contao Open Source CMS
 */

declare(strict_types=1);
namespace Sioweb\Glossar\Classes;

use Contao\BackendModule;
use Sioweb\Glossar\Models\LogModel;
use Sioweb\License\Glossar as GlossarLicense;
use Sioweb\Glossar\Entity\Log AS LogEntity;
use Sioweb\Glossar\Models\PageModel AS GlossarPageModel;

/**
 * @file Log.php
 * @class Log
 * @author Sascha Weidner
 * @version 3.0.0
 * @package sioweb.contao.extensions.glossar
 * @copyright Sascha Weidner, Sioweb
 */

class Log extends BackendModule
{

    protected $strTemplate = 'be_glossar_log';

    private $license = null;

    public function generate()
    {
        if (class_exists('Sioweb\License\Glossar')) {
            $license = new GlossarLicense();
            $this->license = $license->checkLocalLicense();
        }
        return parent::generate();
    }

    public function compile()
    {
        $this->Template->lickey = $this->license;

        $entityManager = $this->getContainer()->get('doctrine.orm.default_entity_manager');
        $LogsRepository = $entityManager->getRepository(LogEntity::class);
        $LogResult = $LogsRepository->findLogs(time() - (86400 * 7 * 31));

        // $entityManager->persist($Entity);
        // $entityManager->flush();

        $arrTerms = $arrLog = array();

        // die('<pre>' . print_r($LogResult, true));

        if (!empty($LogResult)) {
            foreach ($LogResult as $Log) {
                $_term = $Log->getPid();
                if (empty($_term)) {
                    continue;
                }


                $arrLog[$Log->getAction()][$Log->getPid()->getId()][] = $Log->getUser();
                $_log = $Log->getData();
                $_log['term'] = $_term->getdata();
                $Page =  GlossarPageModel::findByPk($_log['page']);
                $_log['page'] = $Page->row();
                $_log['page']['url'] = $Page->getAbsoluteUrl();

                $arrTerms[$_term->getId()][] = $_log;
            }
        }

        $this->Template->terms = $arrTerms;
        $arrTerms = null;

        $stdArray = array(
            0 => array(
                'avg' => 0,
                'sum' => 0,
                'unique' => 0,
                'user' => array(),
                'user_percent' => array(),
            ),
        );

        $arrStats = array(
            'load' => $stdArray,
            'follow' => $stdArray,
            'close' => $stdArray,
            'cloud' => $stdArray,
            'span' => $stdArray,
        );

        $stdArray = null;

        foreach ($arrLog as $type => $terms) {
            foreach ($terms as $id => $users) {
                $arrStats[$type][$id]['unique'] = count(array_unique($users));
                foreach ($users as $key => $sid) {
                    if (empty($arrStats[$type][$id]['user'][$sid])) {
                        $arrStats[$type][$id]['user'][$sid] = 0;
                    }

                    $arrStats[$type][$id]['user'][$sid]++;

                    if (empty($arrStats[$type][0]['user'][$sid])) {
                        $arrStats[$type][0]['user'][$sid] = 0;
                    }

                    $arrStats[$type][0]['user'][$sid]++;
                }

                $arrStats[$type][$id]['sum'] = array_sum($arrStats[$type][$id]['user']);
                $arrStats[$type][0]['sum'] += $arrStats[$type][$id]['sum'];

                foreach ($arrStats[$type][$id]['user'] as $sid => $count) {
                    $arrStats[$type][$id]['user_percent'][$sid] = number_format($count * 100 / $arrStats[$type][$id]['sum'], 2);
                }

                $arrStats[$type][$id]['avg'] = number_format($arrStats[$type][$id]['sum'] / count($arrStats[$type][$id]['user']), 2);
                ksort($arrStats[$type][$id]);
            }

            $arrStats[$type][0]['avg'] = number_format($arrStats[$type][0]['sum'] / count($arrStats[$type][0]['user']), 2);

            foreach ($arrStats[$type][0]['user'] as $sid => $count) {
                $arrStats[$type][0]['user_percent'][$sid] = number_format($count * 100 / $arrStats[$type][0]['sum'], 2);
            }

            $arrStats[$type][0]['unique'] = count($arrStats[$type][0]['user']);
            ksort($arrStats[$type][0]);
            ksort($arrStats[$type]);
        }

        $this->Template->stats = $arrStats;
        $this->Template->log = $arrLog;
    }
}

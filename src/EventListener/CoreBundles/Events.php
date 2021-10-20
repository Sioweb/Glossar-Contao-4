<?php

/**
 * Contao Open Source CMS
 */

declare(strict_types=1);

namespace Sioweb\Glossar\EventListener\CoreBundles;

use Contao;
use Contao\ArticleModel;
use Contao\CalendarBundle\ContaoCalendarBundle;
use Contao\CalendarEventsModel;
use Contao\CalendarModel;
use Contao\Config;
use Contao\ContentModel;
use Contao\Environment;
use Contao\Events as BaseEvents;
use Contao\Input;
use Contao\System;
use Contao\ModuleModel;
use Doctrine\DBAL\Connection;
use Contao\CoreBundle\Framework\ContaoFramework;
use Sioweb\Glossar\Models\CalendarModel as GlossarCalendarModel;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * @file Events.php
 * @class Events
 * @author Sascha Weidner
 * @package sioweb.contao.extensions.glossar
 * @copyright Sascha Weidner, Sioweb
 */


class Events
{
    /**
     * @var BaseEvents
     */
    private $events;

    /**
     * @var Connection
     */
    private $connection;

    public function __construct(ContaoFramework $framework, Connection $connection)
    {
        $this->events = $framework->getAdapter(BaseEvents::class);
        $this->connection = $connection;
    }

    public function compile()
    {
    }

    public function clearGlossar($time)
    {
        if (!class_exists(ContaoCalendarBundle::class)) {
            return;
        }

        $this->connection->prepare("UPDATE tl_calendar_events SET
            glossar = NULL, fallback_glossar = NULL, glossar_time = :glossar_time WHERE glossar_time != :glossar_time
        ")->execute([':glossar_time' => $time]);
    }

    public function glossarContent($item, $strContent, $template)
    {
        if (!class_exists(ContaoCalendarBundle::class)) {
            return null;
        }

        if (empty($item)) {
            return [];
        }

        $Event = CalendarEventsModel::findByAlias(Input::get('items'));
        return $Event->glossar;
    }

    public function updateCache($item, $arrTerms, $strContent)
    {
        if (!class_exists(ContaoCalendarBundle::class)) {
            return null;
        }

        preg_match_all('#' . implode('|', $arrTerms['both']) . '#is', $strContent, $matches);
        $matches = array_unique($matches[0]);

        if (empty($matches)) {
            return;
        }

        $Event = CalendarEventsModel::findByAlias($item);
        $Event->glossar = implode('|', $matches);
        $Event->save();
    }

    public function generateUrl($arrPages)
    {
        if (!class_exists(ContaoCalendarBundle::class)) {
            return [];
        }

        $arrPages = [];

        $Event = CalendarEventsModel::findAll();
        if (empty($Event)) {
            return [];
        }

        while ($Event->next()) {
            $objCalendar = CalendarModel::findByPk($Event->pid);
            if ($objCalendar !== null && $objCalendar->jumpTo && ($objTarget = $objCalendar->getRelated('jumpTo')) !== null) {
                $arrPages[$Event->pid][] = $this->events->generateEventUrl($Event, true);
            }
        }

        $InactiveArchives = GlossarCalendarModel::findByPidsAndInactiveGlossar(array_keys($arrPages));
        if (!empty($InactiveArchives)) {
            while ($InactiveArchives->next()) {
                unset($arrPages[$InactiveArchives->id]);
            }
        }

        $_arrPages = [];
        foreach ($arrPages as $pages) {
            $_arrPages = array_merge($_arrPages, $pages);
        }

        $arrPages = ['events' => $_arrPages];
        unset($_arrPages);

        return $arrPages;
    }
}

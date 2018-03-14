<?php

/**
 * Contao Open Source CMS
 */
namespace Sioweb;
use Contao;

/**
 * @file GlossarEvents.php
 * @class GlossarEvents
 * @author Sascha Weidner
 * @version 3.0.0
 * @package sioweb.contao.extensions.glossar
 * @copyright Sascha Weidner, Sioweb
 */
class GlossarEvents extends \Events {

	public function __construct() {}

	public function compile() {}

	public function clearGlossar($time) {
		$this->import('Database');
		$this->Database->prepare("UPDATE tl_calendar_events SET glossar = NULL,fallback_glossar = NULL,glossar_time = ? WHERE glossar_time != ?")->execute($time, $time);
	}

	public function glossarContent($item, $strContent, $template) {
		if(empty($item)) {
			return array();
		}

		$Event = \CalendarEventsModel::findByAlias(\Input::get('items'));
		return $Event->glossar;
	}

	public function updateCache($item, $arrTerms, $strContent) {
		preg_match_all('#'.implode('|', $arrTerms['both']).'#is', $strContent, $matches);
		$matches = array_unique($matches[0]);

		if(empty($matches)) {
			return;
		}

		$Event = \CalendarEventsModel::findByAlias($item);
		$Event->glossar = implode('|', $matches);
		$Event->save();
	}

	public function generateUrl($arrPages) {
		$arrPages = array();

		$Event = \CalendarEventsModel::findAll();
		if(empty($Event)) {
			return array();
		}

		$arrEvent = array();
		while($Event->next()) {
			$objCalendar = \CalendarModel::findByPk($Event->pid);
			if ($objCalendar !== null && $objCalendar->jumpTo && ($objTarget = $objCalendar->getRelated('jumpTo')) !== null) {
				$arrEvent[$Event->pid][] = $this->generateEventUrl($Event, $this->generateFrontendUrl($objTarget->row(), ((\Config::get('useAutoItem') && !\Config::get('disableAlias')) ?  '/%s' : '/events/%s')));
			}
		}

		$InactiveArchives = \GlossarCalendarModel::findByPidsAndInactiveGlossar(array_keys($arrEvent));
		if(!empty($InactiveArchives)) {
			while($InactiveArchives->next()) {
				unset($arrEvent[$InactiveArchives->id]);
			}
		}

		if(empty($arrEvent)) {
			return array();
		}

		$EventReader = \ModuleModel::findByType('eventreader');

		if(empty($EventReader)) {
			return array();
		}

		$arrReader = array();

		while($EventReader->next()) {
			$arrReader[$EventReader->id] = deserialize($EventReader->cal_calendar);
		}

		$Content = \ContentModel::findBy(array("module IN ('".implode("','",array_keys($arrReader))."')"),array());

		if(empty($Content)) {
			return array();
		}

		$arrContent = array();
		while($Content->next()) {
			$arrContent[$Content->module] = $Content->pid;
		}

		$Article = \ArticleModel::findBy(array("tl_article.id IN ('".implode("','", $arrContent)."')"),array());

		if(empty($Article)) {
			return array();
		}

		$finishedIDs = $arrPages = array();
	 
		while($Article->next()) {
			$domain = \Environment::get('base');
			$strLanguage = 'de';
			$objPages = $Article->getRelated('pid');

			$ReaderId = false;
			foreach($arrContent as $module => $mid)^{
				if($mid == $Article->id) {
					$ReaderId = $module;
				}
			}

			foreach($arrReader[$ReaderId] as $event_id) {
				if(in_array($event_id, $finishedIDs)) {
					continue;
				}

				if(!empty($arrEvent[$event_id])) {
					foreach($arrEvent[$event_id] as $event_domain) {
						$event_domain = end((explode('/',str_replace('.html','', $event_domain))));
						$arrPages['de'][] = $domain . static::generateFrontendUrl($objPages->row(), '/'.$event_domain, $strLanguage);
					}
				}
				$finishedIDs[] = $event_id;
			}
		}

		return $arrPages;
	}
}
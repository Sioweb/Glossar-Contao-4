<?php

/**
 * Contao Open Source CMS
 */

/**
 * @file config.php
 * @author Sascha Weidner
 * @version 3.0.0
 * @package sioweb.contao.extensions.glossar
 * @copyright Sascha Weidner, Sioweb
 */

$GLOBALS['TL_PERMISSIONS'][] = 'glossar';
$GLOBALS['TL_PERMISSIONS'][] = 'glossarp';

if(empty($GLOBALS['tags_extension'])) {
	$GLOBALS['tags_extension'] = array('sourcetable'=>array());
}

$GLOBALS['tags_extension']['sourcetable'][] = 'tl_sw_glossar';
$GLOBALS['TL_HOOKS']['tagSourceTable'][] = array('Sioweb\Glossar', 'addSourceTable');

array_insert($GLOBALS['TL_MAINTENANCE'],1,array(
	'Sioweb\RebuildGlossar'
));

array_insert($GLOBALS['BE_MOD']['content'], 1, array(
	'glossar' => array(
		'tables' => array('tl_glossar','tl_sw_glossar','tl_content'),
		'icon'   => 'system/modules/Glossar/assets/sioweb16x16.png',
		'importGlossar' => array('Sioweb\Glossar', 'importGlossar'),
		'exportGlossar' => array('Sioweb\Glossar', 'exportGlossar'),
		'importTerms' => array('Sioweb\Glossar', 'importTerms'),
		'exportTerms' => array('Sioweb\Glossar', 'exportTerms'),
	)
));

if(TL_MODE == 'BE') {
	$GLOBALS['TL_CSS'][] = 'bundles/siowebglossar/css/be_main.css|static';
}

if(\Config::get('glossarPurgable') == 1) {
	$GLOBALS['TL_PURGE']['custom']['glossar'] = array(
		'callback' => array('Sioweb\Glossar', 'purgeGlossar')
	);
}


/**
 * Front end modules
 */
array_insert($GLOBALS['FE_MOD'], 2, array(
	'glossar' => array
	(
		'glossar_pagination'    => 'ModuleGlossarPagination',
		'glossar_cloud'         => 'ModuleGlossarCloud',
	)
));


array_insert($GLOBALS['BE_MOD']['system'], 1, array(
	'glossar_log' => array(
		'callback'   => 'Sioweb\GlossarLog',
		'icon'   => 'bundles/siowebglossar/img/sioweb16x16.png',
	),
	'glossar_status' => array(
		'callback'   => 'Sioweb\GlossarStatus',
		'icon'   => 'bundles/siowebglossar/img/sioweb16x16.png',
	),
));


if(method_exists('Contao\Config','set')) {
	if(!isset($GLOBALS['TL_CONFIG']['ignoreInTags'])) {
		\Config::set('ignoreInTags','title,a,h1,h2,h3,h4,h5,h6,nav,script,style,abbr,input,button,select,option,optgroup,applet,area,map,base,meta,canvas,head,legend,menu,menuitem,noframes,noscript,object,progress,source,time,video,audio,pre,iframe');
	}

	if(!isset($GLOBALS['TL_CONFIG']['illegalChars'])) {
		\Config::set('illegalChars','")(=?.,;~:\'\>\<+\/\\<');
	}
} elseif(method_exists('Contao\Config','add')) {
	if(!isset($GLOBALS['TL_CONFIG']['ignoreInTags'])) {
		\Config::add('$GLOBALS[\'TL_CONFIG\'][\'ignoreInTags\']','title,a,h1,h2,h3,h4,h5,h6,nav,script,style,abbr,input,button,select,option,optgroup,applet,area,map,base,meta,canvas,head,legend,menu,menuitem,noframes,noscript,object,progress,source,time,video,audio,pre,iframe');
	}

	if(!isset($GLOBALS['TL_CONFIG']['illegalChars'])) {
		\Config::add('$GLOBALS[\'TL_CONFIG\'][\'illegalChars\']','")(=?.,;~:\'\>+\/!$€`´\'%&');
	}
}


$GLOBALS['TL_HOOKS']['getGlossarPages'] = array();
$GLOBALS['TL_CTE']['texts']['glossar'] = 'Sioweb\ContentGlossar';
$GLOBALS['TL_CTE']['texts']['glossar_cloud'] = 'Sioweb\ContentGlossarCloud';

$GLOBALS['TL_HOOKS']['outputFrontendTemplate'][] = array('Sioweb\Glossar', 'searchGlossarTerms');
$GLOBALS['TL_HOOKS']['getSearchablePages'][] = array('Sioweb\Glossar','getSearchablePages');


if (in_array('news', \Config::getInstance()->getActiveModules())) {
	ClassLoader::addClasses(array('Sioweb\GlossarNews' => 'system/modules/Glossar/classes/GlossarNews.php'));
	$GLOBALS['TL_HOOKS']['clearGlossar']['news'] = array('Sioweb\GlossarNews','clearGlossar');
	$GLOBALS['TL_HOOKS']['getGlossarPages']['news'] = array('Sioweb\GlossarNews','generateUrl');
	$GLOBALS['TL_HOOKS']['cacheGlossarTerms']['news'] = array('Sioweb\GlossarNews','updateCache');
	$GLOBALS['TL_HOOKS']['glossarContent']['news'] = array('Sioweb\GlossarNews','glossarContent');
}


if (in_array('faq', \Config::getInstance()->getActiveModules())) {
	ClassLoader::addClasses(array('Sioweb\GlossarFAQ' => 'system/modules/Glossar/classes/GlossarFAQ.php'));
	$GLOBALS['TL_HOOKS']['clearGlossar']['faq'] = array('Sioweb\GlossarFAQ','clearGlossar');
	$GLOBALS['TL_HOOKS']['getGlossarPages']['faq'] = array('Sioweb\GlossarFAQ','generateUrl');
	$GLOBALS['TL_HOOKS']['cacheGlossarTerms']['faq'] = array('Sioweb\GlossarFAQ','updateCache');
	$GLOBALS['TL_HOOKS']['glossarContent']['faq'] = array('Sioweb\GlossarFAQ','glossarContent');
}


if (in_array('events', \Config::getInstance()->getActiveModules())) {
	ClassLoader::addClasses(array('Sioweb\GlossarEvents' => 'system/modules/Glossar/classes/GlossarEvents.php'));
	$GLOBALS['TL_HOOKS']['clearGlossar']['events'] = array('Sioweb\GlossarEvents','clearGlossar');
	$GLOBALS['TL_HOOKS']['getGlossarPages']['events'] = array('Sioweb\GlossarEvents','generateUrl');
	$GLOBALS['TL_HOOKS']['cacheGlossarTerms']['events'] = array('Sioweb\GlossarEvents','updateCache');
	$GLOBALS['TL_HOOKS']['glossarContent']['events'] = array('Sioweb\GlossarEvents','glossarContent');
}

if(empty($GLOBALS['glossar'])) {
	$GLOBALS['glossar'] = array();
}
$GLOBALS['glossar'] = array_merge($GLOBALS['glossar'],array(
	'css' => array(
		'maxWidth' => 450,
		'maxHeight' => 300,
	),
	'illegal' => '\-_\.&><;',
	'templates' => array(
		'ce_glossar',
		'glossar_default',
		'glossar_error',
		'glossar_layer'
	),
	'tables' => array('tl_settings','tl_sw_glossar','tl_content','tl_page','tl_glossar','tl_news_archive','tl_faq_category','tl_calendar'),
));


if(\Config::get('enableGlossar') == 1) {

	$uploadTypes = \Config::get('uploadTypes');
	if(strpos($uploadTypes,'json') === false) {
		$uploadTypes .= (strlen($uploadTypes)>0?',':'').'json';

		if(method_exists('Contao\Config','set')) {
			\Config::set('uploadTypes', $uploadTypes);
		} elseif(method_exists('Contao\Config','add')) {
			\Config::add('$GLOBALS[\'TL_CONFIG\'][\'uploadTypes\']', $uploadTypes);
		}
	}

	if(Input::get('rebuild_glossar') == 1 || \Config::get('disableGlossarCache') == 1) {
		$GLOBALS['TL_HOOKS']['modifyFrontendPage'][] = array('Sioweb\RebuildGlossar', 'prepareRebuild');
		$GLOBALS['TL_HOOKS']['modifyFrontendPage'][] = array('Sioweb\RebuildGlossar', 'rebuild');
		// $GLOBALS['TL_HOOKS']['indexPage'][] = array('Sioweb\RebuildGlossar', 'rebuild');
		$GLOBALS['TL_HOOKS']['clearGlossar'][] = array('Sioweb\RebuildGlossar', 'clearGlossar');
	}

	if(TL_MODE == 'FE') {
		$GLOBALS['TL_CSS'][] = 'web/bundles/siowebglossar/css/glossar.min.css|static';
		if(empty($GLOBALS['TL_CONFIG']['disableToolTips'])) {
			$GLOBALS['TL_JAVASCRIPT'][] = 'web/bundles/siowebglossar/js/glossar.js|static';
		}
	}

	if(Input::post('glossar') == 1) {
		$GLOBALS['TL_HOOKS']['initializeSystem'][] = array('Sioweb\Glossar', 'getGlossarTerm');
	}
}

$GLOBALS['TL_HOOKS']['replaceInsertTags'][] = array('Sioweb\Glossar', 'replaceGlossarInsertTags');
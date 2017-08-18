<?php

/**
 * Contao Open Source CMS
 */
namespace Sioweb;
use Contao;

/**
 * @file GlossarFAQ.php
 * @class GlossarFAQ
 * @author Sascha Weidner
 * @version 3.0.0
 * @package sioweb.contao.extensions.glossar
 * @copyright Sascha Weidner, Sioweb
 */
class GlossarFAQ extends \ModuleFaqList {

  public function __construct() {}

  public function compile() {}

  public function clearGlossar($time) {
    $this->import('Database');
    $this->Database->prepare("UPDATE tl_faq SET glossar = NULL,fallback_glossar = NULL,glossar_time = ? WHERE glossar_time != ?")->execute($time,$time);
  }

  public function glossarContent($item,$strContent,$template) {
    if(empty($item)) {
      return array();
    }

    $Faq = \FaqModel::findByAlias(\Input::get('items'));
    return $Faq->glossar;
  }

  public function updateCache($item,$arrTerms,$strContent) {
    preg_match_all('#'.implode('|',$arrTerms['both']).'#is', $strContent, $matches);
    $matches = array_unique($matches[0]);

    if(empty($matches)) {
      return;
    }

    $Faq = \FaqModel::findByAlias($item);
    $Faq->glossar = implode('|',$matches);
    $Faq->save();
  }

  public function generateUrl($arrPages) {
    $arrPages = array();

    $Faq = \FaqModel::findAll();
    if(empty($Faq)) {
      return array();
    }

    $arrFaq = array();
    while($Faq->next()) {
      if(!empty($Faq)) {
        $arrFaq[$Faq->pid][] = $this->generateFaqLink($Faq);
      }
    }

    $InactiveArchives = \GlossarFaqCategoryModel::findByPidsAndInactiveGlossar(array_keys($arrFaq));
    if(!empty($InactiveArchives)) {
      while($InactiveArchives->next()) {
        unset($arrFaq[$InactiveArchives->id]);
      }
    }
      
    if(empty($arrFaq)) {
      return array();
    }

    $FaqReader = \ModuleModel::findByType('faqreader');

    if(empty($FaqReader)) {
      return array();
    }

    $arrReader = array();

    while($FaqReader->next()) {
      $arrReader[$FaqReader->id] = deserialize($FaqReader->faq_categories);
    }

    $Content = \ContentModel::findBy(array("module IN ('".implode("','",array_keys($arrReader))."')"),array());

    if(empty($Content)) {
      return array();
    }

    $arrContent = array();

    while($Content->next()) {
      $arrContent[$Content->module] = $Content->pid;
    }

    $Article = \ArticleModel::findBy(array("tl_article.id IN ('".implode("','",$arrContent)."')"),array());

    if(empty($Article)) {
      return array();
    }

    $finishedIDs = $arrPages = array();

    while($Article->next()) {
      $domain = \Environment::get('base');
      $strLanguage = 'de';
      $objPages = $Article->getRelated('pid');

      $ReaderId = false;
      foreach($arrContent as $module => $mid) {
        if($mid == $Article->id) {
          $ReaderId = $module;
        }
      }

      foreach($arrReader[$ReaderId] as $faq_id) {
        if(in_array($faq_id,$finishedIDs)) {
          continue;
        }

        if(!empty($arrFaq[$faq_id])) {
          foreach($arrFaq[$faq_id] as $faq_domain) {
            $faq_domain = str_replace('.html','',$faq_domain);
            $arrPages['de'][] = $domain . static::generateFrontendUrl($objPages->row(), substr($faq_domain,strpos($faq_domain,'/')), $strLanguage);
          }
        }
        $finishedIDs[] = $faq_id;
      }
    }

    return $arrPages;
  }
}
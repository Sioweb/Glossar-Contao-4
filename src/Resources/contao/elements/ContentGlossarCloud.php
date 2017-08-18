<?php

/**
 * Contao Open Source CMS
 */

namespace Sioweb;
use Contao;

/**
 * @file ContentGlossarCloud.php
 * @class ContentGlossarCloud
 * @author Sascha Weidner
 * @version 3.0.0
 * @package sioweb.contao.extensions.glossar
 * @copyright Sascha Weidner, Sioweb
 */

class ContentGlossarCloud extends \ContentElement {
  
  /**
   * Template
   * @var string
   */
  protected $strTemplate = 'ce_glossar_cloud';

  private $pages = array();

  private $countTerms = array();

  /**
   * Return if there are no files
   * @return string
   */
  public function generate() {
    global $objPage;

    $pageGlossar = explode('|',$objPage->glossar);
    $Page = \PageModel::findBy(array("glossar LIKE '%|".implode("|%' OR glossar LIKE '%|",$pageGlossar)."|%'"),array());
    
    if(empty($Page)) {
      return;
    }

    $countTerms = array();
    $arrPages = array();
    while($Page->next()) {
      $ap = array(
        'id' => $Page->id,
        'weight' => 0,
        'title' => $Page->title,
        'description' => $Page->description,
        'glossar' => explode('|',$Page->glossar),
        'fallback_glossar' => explode('|',$Page->fallback_glossar),
        'url' => $this->generateFrontendUrl($Page->row())
      );

      foreach($ap['glossar'] as $term) {
        if($term != '') {
          $countTerms[$term] = !empty($term)?($countTerms[$term]+1):1;
        }
      }

      if($ap['id'] != $objPage->id) {
        $arrPages[] = $ap;
      }
    }

    asort($countTerms);
    $this->countTerms = $countTerms;

    $max = 0;
    foreach($arrPages as &$p_page) {
      foreach($countTerms as $term => $count) {
        if(in_array($term,$p_page['glossar'])) {
          $p_page['weight']++;
        }
      }

      if($p_page['weight'] > $max) {
        $max = $p_page['weight'];
      }
    }

    $this->max = $max;

    foreach($arrPages as &$p_page) {
      $x = $p_page['weight']/$max;
      $p_page['weight'] = 1+$x;
    }

    $this->pages = $arrPages;

    if(!isset($_GET['items']) && $GLOBALS['TL_CONFIG']['useAutoItem'] && isset($_GET['auto_item'])) {
      \Input::setGet('items', \Input::get('auto_item'));
    }

    if(!isset($_GET['alpha']) && $GLOBALS['TL_CONFIG']['useAutoItem'] && isset($_GET['auto_item'])) {
      \Input::setGet('alpha', \Input::get('auto_item'));
    }

    return parent::generate();
  }

  public function compile()  {
    if($this->glossar_terms) {
      // echo 'Terms: '.$this->glossar_terms;
    } elseif(\Input::get('items') != '') {
      // echo 'Items: '.\Input::get('items');
    } else {
      $this->Template->pages = $this->pages;
    }

    $this->Template->unique = count(array_unique($this->countTerms));
    $this->Template->countTerms = $this->countTerms;
  }

  private function getRootPage($id) {
    $Page = \PageModel::findByPk($id);
    if(empty($Page)) {
      return;
    }

    if($Page->type == 'root') {
      return $Page;
    }
    
    return $this->getRootPage($Page->pid);
  }
}
<?php

namespace Sioweb\Gitter\ContentElements;
use Sioweb\Gitter\Models;
use Contao;

class ContentGitterLicenses extends \ContentElement {

	/**
	 * Template
	 * @var string
	 */
	protected $strTemplate = 'content_gitter_default';

	/**
	 * Parse the template
	 *
	 * @return string
	 */
	public function generate() {
		$this->import('FrontendUser','User');

		if(!isset($_GET['item']) && $GLOBALS['TL_CONFIG']['useAutoItem'] && isset($_GET['auto_item'])) {
			\Input::setGet('item', \Input::get('auto_item'));
		}

		if(TL_MODE == 'BE') {
			$this->strTemplate = 'be_wildcard';

			/** @var \BackendTemplate|object $objTemplate */
			$objTemplate = new \BackendTemplate($this->strTemplate);

			$this->Template = $objTemplate;
			$this->Template->title = 'Kunden Lizenzliste';
		}
		return parent::generate();
	}


	/**
	 * Generate the content element
	 */
	protected function compile() {
		global $objPage;

		if(TL_MODE == 'BE') {
			return;
		}

		$arrProducts = array();
		$arrLizenses = array();

		if(\Input::get('item') != '') {
			$GitterLicenses = \GitterLicenseModel::findByPk(\Input::get('item'));
			if(empty($arrProducts[$licenseObj->product])) {
				$arrProducts[$licenseObj->product] = array();
				$arrLizenses[$licenseObj->product] = array();
			}
			$arrProducts[$licenseObj->product][] = $GitterLicenses->row();
		} else {

			$GitterLicenses = \GitterLicenseModel::findByCustomer($this->User->id);
			
			while($GitterLicenses->next()) {
				if(empty($arrProducts[$licenseObj->product])) {
					$arrProducts[$licenseObj->product] = array();
					$arrLizenses[$licenseObj->product] = array();
				}
				$arrProducts[$licenseObj->product][] = $GitterLicenses->row();
			}
		}

		foreach($arrProducts as $product => $licenses) {
			foreach($licenses as $license) {
				$licenseObj = new \FrontendTemplate('content_gitter_license'.(\Input::get('item')!=''?'_edit':''));
				$licenseObj->setData($license);

				$domain = str_replace(array('https://','http://','www.'),array('','',''),$license['domain']);
				$licenseObj->hash = 's_'.md5('contao '.$this->User->company.'-'.$license['product'].'-'.$domain.'-'.$license['start'].'-'.$license['stop']);
				$licenseObj->url = 'https://www.sioweb.de/gitter/contao/'.standardize($this->User->company).'/'.$licenseObj->hash.'.git';

				$licenseObj->editLink = $this->generateFrontendUrl($objPage->row(), (($GLOBALS['TL_CONFIG']['useAutoItem'] && !$GLOBALS['TL_CONFIG']['disableAlias']) ?  '/' : '/item/').$license['id']);
				$Valid = array();
				if(!empty($license['start'])) {
					$Valid[] = date('d.m.Y',$license['start']);
				}
				if(!empty($license['end'])) {
					$Valid[] = date('d.m.Y',$license['end']);
				}

				$Valid = implode(' bis ',$Valid);
				$licenseObj->valid = $Valid;
				$arrLicenses[$licenseObj->product][] = $licenseObj->parse();
			}
		}

		$this->Template->licenses = $arrLicenses;
	}
}
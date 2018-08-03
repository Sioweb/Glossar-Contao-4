<?php

$GLOBALS['TL_DCA']['tl_content']['palettes']['gitter_licenses'] = '{type_legend},type,headline;{gitter_legend},show_customer_licenses;{template_legend:hide},customTpl;{protected_legend:hide},protected;{expert_legend:hide},guests,cssID,space;{invisible_legend:hide},invisible,start,stop'; 

$GLOBALS['TL_DCA']['tl_content']['fields']['show_customer_licenses'] = array(
	'label'                   => &$GLOBALS['TL_LANG']['tl_content']['show_customer_licenses'],
	'inputType'               => 'checkbox',
	'fields'				  => array('type','dma_eg_data'=>array('legend','headline')),
	'eval'					  => array('submitOnChange'=>true),
	'sql'                     => "char(1) NOT NULL default ''",
);
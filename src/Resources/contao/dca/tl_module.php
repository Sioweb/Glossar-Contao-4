<?php

/**
 * Contao Open Source CMS
 */

/**
 * @file tl_module.php
 * @author Sascha Weidner
 * @version 3.0.0
 * @package sioweb.contao.extensions.glossar
 * @copyright Sascha Weidner, Sioweb
 */

$GLOBALS['TL_DCA']['tl_module']['palettes']['glossar_pagination']    = '{title_legend},name,headline,type;{glossar_legend},glossar,addOnlyTrueLinks;{protected_legend:hide},protected;{expert_legend:hide},guests,cssID,space';
$GLOBALS['TL_DCA']['tl_module']['palettes']['glossar_cloud']         = '{title_legend},name,headline,type;{glossar_legend},glossar,glossar_items,glossar_max_level,glossar_disable_domains;{protected_legend:hide},protected;{expert_legend:hide},guests,cssID,space';

$GLOBALS['TL_DCA']['tl_module']['fields']['glossar'] = array
(
    'label'                   => &$GLOBALS['TL_LANG']['tl_module']['glossar'],
    'inputType'               => 'select',
    'foreignKey'              => 'tl_glossar.title',
    'eval'                    => array('tl_class'=>'clr','includeBlankOption'=>true),
    'sql'                     => "varchar(20) NOT NULL default ''"
);

$GLOBALS['TL_DCA']['tl_module']['fields']['glossar_items'] = array
(
    'label'                   => &$GLOBALS['TL_LANG']['tl_module']['glossar_items'],
    'inputType'               => 'text',
    'eval'                    => array('tl_class'=>'w50'),
    'sql'                     => "int(11) NOT NULL default '0'"
);

$GLOBALS['TL_DCA']['tl_module']['fields']['glossar_max_level'] = array
(
    'label'                   => &$GLOBALS['TL_LANG']['tl_module']['glossar_max_level'],
    'default'                 => 3,
    'inputType'               => 'text',
    'eval'                    => array('tl_class'=>'w50'),
    'sql'                     => "int(11) NOT NULL default '3'"
);

$GLOBALS['TL_DCA']['tl_module']['fields']['glossar_disable_domains'] = array
(
  'label'                   => &$GLOBALS['TL_LANG']['tl_module']['glossar_disable_domains'],
  'inputType'               => 'checkboxWizard',
  'options_callback'        => array('tl_glossar_module', 'loadRootPages'),
  'eval'                    => array('tl_class'=>'clr long','multiple'=>true),
  'sql'                     => "text NULL"
);

$GLOBALS['TL_DCA']['tl_module']['fields']['sortGlossarBy'] = array
(
    'label'                   => &$GLOBALS['TL_LANG']['tl_module']['sortGlossarBy'],
    'default'                 => 'alias',
    'inputType'               => 'select',
    'options'                 => array('id', 'id_desc', 'date', 'date_desc', 'alias', 'alias_desc' ),
    'reference'               => &$GLOBALS['glossar']['sortGlossarBy'],
    'eval'                    => array('tl_class'=>'w50'),
    'sql'                     => "varchar(20) NOT NULL default ''"
);

$GLOBALS['TL_DCA']['tl_module']['fields']['addOnlyTrueLinks'] = array
(
    'label'                   => &$GLOBALS['TL_LANG']['tl_module']['addOnlyTrueLinks'],
    'exclude'                 => true,
    'inputType'               => 'checkbox',
    'eval'                    => array('submitOnChange'=>true),
    'sql'                     => "char(1) NOT NULL default ''"
);

class tl_glossar_module extends Backend {

    public function loadRootPages() {
        $Page = \PageModel::findByType('root');
        if(empty($Page)) {
            return array();
        }

        $arrPages = array();
        while($Page->next()) {
            $arrPages[$Page->id] = $Page->dns.' <span style="color: #ccc;font-size:10px;display:inline-block;">['.$Page->title.']</span>';
        }
        return $arrPages;
    }
}
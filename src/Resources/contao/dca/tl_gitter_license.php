<?php

/**
 * Contao Open Source CMS
 */

/**
 * @file tl_gitter_license.php
 * @author Sascha Weidner
 * @version 3.0.0
 * @package sioweb.contao.extensions.glossar
 * @copyright Sascha Weidner, Sioweb
 */


$GLOBALS['TL_DCA']['tl_gitter_license'] = array(

  // Config
  'config' => array
  (
    'dataContainer'               => 'Table',
    'switchToEdit'                => true,
    'enableVersioning'            => true,
    'sql' => array
    (
      'keys' => array
      (
        'id' => 'primary'
      )
    ),
    'onsubmit_callback' => array
    (
      array('tl_gitter_license', 'scheduleUpdate')
    )
  ),

  // List
  'list' => array
  (
    'sorting' => array
    (
      'mode'                    => 1,
      'fields'                  => array('product','domain'),
      'flag'                    => 1,
      'panelLayout'             => 'sort,search,limit'
    ),
    'label' => array
    (
      'fields'                  => array('product','domain'),
      'label_callback'          => array('tl_gitter_license', 'getLabels'),
      'format'                  => '%s, %s',
    ),
    'global_operations' => array
    (
      'all' => array
      (
        'label'               => &$GLOBALS['TL_LANG']['MSC']['all'],
        'href'                => 'act=select',
        'class'               => 'header_edit_all',
        'attributes'          => 'onclick="Backend.getScrollOffset()" accesskey="e"'
      ),
    ),
    'operations' => array
    (
      'edit' => array
      (
        'label'               => &$GLOBALS['TL_LANG']['tl_gitter_license']['edit'],
        'href'                => 'act=edit',
        'icon'                => 'edit.gif',
      ),
      'delete' => array
      (
        'label'               => &$GLOBALS['TL_LANG']['tl_gitter_license']['delete'],
        'href'                => 'act=delete',
        'icon'                => 'delete.gif',
        'attributes'          => 'onclick="if(!confirm(\'' . $GLOBALS['TL_LANG']['MSC']['deleteConfirm'] . '\'))return false;Backend.getScrollOffset()"'
      ),
    )
  ),

  // Palettes
  'palettes' => array
  (
    'default'                     => '{title_legend},product,customer,domain,dev_domain,start,stop,static_key',
  ),

  // Fields
  'fields' => array
  (
    'id' => array
    (
      'sql'                     => "int(10) unsigned NOT NULL auto_increment"
    ),
    'tstamp' => array
    (
      'sql'                     => "int(10) unsigned NOT NULL default '0'"
    ),
    'product' => array
    (
      'label'                   => &$GLOBALS['TL_LANG']['tl_gitter_license']['product'],
      'exclude'                 => true,
      'inputType'               => 'select',
      'options'                 => array('Glossar'=>'Glossar','Glossar3'=>'Glossar 3','VersionsNPrices'=>'Versionen und Preise'),
      'eval'                    => array('mandatory'=>true, 'maxlength'=>255, 'tl_class'=>'w50'),
      'sql'                     => "varchar(255) NOT NULL default ''"
    ),
    'customer' => array
    (
      'label'                   => &$GLOBALS['TL_LANG']['tl_gitter_license']['customer'],
      'exclude'                 => true,
      'search'                  => true,
      'inputType'               => 'select',
      'foreignKey'              => 'tl_member.company',
      'eval'                    => array('doNotCopy'=>true, 'mandatory'=>true, 'chosen'=>true, 'includeBlankOption'=>true, 'tl_class'=>'w50'),
      'sql'                     => "int(10) unsigned NOT NULL default '0'",
      'relation'                => array('type'=>'hasOne', 'load'=>'eager')
    ),
    'domain' => array
    (
      'label'                   => &$GLOBALS['TL_LANG']['tl_gitter_license']['domain'],
      'exclude'                 => true,
      'inputType'               => 'text',
      'eval'                    => array('mandatory'=>true, 'maxlength'=>255, 'tl_class'=>'w50'),
      'sql'                     => "varchar(255) NOT NULL default ''"
    ),
    'dev_domain' => array
    (
      'label'                   => &$GLOBALS['TL_LANG']['tl_gitter_license']['dev_domain'],
      'exclude'                 => true,
      'inputType'               => 'text',
      'eval'                    => array('mandatory'=>true, 'maxlength'=>255, 'tl_class'=>'w50'),
      'sql'                     => "varchar(255) NOT NULL default ''"
    ),
    'start' => array
    (
      'exclude'                 => true,
      'label'                   => &$GLOBALS['TL_LANG']['tl_gitter_license']['start'],
      'inputType'               => 'text',
      'eval'                    => array('rgxp'=>'datim', 'datepicker'=>true, 'tl_class'=>'w50 wizard'),
      'sql'                     => "varchar(10) NOT NULL default ''"
    ),
    'stop' => array
    (
      'exclude'                 => true,
      'label'                   => &$GLOBALS['TL_LANG']['tl_gitter_license']['stop'],
      'inputType'               => 'text',
      'eval'                    => array('rgxp'=>'datim', 'datepicker'=>true, 'tl_class'=>'w50 wizard'),
      'sql'                     => "varchar(10) NOT NULL default ''"
    ),
    'static_key' => array
    (
      'label'                   => &$GLOBALS['TL_LANG']['tl_gitter_license']['static_key'],
      'exclude'                 => true,
      'inputType'               => 'text',
      'eval'                    => array('maxlength'=>255, 'tl_class'=>'w50'),
      'sql'                     => "varchar(255) NOT NULL default ''"
    )
  )
);

class tl_gitter_license extends Backend {

  public function __construct() {
    $this->Gitter = new \Sioweb\Gitter\Classes\Gitter();
  }

  public function scheduleUpdate(DataContainer $dc) {

    $customer = \MemberModel::findById($dc->activeRecord->customer);
    $domain  = str_replace(array('https://','http://','www.'),array('','',''),$dc->activeRecord->domain);
    $hash = 's_'.md5('contao '.$customer->company.'-'.$dc->activeRecord->product.'-'.$dc->activeRecord->domain.'-'.$dc->activeRecord->start.'-'.$dc->activeRecord->stop);
    if(!empty($dc->activeRecord->static_key)) {
      $hash = $dc->activeRecord->static_key;
    }

    $create = false;
    if(!is_dir(TL_ROOT.'/web/gitter/contao/'.standardize($customer->company).'/'.$hash)) {
      $create = true;
    }

    $Version = 1;

    if(is_file(TL_ROOT.'/web/gitter/contao/'.standardize($customer->company).'/'.$hash.'/composer.json')) {
      $string = file_get_contents(TL_ROOT.'/web/gitter/contao/'.standardize($customer->company).'/'.$hash.'/composer.json');
      $Composer = json_decode($string, true);

      $Version = floatval($Composer['version'])+1;
    }

    $domain  = str_replace(array('https://','http://','www.'),array('','',''),$dc->activeRecord->domain);
    $dev_domain  = str_replace(array('https://','http://','www.'),array('','',''),$dc->activeRecord->dev_domain);
    
    $fTemplate = new FrontendTemplate('gitter_license');
    $fTemplate->setData(array(
      'version' => $Version,
      'system' => 'contao',
      'customer' => $customer->company,
      'product' => $dc->activeRecord->product,
      'hash' => $hash,
      'dev_domain' => $dev_domain,
      'stop' => $dc->activeRecord->stop,
      'start' => $dc->activeRecord->start
    ));

    $File = new \File('web/gitter/contao/'.standardize($customer->company).'/'.$hash.'/src/'.$dc->activeRecord->product.'.php');
    $Code = $fTemplate->parse();
    $Code = $this->Gitter->removeKommentare($Code);
    $Code = $this->Gitter->replaceVariablen($Code);
    $Code = $this->Gitter->replaceFunction($Code);
    $Code = $this->Gitter->removeWhitespaces($Code);
    $Code = trim(preg_replace('/\t+/', '', $Code));
    
    $Code = "<?php \n/**\n * Das Entfernen oder Manipulieren dieser Datei ist nicht gestattet.\n */\nnamespace Sioweb\License;\n".$Code;

    $File->write($Code);
    $File->close();

    $cTemplate = new FrontendTemplate('gitter_composer');
    $cTemplate->setData(array(
      'version' => $Version,
      'system' => 'contao',
      'customer' => $customer->company,
      'product' => $dc->activeRecord->product,
      'hash' => $hash,
      'dev_domain' => $dev_domain,
      'stop' => $dc->activeRecord->stop,
      'start' => $dc->activeRecord->start
    ));

    $File = new \File('web/gitter/contao/'.standardize($customer->company).'/'.$hash.'/composer.json');
    $File->write($cTemplate->parse());
    $File->close();

    if($create) {
      $shellTemplate = new FrontendTemplate('gitter_create');
      $shellTemplate->setData(array(
        'version' => $Version,
        'system' => 'contao',
        'customer' => $customer->company,
        'product' => $dc->activeRecord->product,
        'hash' => $hash,
        'dev_domain' => $dev_domain,
        'stop' => $dc->activeRecord->stop,
        'start' => $dc->activeRecord->start
      ));
    } else {
      $shellTemplate = new FrontendTemplate('gitter_update');
      $shellTemplate->setData(array(
        'version' => $Version,
        'system' => 'contao',
        'customer' => $customer->company,
        'product' => $dc->activeRecord->product,
        'hash' => $hash,
        'dev_domain' => $dev_domain,
        'stop' => $dc->activeRecord->stop,
        'start' => $dc->activeRecord->start
      ));
    };
   
    exec('cd gitter/contao/'.standardize($customer->company).'/'.$hash.' && ls -la &&'.$shellTemplate->parse().' 2>&1',$output);
  }


  public function getLabels($row, $label) {

    $customer = \MemberModel::findById($row['customer']);
    $domain  = str_replace(array('https://','http://','www.'),array('','',''),$row['domain']);

    $hash = 's_'.md5('contao '.$customer->company.'-'.$row['product'].'-'.$domain.'-'.$row['start'].'-'.$row['stop']);
    if(!empty($row['static_key'])) {
      $hash = $row['static_key'];
    }
    return sprintf('%s %s %s<br><span style="color:#b3b3b3; padding-left:3px;">[%s]</span>',$row['product'],$row['domain'],$row['dev_domain'],'https://www.sioweb.de/gitter/contao/'.standardize($customer->company).'/'.$hash.'.git');
  }
}

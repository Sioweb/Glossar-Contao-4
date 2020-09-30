<?php

/**
 * Contao Open Source CMS
 */

declare(strict_types=1);

namespace Sioweb\Glossar\Classes;

use Contao\BackendModule;
use Contao\BackendTemplate;
use Contao\Config;
use Contao\Environment;
use Contao\Input;
use Contao\System;
use Contao\PageModel;

/**
 * @file Status.php
 * @class Status
 * @author Sascha Weidner
 * @version 2.3
 * @package sioweb.contao.extensions.glossar
 * @copyright Sascha Weidner, Sioweb
 */

class Status extends BackendModule
{

    protected $strTemplate = 'be_glossar_status';

    public function generate()
    {
        if (empty($GLOBALS['glossar']['tables'])) {
            return '';
        }
        return parent::generate();
    }

    public function compile()
    {
        $arrData = [];
        if (Input::get('import_status') == 1 || Input::post('act') == 'import_status') {
            $this->import('BackendUser', 'User');
            $class = $this->User->uploader;

            $FileData = $Import = [];

            if (!class_exists($class) || $class == 'DropZone') {
                $class = 'FileUpload';
            }

            $objUploader = new $class();
            $arrUploaded = $objUploader->uploadTo('system/tmp');

            $arrFiles = [];

            if (Input::post('act') == 'import_status') {
                foreach ($arrUploaded as $strFile) {
                    $arrFiles[] = $strFile;
                }
                $arrData = $this->readFile($arrFiles[0]);
            }
            $this->Template->fields = $objUploader->generateMarkup();
        }

        if (empty($arrData)) {
            $data = [];
            foreach ($GLOBALS['glossar']['tables'] as $key => $table) {
                $this->loadDataContainer($table);
                $this->loadLanguageFile($table);

                $Config = $this->loadGlossarFields($table);
                foreach ($Config as $key => $field) {
                    $fieldName = $field;

                    if (!empty($GLOBALS['TL_DCA'][$table]['fields'][$field]['eval']['gsLabel'])) {
                        $fieldName = $GLOBALS['TL_DCA'][$table]['fields'][$field]['eval']['gsLabel'];
                    }

                    $data[$table][$fieldName] = [
                        'field' => $field,
                        'title' => &$GLOBALS['TL_LANG'][$table][$field][0],
                        'description' => &$GLOBALS['TL_LANG'][$table][$field][1],
                        'tables' => [$table],
                        'type' => $GLOBALS['TL_DCA'][$table]['fields'][$field]['inputType'],
                    ];
                }
            }

            $tmpData = $data;

            foreach ($tmpData as $table => $fields) {
                foreach ($data as $_table => $_fields) {

                    if ($table === $_table) {
                        continue;
                    }

                    foreach ($_fields as $field => $fData) {
                        if (!empty($fields[$field])) {
                            $data[$table][$field]['tables'][] = $_table;
                        }
                        $arrData[$field] = $fData;
                    }
                }
            }

            uasort($arrData, function ($a, $b) {
                return (count($b['tables']) - count($a['tables']));
            });

            foreach ($arrData as $field => &$fieldData) {
                $tables = array_diff($fieldData['tables'], ['tl_settings']);
                if (!empty($tables[0])) {
                    foreach ($tables as $key => $table) {
                        switch ($fieldData['type']) {
                            case 'pageTree':
                                $SQL = $this->Database->prepare("SELECT * FROM " . $table . " WHERE " . $fieldData['field'] . " != 0")->execute();

                                if (!empty($SQL)) {
                                    while ($SQL->next()) {
                                        $Data = PageModel::findByPk($SQL->$fieldData['field']);
                                        if (!empty($Data)) {
                                            if (empty($fieldData['data'][$table][$Data->id])) {
                                                $fieldData['data'][$table][$Data->id] = $this->loadTemplate($fieldData['type'], $Data->row(), $table);
                                            }
                                        }
                                    }
                                }
                                break;
                            default:
                                $SQL = $this->Database->prepare("SELECT * FROM " . $table . " WHERE " . $fieldData['field'] . " != '' AND " . $fieldData['field'] . " != '0'")->execute();

                                if (!empty($SQL)) {
                                    while ($SQL->next()) {
                                        $Data = $SQL->$fieldData['field'];
                                        if (!empty($GLOBALS['glossar'][$fieldData['field']][$Data])) {
                                            $Data = $GLOBALS['glossar'][$fieldData['field']][$Data];
                                        }
                                        $fieldData['data'][$table][$SQL->id] = $this->loadTemplate($fieldData['type'], $Data, $table, $table . ': ' . $SQL->title);
                                    }
                                }

                                break;
                        }
                    }
                }

                if (($Data = Config::get($field))) {
                    switch ($fieldData['type']) {
                        case 'pageTree':
                            $Data = PageModel::findByPk($Data);
                            if (!empty($Data)) {
                                $Data = $Data->row();
                            }
                            break;
                    }

                    $fieldData['data']['tl_settings'][0] = $this->loadTemplate($fieldData['type'], $Data, 'tl_settings', 'Allgemeine Einstellungen');
                } elseif (in_array('tl_settings', $fieldData['tables'])) {
                    $fieldData['data']['tl_settings'][0] = $this->loadTemplate($fieldData['type'], 0, 'tl_settings', 'Allgemeine Einstellungen');
                }
            }

            if (Input::post('act') == 'glossar_Status') {
                header("Content-type: application/download");
                header('Content-Disposition: attachment; filename="glossar_status.json"');
                echo json_encode($arrData);
                die();
            }
        }

        $backLink = str_replace(['do=glossar_status'], [''], Environment::get('request'));

        if (Input::get('import_status') == '1') {
            $backLink = str_replace(['&import_status=1'], [''], Environment::get('request'));
        }

        $this->Template->backLink = $backLink;
        $this->Template->settings = $this->loadData($glossar);
        $this->Template->glossar = $arrData;
    }

    private function readFile($file)
    {
        $data = '';
        if (($handle = fopen(TL_ROOT . '/' . $file, "r")) !== false) {
            $data = json_decode(fgets($handle), true);
            fclose($handle);
        }
        return $data;
    }

    private function loadGlossarFields($table)
    {
        $arrConfig = [];
        $found = false;

        foreach ($GLOBALS['TL_DCA'][$table]['palettes'] as $type => $palette) {
            if (!is_string($palette)) {
                continue;
            }

            $config = explode(';', $palette);

            foreach ($config as $key => $fieldset) {
                if (strpos($fieldset, 'glossar_legend') !== false || $table === 'tl_sw_glossar') {
                    $found = true;
                    $fields = explode(',', preg_replace('|{[^}]*},|', '', $fieldset));
                    $arrFields = [];

                    foreach ($fields as $key => $field) {
                        if (!empty($GLOBALS['TL_DCA'][$table]['fields'][$field]['label']) && !$GLOBALS['TL_DCA'][$table]['fields'][$field]['eval']['gsIgnore']) {
                            $arrFields[] = $field;
                        }
                    }

                    $arrConfig = array_merge($arrConfig, $arrFields);
                }
            }
        }

        if (!$found) {
            return [];
        }

        return array_merge($arrConfig, $this->loadSubFields($arrConfig, $table));
    }

    private function loadSubFields($arr, $table)
    {
        $arrSubConfig = [];
        foreach ($arr as $key => $field) {
            if (empty($GLOBALS['TL_DCA'][$table]['fields'][$field]['label']) || $GLOBALS['TL_DCA'][$table]['fields'][$field]['eval']['gsIgnore']) {
                continue;
            }
        }

        if (!empty($GLOBALS['TL_DCA'][$table]['subpalettes'][$field])) {
            $arrSubConfig = explode(',', $GLOBALS['TL_DCA'][$table]['subpalettes'][$field]);
            $arrSubConfig = array_merge($arrSubConfig, $this->loadSubFields($arrSubConfig, $table));
        }
        return $arrSubConfig;
    }

    private function loadTemplate($type, $data, $table, $headline = false)
    {
        $template = 'be_glossar_type_valid';

        switch ($type) {
            case 'pageTree':
                $template = 'be_glossar_type_page';
                break;
            case 'text':
                $template = 'be_glossar_type_content';
                break;
            case 'select':
                $template = 'be_glossar_type_select';
                break;
        }

        $objGlossar = new BackendTemplate($template);

        if (!is_array($data)) {
            $objGlossar->data = preg_replace('|,(?! )|', ', ', $data);
        } else {
            $objGlossar->data = $data;
        }

        $objGlossar->table = $table;
        $objGlossar->headline = $headline;
        return $objGlossar->parse();
    }

    private function loadData()
    {
    }
}

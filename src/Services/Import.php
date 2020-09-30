<?php

/**
 * Contao Open Source CMS
 */

declare(strict_types=1);

namespace Sioweb\Glossar\Services;

use Contao\BackendTemplate;
use Contao\Config;
use Contao\CoreBundle\Framework\ContaoFramework;
use Contao\Environment;
use Contao\Input;
use Contao\Message;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * @file Import.php
 * @class Import
 * @author Sascha Weidner
 * @package sioweb.contao.extensions.glossar
 * @copyright Sascha Weidner, Sioweb
 */

class Import
{

    /**
     * @var ContaoFramework
     */
    private $framework;

    /**
     * @var License
     */
    private $license;

    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    /**
     * @var SessionInterface
     */
    private $session;

    public function __construct(ContaoFramework $framework, License $license, TokenStorageInterface $tokenStorage, SessionInterface $session)
    {
        $this->framework = $framework;
        $this->license = $license;
        $this->tokenStorage = $tokenStorage;
        $this->session = $session;
    }

    public function import()
    {
    }

    public function run()
    {
        $FirstID = $Stage = 0;

        $token = $this->tokenStorage->getToken();
        $user = $token->getUser();

        $class = $user->uploader;

        $FileData = $Import = [];

        // See #4086 and #7046
        if (!class_exists($class) || $class == 'DropZone') {
            $class = 'FileUpload';
        }

        $objUploader = new $class();

        if (Input::post('FORM_SUBMIT') == 'tl_csv_import') {
            $arrUploaded = $objUploader->uploadTo('system/tmp');
            $arrFiles = [];

            foreach ($arrUploaded as $strFile) {
                $arrFiles[] = $strFile;
            }

            if (Input::post('update_glossar') != '') {
                $Stage = Input::post('update_glossar') + 1;
            }

            if (Input::post('glossar_kill_all') == '1') {
                $this->Database->execute("TRUNCATE TABLE tl_glossar");
                $this->Database->execute("TRUNCATE TABLE tl_sw_glossar");
                $this->Database->execute("DELETE FROM tl_content WHERE ptable = 'tl_sw_glossar'");
            }

            switch (Input::post('update_glossar')) {
                case 1:
                    $FileData = $this->decode_array($this->session->get('glossar_file'));
                    $Import = $this->decode_array($this->session->get('glossar_import'));
                    $f = $FirstID = $this->session->get('glossar_first_id');

                    $arrFD = [];
                    foreach ($Import['update']['tl_glossar'] as $glossar => $value) {
                        if (in_array($glossar, Input::post('update'))) {
                            $arrFD[$glossar] = $value;
                        }
                    }

                    $Import['update']['tl_glossar'] = $arrFD;
                    $arrFD = null;

                    if (!empty($Import['insert']['tl_glossar'])) {
                        foreach ($Import['insert']['tl_glossar'] as $alias => &$gdata) {
                            $gdata['id'] = $f++;
                        }
                    }

                    if (Input::post('glossar_update_action') <= 1) {
                        $Update = $this->updateGlossarData($FileData, $Import['update']['tl_glossar']);
                        $Update['insert'] = array_merge($Update['insert']['tl_glossar'], (array) $Import['insert']['tl_glossar']);
                    } else {
                        $Update['insert'] = (array) $Import['update']['tl_glossar'];
                        foreach ($Update['insert'] as $glossar => $insert) {
                            unset($Update['insert'][$glossar]['tl_sw_glossar']);
                        }
                    }

                    $insertedTerms = $this->importTermData($FileData, $Update['insert']);
                    $this->importContentData($FileData, $insertedTerms);

                    unset($Import['update']['tl_glossar']);
                    unset($Import['insert']['tl_glossar']);
                    break;
                default:
                    $FileData = $this->decode_array($this->readFile($arrFiles[0]));
                    $this->session->set('glossar_file', $FileData);
                    $Import = $this->importGlossarData($FileData);
                    if (!empty($Import['insert'])) {
                        $FirstID = $this->insertData($Import['insert'], 'tl_glossar');
                        $TermIDs = $this->importTermData($FileData, $Import['insert'], $FirstID);
                        $this->importContentData($FileData, $TermIDs);
                    }
                    break;
            }

            if (!empty($Import['update'])) {
                $this->session->set('glossar_first_id', $FirstID);
                $this->session->set('glossar_import', $Import);
            } else {
                $Stage = 2;
            }
        } else {
            $arrFiles = explode(', ', (string) $this->session->get('uploaded_themes'));
        }

        $this->Template = new BackendTemplate('be_glossar_import');
        $this->Template->setData([
            'import' => $Import,
            'stage' => $Stage,
            'update_action' => Input::post('glossar_update_action'),
            'maxFileSize' => Config::get('maxFileSize'),
            'messages' => Message::generate(),
            'fields' => $objUploader->generateMarkup(),
            'action' => ampersand(Environment::get('request'), true),
        ]);

        return $this->Template->parse();
    }

    /* Eingabe (Nicer JSON) */
    public function encode_array($input)
    {
        foreach ($input as &$value) {
            if (is_array($value)) {
                $value = $this->encode_array($value);
            } else {
                $value = addslashes(htmlentities((string)$value));
            }
        }
        return $input;
    }

    /* Ausgabe */
    public function decode_array($input)
    {
        foreach ($input as $key => &$value) {
            if (is_array($value)) {
                $value = $this->decode_array($value);
            } else {
                $value = html_entity_decode(stripslashes((string)$value));
            }
        }
        return $input;
    }

    /* Nur das noetigste und keine Standardwerte speichern. */
    public function cleanArray($input)
    {
        foreach ($input as &$value) {
            if (is_array($value)) {
                $value = $this->cleanArray($value);
            }
        }
        return array_filter($input, function ($item) {
            return $item !== null && $item !== '' && $item !== '0' && !in_array($item, ['a:2:{i:0;s:0:"";i:1;s:0:"";}', 'a:2:{s:4:"unit";s:2:"h1";s:5:"value";s:0:"";}', 'com_default']);
        });
    }
}

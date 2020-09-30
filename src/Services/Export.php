<?php

/**
 * Contao Open Source CMS
 */

declare(strict_types=1);

namespace Sioweb\Glossar\Services;

use Contao\Config;
use Contao\Input;
use Contao\BackendTemplate;
use Contao\Environment;
use Date;
use Sioweb\Glossar\Entity\Glossar as GlossarEntity;
use Sioweb\Glossar\Entity\Terms as TermsEntity;
use Contao\CoreBundle\Framework\ContaoFramework;
use Sioweb\Glossar\Models\ContentModel as GlossarContentModel;

/**
 * @file Export.php
 * @class Export
 * @author Sascha Weidner
 * @package sioweb.contao.extensions.glossar
 * @copyright Sascha Weidner, Sioweb
 */

class Export
{

    /**
     * @var ContaoFramework
     */
    private $framework;

    /**
     * @var License
     */
    private $license;

    private $entityManager;

    public function __construct(ContaoFramework $framework, License $license, $entityManager)
    {
        $this->framework = $framework;
        $this->license = $license;
        $this->entityManager = $entityManager;
    }

    public function export()
    {
    }

    public function run()
    {
        $objGlossar = new BackendTemplate('be_glossar_export');
        $objGlossar->setData([
            'lickey' => true,
            'headline' => 'Export',
            'glossarMessage' => '',
            'glossarSubmit' => 'Export',
            'glossarLabel' => 'Format wählen',
            'glossarHelp' => 'Bitte wählen Sie das Format aus, mit der der Exporter Ihre Einträge exportieren soll.',
            'action' => ampersand(Environment::get('request'), true),
        ]);

        $objGlossar->lickey = $this->license->valid();

        if (!$objGlossar->lickey) {
            return $objGlossar->parse();
        }

        $GlossarRepository = $this->entityManager->getRepository(GlossarEntity::class);
        $TermRepository = $this->entityManager->getRepository(TermsEntity::class);

        if (($ExportType = Input::get('glossar_export')) != '') {
            $JSON = [];
            $id = Input::get('id');

            $Glossaries = $GlossarRepository->findAll();

            $arrGlossar = [];
            if (!empty($Glossaries)) {
                foreach ($Glossaries as $Glossar) {
                    if (empty($id) || $Glossar->getId() == $id) {
                        $arrGlossar[] = $Glossar->getId();
                        $JSON[$Glossar->getAlias()] = ['tl_glossar' => $Glossar->getData()];
                    }
                }
                // die('<pre>' . print_r($ExportType, true));
                if ($ExportType === 'all') {
                    $Terms = $TermRepository->findBy(['pid' => $arrGlossar]);
                } else {
                    $Terms = $TermRepository->findBy(['pid' => $arrGlossar, 'type' => $ExportType]);
                }

                if (!empty($Terms)) {
                    $arrTerms = [];
                    $term = null;
                    foreach ($Terms as $Term) {
                        $arrTerms[] = $Term->getId();
                        foreach ($JSON as $key => $glossar) {
                            if ($glossar['tl_glossar']['id'] == $Term->getPid()) {
                                $JSON[$key]['tl_glossar']['tl_sw_glossar'][] = $Term->getData();
                            }
                        }
                    }

                    if ($ExportType === 'default') {
                        $ExportType = 'glossar';
                    }
                    $Content = GlossarContentModel::findByPidsAndTable($arrTerms, 'tl_sw_glossar', $ExportType);

                    if (!empty($Content)) {
                        while ($Content->next()) {
                            foreach ($JSON as $key => $glossar) {
                                foreach ($glossar['tl_glossar']['tl_sw_glossar'] as $term => $tdata) {
                                    if ($tdata['id'] == $Content->pid) {
                                        $JSON[$key]['tl_glossar']['tl_sw_glossar'][$term]['tl_content'][] = $Content->row();
                                    }
                                }
                            }
                        }
                    }
                }
            }

            $JSON = $this->encode_array($this->cleanArray($JSON));

            $title = $Glossar->getAlias();
            if (!$id) {
                $title = 'complete';
            }

            header("Content-type: application/download");
            header('Content-Disposition: attachment; filename="glossar_' . $title . '.json"');
            echo json_encode($JSON);
            die();
        }

        return $objGlossar->parse();
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

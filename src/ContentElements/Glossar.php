<?php

/**
 * Contao Open Source CMS
 */

declare(strict_types=1);

namespace Sioweb\Glossar\ContentElements;

use Contao;
use stdClass;
use Contao\Input;
use Contao\Config;
use Contao\Pagination;
use Contao\Controller;
use Contao\Environment;
use Contao\ContentElement;
use Contao\ContentHeadline;
use Contao\FrontendTemplate;
use Sioweb\License\Glossar as GlossarLicense;

use Sioweb\Glossar\Entity\Terms;
use Sioweb\Glossar\Models\StdModel;
use Sioweb\Glossar\Models\PageModel as GlossarPageModel;

/**
 * @file ContentGlossar.php
 * @class ContentGlossar
 * @author Sascha Weidner
 * @version 2.3
 * @package sioweb.contao.extensions.glossar
 * @copyright Sascha Weidner, Sioweb
 */

class Glossar extends ContentElement
{

    /**
     * Template
     * @var string
     */
    protected $strTemplate = 'ce_glossar';

    private $license = false;

    /**
     * Return if there are no files
     * @return string
     */
    public function generate()
    {
        $this->license = $this->getContainer()->get('sioweb.glossar.license')->valid();

        if (!isset($_GET['items']) && Config::get('useAutoItem') && isset($_GET['auto_item'])) {
            Input::setGet('items', Input::get('auto_item'));
        }

        if (!isset($_GET['alpha']) && Config::get('useAutoItem') && isset($_GET['auto_item'])) {
            Input::setGet('alpha', Input::get('auto_item'));
        }

        if (Input::get('items') !== null && $this->type !== 'glossar_reader' && $this->differentGlossarDetailPage && $this->jumpToGlossarTerm) {
            $url = null;

            $EntityManager = $this->getContainer()->get('doctrine.orm.default_entity_manager');
            $TermsRepository = $EntityManager->getRepository(Terms::class);
            $TermObj = $TermsRepository->findOneByAlias(Input::get('items'));

            if ($this->glossar == $TermObj->getPid()->getId()) {
                $objParent = GlossarPageModel::findWithDetails($this->jumpToGlossarTerm);
                $domain = ($objParent->rootUseSSL ? 'https://' : 'http://') . ($objParent->domain ?: Environment::get('host')) . TL_PATH . '/';

                $link = GlossarPageModel::findByPk($this->jumpToGlossarTerm);
                if ($link !== null) {
                    $url = $domain . Controller::generateFrontendUrl($link->row(), ((Config::get('useAutoItem') && !Config::get('disableAlias')) ? '/' : '/items/') . Input::get('items'));
                }

                static::redirect($url, 301);
            }
        }

        if ($this->type === 'glossar_reader' && Input::get('items') == null) {
            return '';
        }

        return parent::generate();
    }

    public function compile()
    {
        global $objPage;
        $this->loadLanguageFile('default');
        $this->loadLanguageFile('glossar_errors');
        $glossarErrors = [];

        if (empty($this->headlineUnit)) {
            $this->headlineUnit = 'h2';
        }

        if (!$this->sortGlossarBy) {
            $this->sortGlossarBy = 'alias';
        }

        $EntityManager = $this->getContainer()->get('doctrine.orm.default_entity_manager');
        $TermsRepository = $EntityManager->getRepository(Terms::class);

        $_sortGlossarBy = explode('_', $this->sortGlossarBy);
        $sortGlossarBy = [];
        $sortGlossarBy[$_sortGlossarBy[0]] = !empty($_sortGlossarBy[1]) ? $_sortGlossarBy[1] : 'ASC';

        $Options = ['order' => $sortGlossarBy];
        if (Input::get('page') && $this->perPage) {
            $Options['limit'] = $this->perPage;
            $Options['offset'] = $this->perPage * (Input::get('page') - 1);
        } elseif ($this->perPage) {
            $Options['limit'] = $this->perPage;
            $Options['offset'] = 0;
        }

        if (Input::get('items') == '') {
            if (empty($this->glossar)) {
                $TermObj = $TermsRepository->findAll($Options['order'], $Options['limit'], $Options['offset']);
            } else {
                $TermObj = $TermsRepository->findBy(['pid' => $this->glossar], $Options['order'], $Options['limit'], $Options['offset']);
            }
        } else {
            $TermObj = $TermsRepository->findByAlias(Input::get('items'), [], $Options['limit'], $Options['offset']);
        }

        /* Gefundene Begriffe durch Links zum Glossar ersetzen */
        $arrGlossar = [];
        $filledLetters = [];
        if ($TermObj) {

            if (Input::get('items') == '') {
                $arrGlossarIDs = [];

                foreach ($TermObj as $Term) {
                    $arrGlossarIDs[] = $Term->getId();
                }

                if (in_array('tags', Config::getInstance()->getActiveModules())) {
                    $arrTags = $this->Database->prepare("SELECT * FROM tl_tag WHERE from_table = ? AND tid IN ('" . implode("','", $arrGlossarIDs) . "') ORDER BY tag ASC");
                }
            } elseif (in_array('tags', Config::getInstance()->getActiveModules())) {
                $arrTags = $this->Database->prepare("SELECT * FROM tl_tag WHERE from_table = ? AND tid = " . intval($TermObj['id']) . " ORDER BY tag ASC");
            }

            if (!empty($arrTags)) {
                $arrTags = $arrTags->execute('tl_sw_glossar')->fetchAllAssoc();

                $_arrTags = [];
                foreach ($arrTags as $key => $value) {
                    $_arrTags[$value['tid']][] = $value;
                }

                $arrTags = $_arrTags;
                unset($_arrTags);
            }

            $delimittedGlossarTerms = [];

            foreach ($TermObj as $Term) {
                $initial = substr(str_replace('id-', '', $Term->getAlias()), 0, 1);
                $filledLetters[] = $initial;
                $delimittedGlossarTerms[strtoupper($initial)] = [];

                if (Input::get('items') != '' || (!$this->showAfterChoose || !$this->addAlphaPagination) || ($this->addAlphaPagination && $this->showAfterChoose && Input::get('pag') != '')) {
                    if (Input::get('pag') == '' || $initial == Input::get('pag')) {

                        $newGlossarObj = new FrontendTemplate('glossar_default');
                        $newGlossarObj->setData($Term->getData());
                        $newGlossarObj->glossar = $GLOBALS['TL_LANG']['glossar'];

                        if (!empty($arrTags) && !empty($arrTags[$newGlossarObj->id])) {
                            $newGlossarObj->showTags = $this->glossarShowTags;
                            $newGlossarObj->tags = $arrTags[$newGlossarObj->id];
                        }

                        $link = null;
                        if ($this->differentGlossarDetailPage && $this->jumpToGlossarTerm) {
                            $link = \PageModel::findByPk($this->jumpToGlossarTerm);
                            // $newGlossarObj->content = 1;
                            if ($link) {
                                $newGlossarObj->link = $link->getAbsoluteUrl('/' . $newGlossarObj->alias);
                            }
                        } else {
                            $Content = \ContentModel::findPublishedByPidAndTable($newGlossarObj->id, 'tl_sw_glossar');
                            if (!empty($Content) || (!empty($Term->getTeaser()) && Config::get('acceptTeasersAsContent'))) {
                                if (!$newGlossarObj->jumpTo || $newGlossarObj->source != 'page') {
                                    $newGlossarObj->jumpTo = Config::get('jumpToGlossar');
                                }

                                if ($newGlossarObj->jumpTo) {
                                    $link = \PageModel::findByPk($newGlossarObj->jumpTo);
                                }
                                $newGlossarObj->content = 1;
                                if ($link) {
                                    $newGlossarObj->link = $link->getAbsoluteUrl('/' . $newGlossarObj->alias);
                                }
                            } else {
                                $newGlossarObj->link = false;
                            }
                        }

                        if (Input::get('items') == '') {
                            $arrGlossar[$newGlossarObj->title] = $newGlossarObj->parse();
                        } else {
                            if (!empty($arrTags) && !empty($arrTags[$Term->getId()])) {
                                $this->Template->showTags = $this->glossarShowTags && $this->glossarShowTagsDetails;
                                $this->Template->tags = $arrTags[$Term->getId()];
                            }

                            $this->addCommentsToTerm($Term);
                            $elements = $this->getGlossarElements($newGlossarObj->id);

                            if (empty($elements) && $Term->getDescription()) {
                                $descriptionObj = new FrontendTemplate('glossar_description');
                                $descriptionObj->content = $Term->getDescription();
                                $elements = [$descriptionObj->parse()];
                            }
                            if (empty($elements)) {
                                $elements = [$Term->getTeaser()];
                            }
                            $arrGlossar[$newGlossarObj->title] = $elements;
                        }
                    }
                }
            }
        }

        if (Input::get('items') != '') {
            $this->useInitialAsDelimitter = false;
        }

        if ($this->useInitialAsDelimitter) {
            foreach ($arrGlossar as $key => $term) {
                $delimittedGlossarTerms[strtoupper($key[0])][] = $term;
            }

            $arrGlossar = $delimittedGlossarTerms;
            unset($delimittedGlossarTerms);
        }

        $this->Template->pagination = '';
        if (!empty($this->perPage)) {
            $objPagination = new Pagination(count($TermsRepository->findAll()), $this->perPage);
            $this->Template->pagination = $objPagination->generate("\n  ");
            $this->Template->perPage = $this->perPage;
        }

        $numbers = $letters = [];

        if ($this->addAlphaPagination) {
            for ($c = 65; $c <= 90; $c++) {
                if (($this->addOnlyTrueLinks && in_array(strtolower(chr($c)), $filledLetters)) || !$this->addOnlyTrueLinks) {
                    $letters[] = [
                        'href' => $this->addToUrl('pag=' . strtolower(chr($c)) . '&amp;alpha=&amp;items=&amp;auto_item='),
                        'initial' => chr($c),
                        'active' => (Input::get('pag') == strtolower(chr($c))),
                        'trueLink' => (in_array(strtolower(chr($c)), $filledLetters)),
                        'onlyTrueLinks' => $this->addOnlyTrueLinks,
                    ];
                }
            }

            if ($this->addNumericPagination) {
                for ($n = 0; $n < 10; $n++) {
                    if (($this->addOnlyTrueLinks && in_array(strtolower((string)$n), $filledLetters)) || !$this->addOnlyTrueLinks) {
                        $numbers[] = [
                            'href' => $this->addToUrl('pag=' . strtolower((string)$n) . '&amp;alpha=&amp;items=&amp;auto_item='),
                            'initial' => $n,
                            'active' => (Input::get('pag') == strtolower((string)$n)),
                            'trueLink' => (in_array(strtolower((string)$n), $filledLetters)),
                            'onlyTrueLinks' => $this->addOnlyTrueLinks,
                        ];
                    }
                }
            }
        }

        if (!empty($letters)) {
            $letters[0]['class'] = 'first';
            $letters[count($letters) - 1]['class'] = 'last';
        }

        if (!empty($numbers)) {
            $numbers[0]['class'] = 'first';
            $numbers[count($numbers) - 1]['class'] = 'last';
        }

        $objPagination = new FrontendTemplate('glossar_pagination');

        if ($objPage) {
            $objPagination->showAllHref = $this->generateFrontendUrl($objPage->row());
            $objPagination->showAllLabel = $GLOBALS['TL_LANG']['glossar']['showAllLabel'];
            $objPagination->alphaPagination = $letters;
            $objPagination->numericPagination = $numbers;
            $strAlphaPagination = $objPagination->parse();
        }

        $this->Template->alphaPagination = $strAlphaPagination;

        if (Input::get('items') != '' || (!$this->showAfterChoose || !$this->addAlphaPagination) || ($this->addAlphaPagination && $this->showAfterChoose && Input::get('pag') != '')) {
            if (!$arrGlossar && $GLOBALS['glossar']['errors']['no_content']) {
                $glossarErrors[] = $GLOBALS['glossar']['errors']['no_content'];
            }
        }

        $termAsHeadline = false;
        if (Input::get('items') != '') {
            if (($this->termAsHeadline || Config::get('termAsHeadline')) && !$Term->getTermAsHeadline()) {
                $Headline = new StdModel();
                $Headline->headline = serialize(['value' => $Term->getTitle(), 'unit' => $this->headlineUnit]);
                // $Headline->cssID = serialize(['','glossar_headline']);
                $Headline->type = 'glossar_headline';
                $objHeadline = new ContentHeadline($Headline);
                $termAsHeadline = $objHeadline->generate();
            }

            $this->Template->termAsHeadline = $termAsHeadline;
            $this->Template->content = 1;
            $arrGlossar = array_shift($arrGlossar);

            $Seo = null;
            $oGlossar = $Term->getPid();

            if ($oGlossar->getSeo() || $Term->getSeo()) {
                if ($oGlossar->getTermInTitleTag() || $Term->getTermInTitleTag()) {
                    $Title = $objPage->pageTitle;

                    if ($Term->getTermInTitleStrTag() === '' && $oGlossar->getTermInTitleStrTag() === '') {
                        $pageTitle = strip_tags(strip_insert_tags($Term->getTitle()));
                    } elseif ($Term->getTermInTitleStrTag()) {
                        $pageTitle = strip_tags($this->replaceInsertTags($Term->getTermInTitleStrTag()));
                    } elseif ($oGlossar->getTermInTitleStrTag()) {
                        $pageTitle = strip_tags($this->replaceInsertTags($oGlossar->getTermInTitleStrTag()));
                    }
                    $objPage->pageTitle = $pageTitle;
                }

                if ($Term->getTermDescriptionTag()) {
                    $objPage->description = $this->prepareMetaDescription($Term->getTermDescriptionTag());
                } elseif ($oGlossar->getTermDescriptionTag()) {
                    $objPage->description = $this->prepareMetaDescription($oGlossar->getTermDescriptionTag());
                }
            }
        }

        $this->Template->ppos = $this->paginationPosition;
        $this->Template->delimitByInital = $this->useInitialAsDelimitter;
        $this->Template->glossar = $arrGlossar;
        $this->Template->back = $GLOBALS['TL_LANG']['MSC']['goBack'];

        if ($glossarErrors) {
            $errorObj = new FrontendTemplate('glossar_error');
            $errorObj->msg = $glossarErrors;
            $this->Template->errors = $errorObj->parse();
        }
    }

    private function addCommentsToTerm($term)
    {
        $objGlossar = $term->getPid();
        $this->Template->allowComments = $objGlossar->getAllowComments();

        // Comments are not allowed
        if (!$objGlossar->getAllowComments()) {
            return;
        }

        // Adjust the comments headline level
        $intHl = min(intval(str_replace('h', '', $this->hl)), 5);
        $this->Template->hlc = 'h' . ($intHl + 1);

        $this->import('Comments');
        $arrNotifies = [];

        // Notify the system administrator
        if ($objGlossar->getNotify() != 'notify_author') {
            $arrNotifies[] = $GLOBALS['TL_ADMIN_EMAIL'];
        }

        // Notify the author
        // if ($objGlossar->getNotify() != 'notify_admin' && $objArticle) {
        //     /** @var \UserModel $objAuthor */
        //     if (($objAuthor = $objArticle->getRelated('author')) !== null && $objAuthor->email != '') {
        //         $arrNotifies[] = $objAuthor->email;
        //     }
        // }

        $objConfig = new \stdClass();
        $objConfig->perPage = $objGlossar->getPerPage();
        $objConfig->order = $objGlossar->getSortOrder();
        $objConfig->template = $this->com_template;
        $objConfig->requireLogin = $objGlossar->getRequireLogin();
        $objConfig->disableCaptcha = $objGlossar->getDisableCaptcha();
        $objConfig->bbcode = $objGlossar->getBbcode();
        $objConfig->moderate = $objGlossar->getModerate();

        $this->Comments->addCommentsToTemplate($this->Template, $objConfig, 'tl_sw_glossar', $term->getId(), $arrNotifies);
    }

    private function getGlossarElements($id)
    {
        $arrElements = [];
        $objCte = \ContentModel::findPublishedByPidAndTable($id, 'tl_sw_glossar');

        if ($objCte !== null) {
            $intCount = 0;
            $intLast = $objCte->count() - 1;

            while ($objCte->next()) {
                $arrCss = [];
                /** @var \ContentModel $objRow */
                $objRow = $objCte->current();

                // Add the "first" and "last" classes (see #2583)
                if ($intCount == 0 || $intCount == $intLast) {
                    if ($intCount == 0) {
                        $arrCss[] = 'first';
                    }

                    if ($intCount == $intLast) {
                        $arrCss[] = 'last';
                    }
                }

                $objRow->classes = $arrCss;
                $arrElements[] = $this->getContentElement($objRow, $this->strColumn);
                ++$intCount;
            }
        }
        return $arrElements;
    }
}

<?php

/**
 * Contao Open Source CMS
 */

declare(strict_types=1);

namespace Sioweb\Glossar\Services;

use Contao\ArticleModel;
use Contao\Backend;
use Contao\BackendTemplate;
use Contao\Config;
use Contao\Environment;
use Contao\Input;
use Contao\System;
use Contao\Controller;
use Contao\RequestToken;
use Contao\CoreBundle\Framework\ContaoFramework;
use Sioweb\Glossar\Models\PageModel as GlossarPageModel;
use Contao\CoreBundle\Security\Authentication\FrontendPreviewAuthenticator;

/**
 * @file Rebuild.php
 * @class Rebuild
 * @author Sascha Weidner
 * @package sioweb.contao.extensions.glossar
 * @copyright Sascha Weidner, Sioweb
 */

class Rebuild extends Backend implements \executable
{

    /**
     * @var ContaoFramework
     */
    private $framework;

    private $Database;

    private $authenticator;

    public function __construct(ContaoFramework $framework, FrontendPreviewAuthenticator $frontendAuthenticator = null)
    {
        $framework->initialize();
        $this->Database = System::importStatic('Database');
        $this->authenticator = $frontendAuthenticator;
        if ($frontendAuthenticator === null && VERSION >= 4.6) {
            $this->authenticator = System::getContainer()->get('contao.security.frontend_preview_authenticator');
        }
    }

    /**
     * Return true if the module is active
     * @return boolean
     */
    public function isActive()
    {
        return (Config::get('enableGlossar') && Input::get('act') == 'glossar');
    }

    /**
     * Generate the module
     * @return string
     */
    public function run()
    {
        if (!Config::get('enableGlossar')) {
            return '';
        }

        $time = time();
        $objTemplate = new BackendTemplate('be_rebuild_glossar');
        $objTemplate->action = ampersand(Environment::get('request'));
        $objTemplate->indexHeadline = $GLOBALS['TL_LANG']['tl_maintenance']['glossarIndex'];
        $objTemplate->isActive = $this->isActive();

        // Add the error message
        if ($_SESSION['REBUILD_INDEX_ERROR'] != '') {
            $objTemplate->indexMessage = $_SESSION['REBUILD_INDEX_ERROR'];
            $_SESSION['REBUILD_INDEX_ERROR'] = '';
        }

        $arrUser = ['' => '-'];

        // Get active front end users
        $objUser = $this->Database->execute("SELECT id, username FROM tl_member WHERE disable!=1 AND (start='' OR start<$time) AND (stop='' OR stop>$time) ORDER BY username");

        while ($objUser->next()) {
            if (!empty($objUser->username)) {
                $arrUser[$objUser->id] = $objUser->username . ' (' . $objUser->id . ')';
            }
        }

        // Rebuild the index
        if (Input::get('act') == 'glossar') {
            if (!isset($_GET['rt']) || !RequestToken::validate(Input::get('rt'))) {
                $this->Session->set('INVALID_TOKEN_URL', Environment::get('request'));
                $this->redirect('contao/confirm.php');
            }

            $arrPages['regular'] = $this->findGlossarPages();

            // HOOK: take additional pages
            $InactiveArchives = (array) deserialize(Config::get('glossar_archive'));
            if (!empty($InactiveArchives)) {
                $InactiveArchives = array_flip($InactiveArchives);
            }

            if (isset($GLOBALS['TL_HOOKS']['getGlossarPages']) && is_array($GLOBALS['TL_HOOKS']['getGlossarPages'])) {
                foreach ($GLOBALS['TL_HOOKS']['getGlossarPages'] as $type => $callback) {
                    if (in_array($type, $InactiveArchives)) {
                        continue;
                    }

                    $this->{$callback[0]} = System::importStatic($callback[0]);
                    $cb_return = $this->{$callback[0]}->{$callback[1]}($arrPages);

                    if (!empty($cb_return) && !empty($InactiveArchives) && is_array($cb_return)) {
                        $cb_return = array_diff_key($cb_return, $InactiveArchives);
                    }

                    if (!empty($cb_return)) {
                        $arrPages[$type] = $cb_return;
                    }
                }
            }

            if ($this->authenticator === null) { // Contao 4.4
                // Calculate the hash
                $strHash = System::getSessionHash('FE_USER_AUTH');
                System::setCookie('FE_PREVIEW', 0, ($time - 86400), null, null, Environment::get('ssl'), true);

                // Remove old sessions
                $this->Database->prepare("DELETE FROM tl_session WHERE tstamp<? OR hash=?")
                    ->execute(($time - \Config::get('sessionTimeout')), $strHash);
            }

            $strUser = Input::get('user');

            // Log in the front end user
            if (is_numeric($strUser) && $strUser > 0 && isset($arrUser[$strUser])) {
                if ($this->authenticator === null) { // Contao 4.4
                    // Insert a new session
                    $this->Database->prepare("INSERT INTO tl_session (pid, tstamp, name, sessionID, ip, hash) VALUES (?, ?, ?, ?, ?, ?)")
                        ->execute($strUser, $time, 'FE_USER_AUTH', System::getContainer()->get('session')->getId(), Environment::get('ip'), $strHash);

                    // Set the cookie
                    System::setCookie('FE_USER_AUTH', $strHash, ($time + Config::get('sessionTimeout')), null, null, Environment::get('ssl'), true);
                } else { // Contao 4.5+
                    $objUser = $this->Database->prepare("SELECT username FROM tl_member WHERE id=?")
                        ->execute($strUser);

                    if (!$objUser->numRows || !$this->authenticator->authenticateFrontendUser($objUser->username, false)) {
                        $this->authenticator->removeFrontendAuthentication();
                    }
                }
            } else {
                // Log out the front end user
                if ($this->authenticator === null) { // Contao 4.4
                    System::setCookie('FE_USER_AUTH', $strHash, ($time - 86400), null, null, Environment::get('ssl'), true);
                    System::setCookie('FE_AUTO_LOGIN', Input::cookie('FE_AUTO_LOGIN'), ($time - 86400), null, null, Environment::get('ssl'), true);
                } else { // Contao 4.5+
                    $this->authenticator->removeFrontendAuthentication();
                }
            }

            $strBuffer = '';
            $rand = rand();
            $time = time();

            foreach ($arrPages as $type => $pages) {
                foreach ($pages as $lang => $arrPage) {
                    for ($i = 0, $c = count($arrPage); $i < $c; $i++) {
                        $strBuffer .= '<span class="get_' . $type . '_url" data-time="' . $time . '" data-type="' . $type . '" data-language="' . $lang . '" data-url="' . $arrPage[$i] . '#' . $rand . $i . '">' . \StringUtil::substr($arrPage[$i], 100) . '</span><br>';
                        unset($arrPages[$type][$lang][$i]);
                    }
                }
            }

            $objTemplate->content = $strBuffer;
            $objTemplate->note = $GLOBALS['TL_LANG']['tl_maintenance']['glossarNote'];
            $objTemplate->loading = $GLOBALS['TL_LANG']['tl_maintenance']['glossarLoading'];
            $objTemplate->complete = $GLOBALS['TL_LANG']['tl_maintenance']['glossarComplete'];
            $objTemplate->indexContinue = $GLOBALS['TL_LANG']['MSC']['continue'];
            $objTemplate->theme = Backend::getTheme();
            $objTemplate->isRunning = true;

            return $objTemplate->parse();
        }

        // Default variables
        $objTemplate->user = $arrUser;
        $objTemplate->indexLabel = $GLOBALS['TL_LANG']['tl_maintenance']['frontendUser'][0];
        $objTemplate->indexHelp = (Config::get('showHelp') && strlen($GLOBALS['TL_LANG']['tl_maintenance']['rebuildGlossarHelp'][1])) ? $GLOBALS['TL_LANG']['tl_maintenance']['rebuildHelp'][1] : '';
        $objTemplate->indexSubmit = $GLOBALS['TL_LANG']['tl_maintenance']['glossarSubmit'];

        return $objTemplate->parse();
    }

    public function clearGlossar()
    {
        /** @todo Clear all Glossar caches */
    }

    private function getRootPage($id)
    {
        $Page = \PageModel::findByPk($id);
        if ($Page->type !== 'root') {
            $Page = $this->getRootPage($Page->pid);
        }

        return $Page;
    }

    protected function findGlossarPages()
    {
        $time = time();
        $arrPages = [];
        $objPages = GlossarPageModel::findActiveAndEnabledGlossarPages();
        $domain = rtrim(Environment::get('base'), '/') . '/';

        if (!empty($objPages)) {
            while ($objPages->next()) {
                if ($objPages->type === 'root') {
                    continue;
                }

                if ($objPages->pid) {
                    $RootPage = $this->getRootPage($objPages->pid);
                }

                if ($RootPage->dns) {
                    $domain = rtrim('http' . ($RootPage->useSSL ? 's' : '') . '://' . str_replace(['http://', 'https://'], '', $RootPage->dns), '/') . '/';
                } else {
                    $domain = rtrim(Environment::get('base'), '/') . '/';
                }

                $strLanguage = $RootPage->language;

                if ((!$objPages->start || $objPages->start < $time) && (!$objPages->stop || $objPages->stop > $time)) {
                    $arrPages[$strLanguage][] = $domain . Controller::generateFrontendUrl($objPages->row(), null, $strLanguage);

                    $objArticle = ArticleModel::findBy(["tl_article.pid=? AND (tl_article.start='' OR tl_article.start<$time) AND (tl_article.stop='' OR tl_article.stop>$time) AND tl_article.published=1 AND tl_article.showTeaser=1"], [$objPages->id], ['order' => 'sorting']);

                    if (!empty($objArticle)) {
                        while ($objArticle->next()) {
                            $arrPages[$strLanguage][] = $domain . Controller::generateFrontendUrl($objPages->row(), '/articles/' . (($objArticle->alias != '' && !Config::get('disableAlias')) ? $objArticle->alias : $objArticle->id), $strLanguage);
                        }
                    }
                }
            }
        }

        return $arrPages;
    }
}

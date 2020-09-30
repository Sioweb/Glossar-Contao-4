<?php

/**
 * Contao Open Source CMS
 */

declare(strict_types=1);

namespace Sioweb\Glossar\Dca;

use Contao\CoreBundle\Exception\AccessDeniedException;
use Contao\DataContainer;
use Contao\Exception;
use Contao\Image;
use Contao\Input;
use Contao\StringUtil;
use Contao\System;
use Contao\Backend;
use Contao\CoreBundle\Framework\ContaoFramework;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class Glossar
{

    /**
     * @var ContaoFramework
     */
    private $framework;

    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    /**
     * @var SessionInterface
     */
    private $session;

    private $entityManager;

    private $User;

    private $Database = null;

    public function __construct(ContaoFramework $framework, TokenStorageInterface $tokenStorage, SessionInterface $session, $entityManager)
    {
        $this->framework = $framework;
        $this->tokenStorage = $tokenStorage;
        $this->session = $session;
        $this->entityManager = $entityManager;

        $token = $this->tokenStorage->getToken();
        $this->User = $token->getUser();
        $this->Database = System::importStatic('Database');
    }

    /**
     * Check permissions to edit table tl_glossar
     *
     * @throws AccessDeniedException
     */
    public function checkPermission()
    {
        $bundles = System::getContainer()->getParameter('kernel.bundles');

        // HOOK: comments extension required
        if (!isset($bundles['ContaoCommentsBundle'])) {
            unset($GLOBALS['TL_DCA']['tl_glossar']['fields']['allowComments']);
        }

        if ($this->User->isAdmin) {
            return;
        }

        // Set root IDs
        if (empty($this->User->news) || !\is_array($this->User->news)) {
            $root = [0];
        } else {
            $root = $this->User->news;
        }

        $GLOBALS['TL_DCA']['tl_glossar']['list']['sorting']['root'] = $root;

        // Check permissions to add archives
        if (!$this->User->hasAccess('create', 'glossarp')) {
            $GLOBALS['TL_DCA']['tl_glossar']['config']['closed'] = true;
        }

        /** @var Symfony\Component\HttpFoundation\Session\SessionInterface $objSession */
        $objSession = $this->session;

        // Check current action
        switch (Input::get('act')) {
            case 'create':
            case 'select':
                // Allow
                break;

            case 'edit':
                // Dynamically add the record to the user profile
                if (!\in_array(Input::get('id'), $root)) {
                    /** @var Symfony\Component\HttpFoundation\Session\Attribute\AttributeBagInterface $objSessionBag */
                    $objSessionBag = $objSession->getBag('contao_backend');

                    $arrNew = $objSessionBag->get('new_records');

                    if (\is_array($arrNew['tl_glossar']) && \in_array(Input::get('id'), $arrNew['tl_glossar'])) {
                        // Add the permissions on group level
                        if ($this->User->inherit != 'custom') {
                            $objGroup = $this->Database->execute("SELECT id, glossar, glossarp FROM tl_user_group WHERE id IN(" . implode(',', array_map('intval', $this->User->groups)) . ")");

                            while ($objGroup->next()) {
                                $arrGlossarp = StringUtil::deserialize($objGroup->glossarp);

                                if (\is_array($arrGlossarp) && \in_array('create', $arrGlossarp)) {
                                    $arrGlossar = StringUtil::deserialize($objGroup->glossar, true);
                                    $arrGlossar[] = Input::get('id');

                                    $this->Database->prepare("UPDATE tl_user_group SET glossar=? WHERE id=?")
                                        ->execute(serialize($arrGlossar), $objGroup->id);
                                }
                            }
                        }

                        // Add the permissions on user level
                        if ($this->User->inherit != 'group') {
                            $objUser = $this->Database->prepare("SELECT glossar, glossarp FROM tl_user WHERE id=?")
                                ->limit(1)
                                ->execute($this->User->id);

                            $arrGlossarp = StringUtil::deserialize($objUser->glossarp);

                            if (\is_array($arrGlossarp) && \in_array('create', $arrGlossarp)) {
                                $arrGlossar = StringUtil::deserialize($objUser->glossar, true);
                                $arrGlossar[] = Input::get('id');

                                $this->Database->prepare("UPDATE tl_user SET glossar=? WHERE id=?")
                                    ->execute(serialize($arrGlossar), $this->User->id);
                            }
                        }

                        // Add the new element to the user object
                        $root[] = Input::get('id');
                        $this->User->glossar = $root;
                    }
                }
                // No break;

            case 'copy':
            case 'delete':
            case 'show':
                if (!\in_array(Input::get('id'), $root) || (Input::get('act') == 'delete' && !$this->User->hasAccess('delete', 'glossarp'))) {
                    throw new AccessDeniedException('Not enough permissions to ' . Input::get('act') . ' glossar ID ' . Input::get('id') . '.');
                }
                break;

            case 'editAll':
            case 'deleteAll':
            case 'overrideAll':
                $session = $objSession->all();
                if (Input::get('act') == 'deleteAll' && !$this->User->hasAccess('delete', 'glossarp')) {
                    $session['CURRENT']['IDS'] = [];
                } else {
                    $session['CURRENT']['IDS'] = array_intersect((array) $session['CURRENT']['IDS'], $root);
                }
                $objSession->replace($session);
                break;

            default:
                if (\strlen(Input::get('act'))) {
                    throw new AccessDeniedException('Not enough permissions to ' . Input::get('act') . ' glossars.');
                }
                break;
        }
    }

    /**
     * Auto-generate an article alias if it has not been set yet
     * @param mixed
     * @param DataContainer
     * @return string
     * @throws Exception
     */
    public function generateAlias($varValue, DataContainer $dc)
    {
        $autoAlias = false;

        // Generate an alias if there is none
        if ($varValue == '') {
            $autoAlias = true;
            $varValue = standardize(StringUtil::restoreBasicEntities($dc->activeRecord->title));
        }

        $objAlias = $this->Database->prepare("SELECT id FROM tl_glossar WHERE id=? OR alias=?")
            ->execute($dc->id, $varValue);

        // Check whether the page alias exists
        if ($objAlias->numRows > 1) {
            if (!$autoAlias) {
                throw new \Exception(sprintf($GLOBALS['TL_LANG']['ERR']['aliasExists'], $varValue));
            }

            $varValue .= '-' . $dc->id;
        }
        return $varValue;
    }

    /**
     * Make sure there is only one fallback per domain (thanks to Andreas Schempp)
     *
     * @param mixed         $varValue
     * @param DataContainer $dc
     *
     * @return mixed
     *
     * @throws Exception
     */
    public function checkFallback($varValue, DataContainer $dc)
    {
        if ($varValue == '') {
            return '';
        }

        $objPage = $this->Database->prepare("SELECT * FROM tl_glossar WHERE fallback=1 AND id!=?")
            ->execute($dc->activeRecord->id);

        if ($objPage->numRows) {
            throw new \Exception($GLOBALS['TL_LANG']['ERR']['multipleGlossarFallback']);
        }

        return $varValue;
    }

    /**
     * Return the copy page button
     *
     * @param array  $row
     * @param string $href
     * @param string $label
     * @param string $title
     * @param string $icon
     * @param string $attributes
     * @param string $table
     *
     * @return string
     */
    public function copyPage($row, $href, $label, $title, $icon, $attributes, $table)
    {
        if ($GLOBALS['TL_DCA'][$table]['config']['closed']) {
            return '';
        }

        return '<a href="' . Backend::addToUrl($href . '&amp;id=' . $row['id']) . '" title="' . specialchars($title) . '"' . $attributes . '>' . Image::getHtml($icon, $label) . '</a> ';
    }

    /**
     * Return the copy page with subpages button
     *
     * @param array  $row
     * @param string $href
     * @param string $label
     * @param string $title
     * @param string $icon
     * @param string $attributes
     * @param string $table
     *
     * @return string
     */
    public function copyPageWithSubpages($row, $href, $label, $title, $icon, $attributes, $table)
    {
        if ($GLOBALS['TL_DCA'][$table]['config']['closed']) {
            return '';
        }

        $objSubpages = $this->Database->prepare("SELECT * FROM tl_page WHERE pid=?")
            ->limit(1)
            ->execute($row['id']);

        return '<a href="' . Backend::addToUrl($href . '&amp;id=' . $row['id']) . '" title="' . specialchars($title) . '"' . $attributes . '>' . Image::getHtml($icon, $label) . '</a> ';
    }

    /**
     * Return the edit header button
     *
     * @param array  $row
     * @param string $href
     * @param string $label
     * @param string $title
     * @param string $icon
     * @param string $attributes
     *
     * @return string
     */
    public function editHeader($row, $href, $label, $title, $icon, $attributes)
    {
        return $this->User->canEditFieldsOf('tl_glossar') ? '<a href="' . Backend::addToUrl($href . '&amp;id=' . $row['id']) . '" title="' . StringUtil::specialchars($title) . '"' . $attributes . '>' . Image::getHtml($icon, $label) . '</a> ' : Image::getHtml(preg_replace('/\.svg$/i', '_.svg', $icon)) . ' ';
    }

    /**
     * Return the copy archive button
     *
     * @param array  $row
     * @param string $href
     * @param string $label
     * @param string $title
     * @param string $icon
     * @param string $attributes
     *
     * @return string
     */
    public function copyArchive($row, $href, $label, $title, $icon, $attributes)
    {
        return $this->User->hasAccess('create', 'glossarp') ? '<a href="' . Backend::addToUrl($href . '&amp;id=' . $row['id']) . '" title="' . StringUtil::specialchars($title) . '"' . $attributes . '>' . Image::getHtml($icon, $label) . '</a> ' : Image::getHtml(preg_replace('/\.svg$/i', '_.svg', $icon)) . ' ';
    }

    /**
     * Return the delete archive button
     *
     * @param array  $row
     * @param string $href
     * @param string $label
     * @param string $title
     * @param string $icon
     * @param string $attributes
     *
     * @return string
     */
    public function deleteArchive($row, $href, $label, $title, $icon, $attributes)
    {
        return $this->User->hasAccess('delete', 'glossarp') ? '<a href="' . Backend::addToUrl($href . '&amp;id=' . $row['id']) . '" title="' . StringUtil::specialchars($title) . '"' . $attributes . '>' . Image::getHtml($icon, $label) . '</a> ' : Image::getHtml(preg_replace('/\.svg$/i', '_.svg', $icon)) . ' ';
    }
}

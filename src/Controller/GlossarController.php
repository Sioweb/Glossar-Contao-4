<?php

/**
 * Contao Open Source CMS
 */

declare (strict_types = 1);
namespace Sioweb\Glossar\Controller;

use Contao\Config;
use Contao\Controller as ContaoController;
use Contao\FrontendTemplate;
use Contao\Input;
use Contao\System;
use Sioweb\Glossar\Entity\Log as LogEntity;
use Sioweb\Glossar\Entity\Terms as TermsEntity;
use Sioweb\Glossar\Models\ContentModel as GlossarContentModel;
use Sioweb\Glossar\Models\PageModel as GlossarPageModel;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;

class GlossarController extends Controller
{

    public function indexAction()
    {
        return new JsonResponse([
            'success' => 0,
            'error' => 'Invalid route!',
        ]);
    }

    public function getTermAction($termId)
    {
        $this->get('contao.framework')->initialize();

        $TermRepository = $this->getDoctrine()->getRepository(TermsEntity::class);

        System::loadLanguageFile('default');

        $Term = $TermRepository->findOneById($termId);

        if ($Term === null && Input::get('cloud') == '') {
            return false;
        }

        if (Input::get('cloud') != '') {
            return false;
        }

        $Content = GlossarContentModel::findPublishedByPidAndTable($Term->getId(), 'tl_sw_glossar');

        $termObj = new FrontendTemplate('glossar_layer');
        $termObj->setData($Term->getData());
        $termObj->class = 'ce_glossar_layer';
        
        if ($termObj->addImage && $termObj->singleSRC != '')
		{
			$objModel = \FilesModel::findByUuid($termObj->singleSRC);
			if ($objModel !== null && is_file(\System::getContainer()->getParameter('kernel.project_dir') . '/' . $objModel->path))
			{
				// Do not override the field now that we have a model registry (see #6303)
				$arrArticle = $termObj->getData();
				// Override the default image size
				if ($this->imgSize != '')
				{
					$size = \StringUtil::deserialize($this->imgSize);
					if ($size[0] > 0 || $size[1] > 0 || is_numeric($size[2]))
					{
						$arrArticle['size'] = $this->imgSize;
					}
				}
				$arrArticle['singleSRC'] = $objModel->path;
				ContaoController::addImageToTemplate($termObj, $arrArticle, null, null, $objModel);
				// Link to the news article if no image link has been defined (see #30)
				if (!$termObj->fullsize && !$termObj->imageUrl)
				{
					// Unset the image title attribute
					$picture = $termObj->picture;
					unset($picture['title']);
					$termObj->picture = $picture;
					// Link to the news article
					$termObj->href = $termObj->link;
					$termObj->linkTitle = \StringUtil::specialchars(sprintf($GLOBALS['TL_LANG']['MSC']['readMore'], $objArticle->headline), true);
					// If the external link is opened in a new window, open the image link in a new window, too (see #210)
					if ($termObj->source == 'external' && $termObj->target && strpos($termObj->attributes, 'target="_blank"') === false)
					{
						$termObj->attributes .= ' target="_blank"';
					}
				}
			}
		}

        $objUrlGenerator = $this->get('contao.routing.url_generator');

        if (!empty($Content)) {
            if (Config::get('jumpToGlossar')) {
                $link = GlossarPageModel::findByPk(Config::get('jumpToGlossar'));
                $termObj->link = $link->getAbsoluteUrl('/' . $termObj->alias);
            }

            if ($termObj->jumpTo) {
                $link = GlossarPageModel::findByPk($termObj->jumpTo);
                $termObj->link = $link->getAbsoluteUrl('/' . $termObj->alias);
            }
        } elseif (!empty($termObj->teaser) && Config::get('acceptTeasersAsContent')) {
            if (!$termObj->jumpTo || $termObj->source != 'page') {
                $termObj->jumpTo = Config::get('jumpToGlossar');
            }

            if ($termObj->jumpTo) {
                $link = GlossarPageModel::findByPk($termObj->jumpTo);
            }
            $termObj->content = 1;
            if ($link) {
                $termObj->link = $link->getAbsoluteUrl('/' . $termObj->alias);
            }
        }

        echo json_encode(array('content' => ContaoController::replaceInsertTags($termObj->parse(), false)));
        die();

        return new JsonResponse([
            'success' => true,
        ]);
    }

    public function registrateLoadAction($termId)
    {
        $this->get('contao.framework')->initialize();

        $TermRepository = $this->getDoctrine()->getRepository(TermsEntity::class);

        System::loadLanguageFile('default');

        $Term = $TermRepository->findOneById($termId);

        if ($Term === null) {
            return false;
        }

        if ($this->get('sioweb.glossar.license')->valid()) {
            $Log = new LogEntity();
            $Log->setTstamp(time());
            $Log->setUser(session_id());
            $GAction = 'load';

            $Log->setAction($GAction);
            $Log->setPid($Term);
            $Log->setPage(Input::post('page'));
            $Log->setHost($_SERVER['SERVER_NAME']);
            $Log->setLanguage($_SESSION['TL_LANGUAGE'] ?? 'de');

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($Log);
            $entityManager->flush();
        }

        return new JsonResponse([
            'success' => true,
        ]);
    }

    public function registrateFolowAction($termId)
    {
        $this->get('contao.framework')->initialize();

        $TermRepository = $this->getDoctrine()->getRepository(TermsEntity::class);

        System::loadLanguageFile('default');

        $Term = $TermRepository->findOneById($termId);

        if ($Term === null) {
            return false;
        }

        if ($this->get('sioweb.glossar.license')->valid()) {
            $Log = new LogEntity();
            $Log->setTstamp(time());
            $Log->setUser(session_id());
            $GAction = 'follow';

            $Log->setAction($GAction);
            $Log->setPid($Term);
            $Log->setPage(Input::post('page'));
            $Log->setHost($_SERVER['SERVER_NAME']);
            $Log->setLanguage($_SESSION['TL_LANGUAGE'] ?? 'de');

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($Log);
            $entityManager->flush();
        }

        return new JsonResponse([
            'success' => true,
        ]);
    }

    public function registrateCloseAction($termId)
    {
        $this->get('contao.framework')->initialize();

        $TermRepository = $this->getDoctrine()->getRepository(TermsEntity::class);

        System::loadLanguageFile('default');

        $Term = $TermRepository->findOneById($termId);

        if ($Term === null) {
            return false;
        }

        if ($this->get('sioweb.glossar.license')->valid()) {
            $Log = new LogEntity();
            $Log->setTstamp(time());
            $Log->setUser(session_id());
            $GAction = 'close';

            $Log->setAction($GAction);
            $Log->setPid($Term);
            $Log->setPage(Input::post('page'));
            $Log->setHost($_SERVER['SERVER_NAME']);
            $Log->setLanguage($_SESSION['TL_LANGUAGE'] ?? 'de');

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($Log);
            $entityManager->flush();
        }

        return new JsonResponse([
            'success' => true,
        ]);
    }

}

<?php

namespace App\Controller;

use App\Form\GenerateDemoArticleFormType;
use App\Form\Model\DemoArticleModel;
use App\Service\PasteWords;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PublicController extends AbstractController
{
    /**
     * @Route("/", name="app_main")
     */
    public function index(): Response
    {
        return $this->render('layouts/index.html.twig');
    }

    /**
     * @Route("/try", name="app_try")
     */
    public function try(PasteWords $pasteWords, Request $request): Response
    {
        if ($disabled = $request->getSession()->has('demoArticle')) {
            $demoArticle = $request->getSession()->get('demoArticle');
        } else {
            $demoArticle = new DemoArticleModel();
        }

        $form = $this->createForm(GenerateDemoArticleFormType::class, $demoArticle, ['disabled' => $disabled]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var DemoArticleModel $demoArticle */

            foreach ($demoArticle->paragraphs as $key => $paragraph) {
                $demoArticle->paragraphs[$key] = $pasteWords->paste($paragraph, $demoArticle->articleWord, 1);
            }

            $request->getSession()->set('demoArticle', $demoArticle);

            return $this->redirectToRoute('app_try');
        }

        return $this->render('layouts/try.html.twig', [
            'generateDemoArticleForm' => $form->createView(),
            'disabled' => $disabled,
            'paragraphs' => $demoArticle->paragraphs
        ]);
    }
}

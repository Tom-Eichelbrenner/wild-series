<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/wild", name="wild_")
 *
 */
class WildController extends AbstractController
{
    /**
     * @Route("", name="index")
     */
    public function index(): Response
    {
        return $this->render('wild/index.html.twig',
            ['website' => 'Wild Séries']);
    }

    /**
     * @Route("/show/{slug}", defaults={"slug"= "Aucune série sélectionnée, veuillez choisir une série"}, name="show", requirements={"slug"="[a-z0-9-]+"})
     */
    public function show($slug){
        $slug = str_replace("-"," ",$slug);
        $slug = ucwords($slug);
        return $this->render("wild/show.html.twig", ['slug' => $slug ]);
    }
}
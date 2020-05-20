<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Program;
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
        $programs = $this->getDoctrine()
            ->getRepository(Program::class)
            ->findAll();

        if (!$programs) {
            throw $this->createNotFoundException('No program found in program\'s table.');
        }
        return $this->render('wild/index.html.twig',
            ['programs' => $programs]);
    }

    /**
     * @Route("/show/{slug}", defaults={"slug"= "Aucune série sélectionnée, veuillez choisir une série"}, name="show", requirements={"slug"="[a-z0-9-]+"})
     */
    public function show(?string $slug): Response
    {
        if (!$slug) {
            throw $this
                ->createNotFoundException('No slug has been sent to find a program in program\'s table.');
        }
        $slug = preg_replace(
            '/-/',
            ' ', ucwords(trim(strip_tags($slug)), "-")
        );
        $program = $this->getDoctrine()
            ->getRepository(Program::class)
            ->findOneBy(['title' => mb_strtolower($slug)]);
        if (!$program) {
            throw $this->createNotFoundException(
                'No program with ' . $slug . ' title, found in program\'s table.'
            );
        }

        return $this->render('wild/show.html.twig', [
            'program' => $program,
            'slug' => $slug,
        ]);
    }

    /**
     * @Route("/category/{categoryName}", name="show_category")
     */
    public function category(string $categoryName): Response
    {
        $categoryInfos = $this->getDoctrine()
            ->getRepository(Category::class)
            ->findOneByName($categoryName);

        $programs = $this->getDoctrine()
            ->getRepository(Program::class)
            ->findByCategory($categoryInfos);

        return $this->render('wild/category.html.twig', ['programs' => $programs, 'category' => $categoryInfos]);
    }
}
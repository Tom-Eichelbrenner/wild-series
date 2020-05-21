<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Episode;
use App\Entity\Program;
use App\Entity\Season;
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
        return $this->render('wild/index.html.twig', ['programs' => $programs]);
    }

    /**
     * @Route("/category/{categoryName}", name="show_category")
     */
    public function showByCategory(string $categoryName): Response
    {
        $category = $this->getDoctrine()
            ->getRepository(Category::class)
            ->findOneBy(['name' => $categoryName]);

        $programs = $this->getDoctrine()
            ->getRepository(Program::class)
            ->findByCategory($category);
        return $this->render('wild/category.html.twig', ['category' => $category, 'programs' => $programs]);
    }

    /**
     * @Route("/program/{slug}", defaults={"slug"= "Aucune série sélectionnée, veuillez choisir une série"}, name="show", requirements={"slug"="[a-z0-9-]+"})
     */
    public function showByProgram(?string $slug): Response
    {
        if (!$slug) {
            throw $this->createNotFoundException('No slug has been sent.');
        }
        $slug = preg_replace(
            '/-/',
            ' ', ucwords(trim(strip_tags($slug)), "-")
        );
        $program = $this->getDoctrine()
            ->getRepository(Program::class)
            ->findOneBy(['title' => mb_strtolower($slug)]);
        if (!$program) {
            throw $this->createNotFoundException('No program with ' . $slug . ' title found.');
        }
        $seasons = $program->getSeasons();

        return $this->render('/wild/show_program.html.twig', ['program' => $program, 'seasons' => $seasons, 'slug' => $slug]);
    }

    /**
     * @Route("/season/{id}", name="season", requirements={"id"="[0-9-]+"})
     */
    public function showBySeason(int $id): Response
    {
        $season = $this->getDoctrine()
            ->getRepository(Season::class)
            ->findOneBy(['id' => $id]);

        $program = $season->getProgram();
        $episodes = $season->getEpisodes();

        return $this->render('/wild/season.html.twig', ['season' => $season, 'program' => $program, 'episodes' => $episodes]);
    }

    /**
     * @Route("/episode/{id}", name="episode", requirements={"id"="[0-9-]+"})
     */
    public function showEpisode(int $id): Response
    {
        $episode = $this->getDoctrine()
            ->getRepository(Episode::class)
            ->findOneBy(['id' => $id]);

        $season = $episode->getSeason();
        $program = $season->getProgram();

        return $this->render('/wild/episode.html.twig', ['season' => $season, 'program' => $program, 'episode' => $episode]);
    }
}
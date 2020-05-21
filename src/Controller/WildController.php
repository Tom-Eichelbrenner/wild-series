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
        return $this->render('wild/index.html.twig',
            ['programs' => $programs]);
    }

    /**
     * @Route("/episode/{id}", name="episode")
     */
    public function showEpisode(int $id): Response
    {
       $episode = $this->getDoctrine()
           ->getRepository(Episode::class)
           ->findOneBy(['id' => $id]);
       $seasonID = $episode->getSeasonId();
       $season = $this->getDoctrine()
           ->getRepository(Season::class)
           ->findOneBy(['id' => $seasonID]);

       $programId = $season->getProgramId();
       $program = $this->getDoctrine()
           ->getRepository(Program::class)
           ->findOneBy(['id' => $programId]);
       dump($program);
       return $this->render('wild/episode.html.twig',['episode' => $episode, 'season' => $season, 'program' => $program]);
    }

    /**
     * @Route("/season/{id}", name="season")
     */
    public function showBySeason(int $id): Response
    {
        $season = $this->getDoctrine()
            ->getRepository(Season::class)
            ->findOneBy(['id' => $id]);

        $programid = $season->getProgramId();
        $program = $this->getDoctrine()
            ->getRepository(Program::class)
            ->findOneBy(['id' => $programid]);

        $seasonID = $season->getId();
        $episodes = $this->getDoctrine()
            ->getRepository(Episode::class)
            ->findBy(['season_id' => $seasonID]);


        return $this->render('wild/season.html.twig', ['program' => $program, 'season' => $season, 'episodes' => $episodes]);
    }

    /**
     * @Route("/show/{slug}", defaults={"slug"= "Aucune série sélectionnée, veuillez choisir une série"}, name="show", requirements={"slug"="[a-z0-9-]+"})
     */
    public function showByPrograms(string $slug): Response
    {
        if (!$slug) {
            throw $this
                ->createNotFoundException('No slug has been sent to find a program in program\'s table.');
        }
        $slug = preg_replace(
            '/-/',
            ' ', ucwords(trim(strip_tags($slug)), "-")
        );
        $slug = mb_strtolower($slug);
        $program = new Program();
        $program = $this->getDoctrine()
            ->getRepository(Program::class)
            ->findOneByTitle($slug);
        if (!$program) {
            throw $this->createNotFoundException(
                'No program with ' . $slug . ' title, found in program\'s table.'
            );
        }
        $seasons = $this->getDoctrine()
            ->getRepository(Season::class)
            ->findBy(['program_id' => $program]);
        dump($seasons);
        return $this->render('wild/show.html.twig', [
            'program' => $program,
            'slug' => $slug,
            'seasons' => $seasons
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
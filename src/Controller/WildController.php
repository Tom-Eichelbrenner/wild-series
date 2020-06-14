<?php

namespace App\Controller;

use App\Entity\Actor;
use App\Entity\Category;
use App\Entity\Comment;
use App\Entity\Episode;
use App\Entity\Program;
use App\Entity\Season;
use App\Entity\User;
use App\Form\EpisodeCommentType;
use App\Repository\CommentRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

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
        $slug = urldecode($slug);
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
    public function showEpisode(Episode $episode,Request $request): Response
    {
        $season = $episode->getSeason();
        $program = $season->getProgram();
        $user = $this->getUser();


        $comment = new Comment();
        $comment->setAuthor($user);
        $comment->setEpisode($episode);
        $form = $this->createForm(EpisodeCommentType::class, $comment);
        $form->handleRequest($request);



        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($comment);
            $entityManager->flush();
        }

        $comments = $this->getDoctrine()
            ->getRepository(Comment::class)
            ->findByEpisode($episode);
        return $this->render('/wild/episode.html.twig', ['comments' => $comments,'season' => $season, 'program' => $program, 'episode' => $episode, 'form' => $form->createView(), 'author' => $this->getUser()]);
    }

    /**
     * @Route("/actor/{id}", name="actor", requirements={"id"="[0-9-]+"})
     */
    public function showActor(Actor $actor): Response
    {
        $name = $actor->getName();
        $programs = $actor->getPrograms();

        return $this->render('/wild/actor.html.twig', ['name' => $name, 'programs' => $programs]);
    }

}
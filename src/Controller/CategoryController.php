<?php

namespace App\Controller;

use App\Form\CategoryAddType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
/**
 * @Route("/category", name="category_")
 *
 */
class CategoryController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index()
    {
        return $this->render('category/index.html.twig', [
            'controller_name' => 'CategoryController',
        ]);
    }
    /**
     * @Route("/add", name="add")
     * @IsGranted("ROLE_ADMIN")
     */
    public function add(Request $request): Response
    {
        $form = $this->createForm(CategoryAddType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $category = $form->getData();
            $em->persist($category);
            $em->flush();
        }

        return $this->render('category/add.html.twig', [
            'form' => $form->createView()
        ]);
    }
}

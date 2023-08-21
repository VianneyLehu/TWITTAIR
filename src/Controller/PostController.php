<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\PostRepository;
use App\Form\PostFormType;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Post;
use App\Entity\Tag;

class PostController extends AbstractController
{
    #[Route('/', name: 'app_post')]
    public function index(PostRepository $PR): Response
    {
        if (!$this->getUser()) {
            return new RedirectResponse($this->generateUrl('app_login')); // Rediriger l'utilisateur vers la page de connexion
        }

        $Post = $PR->findall();


        return $this->render('post/index.html.twig', [
            'controller_name' => 'PostController',
            'Post' => $Post,
        ]);
    }


    #[Route('/post/create', name: 'app_post_create')]

    public function createPost(Request $Request, EntityManagerInterface $em): Response
    {


        if (!$this->getUser()) {
            return new RedirectResponse($this->generateUrl('app_login')); // Rediriger l'utilisateur vers la page de connexion
        }


        $currentuser = $this->getUser();

        $Post = new Post();
        $form = $this->createForm(PostFormType::class, $Post);
        $form->handleRequest($Request);


        if($form->isSubmitted() && $form->isValid()){
            $Post->setParent($currentuser);
            $em->persist($Post);
            $em->flush();
            
            return $this->redirectToRoute("app_post", [] , Response::HTTP_SEE_OTHER);
        }

        return $this->render('post/create.html.twig', [
            'controller_name' => 'CreateController',
            'form' => $form->createView(), 
        ]);


    }

}

<?php

namespace App\Controller;


use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\CommentRepository;
use App\Repository\PostRepository;
use App\Form\CommentTypev2;
use App\Form\CommentType;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Comment;
use App\Entity\Post;
use App\Entity\Users;

class CommentController extends AbstractController
{


    #[Route('/comment', name: 'app_comment')]
    public function index(CommentRepository $CR): Response
    {


        if (!$this->getUser()) {
            return new RedirectResponse($this->generateUrl('app_login')); // Rediriger l'utilisateur vers la page de connexion
        }

        $Comment = $CR->findall();

        return $this->render('comment/index.html.twig', [
            'controller_name' => 'CommentController',
            'comment' => $Comment,
        ]);
    }


    #[Route('/comment/create', name: 'app_comment_create')]
    public function CreateComment(Request $Request, EntityManagerInterface $em): Response
    {

        if (!$this->getUser()) {
            return new RedirectResponse($this->generateUrl('app_login')); // Rediriger l'utilisateur vers la page de connexion
        }

        $currentuser = $this->getUser();

    

        $Comment = new Comment();
        $form = $this->createForm(CommentType::class, $Comment);
        $form->handleRequest($Request);


        if($form->isSubmitted() && $form->isValid()){
            $Comment->setUser($currentuser);
            $em->persist($Comment);
            $em->flush();
            
            return $this->redirectToRoute("app_comment", [] , Response::HTTP_SEE_OTHER);
        }


        return $this->render('comment/create.html.twig', [
            'controller_name' => 'CommentController',
            'form' => $form->createView(),
        ]);
    }
    
    #[Route('/comment/create/{id}', name: 'app_comment_create_v2')]

    public function CreateCommentv2(Request $Request, EntityManagerInterface $em,$id,PostRepository $PR, CommentRepository $CR): Response
    {

        if (!$this->getUser()) {
            return new RedirectResponse($this->generateUrl('app_login')); // Rediriger l'utilisateur vers la page de connexion
        }

        $currentuser = $this->getUser();

        $CommentTab = $CR->findBy(['post' => $id]);



        $Comment = new Comment();
        $form = $this->createForm(CommentTypev2::class, $Comment);
        $form->handleRequest($Request);

        if($form->isSubmitted() && $form->isValid()){
            $Post = $PR->findById($id);
            $Comment->setPost($Post[0]);
            $Comment->setUser($currentuser);

            $em->persist($Comment);
            $em->flush();
            return new RedirectResponse($this->generateUrl('app_comment_create_v2', ['id' => $id]));

        }


        return $this->render('comment/createv2.html.twig', [
            'controller_name' => 'CommentController',
            'form' => $form->createView(),
            'Comment' => $CommentTab,
        ]);
    }


}
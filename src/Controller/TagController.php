<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\TagRepository;
use App\Form\TagType;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Tag;
use Symfony\Component\HttpFoundation\RedirectResponse;


class TagController extends AbstractController
{
    #[Route('/tag', name: 'app_tag')]
    public function index(TagRepository $TG): Response
    {

        if (!$this->getUser()) {
            return new RedirectResponse($this->generateUrl('app_login')); // Rediriger l'utilisateur vers la page de connexion
        }

        $Tag = $TG->findall();


        return $this->render('tag/index.html.twig', [
            'controller_name' => 'TagController',
            'Tag' => $Tag,

        ]);
    }


    #[Route('/admin/tag', name: 'app_tag_create')]

    public function createTag(Request $Request, EntityManagerInterface $em): Response
    {

        if (!$this->getUser()) {
            return new RedirectResponse($this->generateUrl('app_login')); // Rediriger l'utilisateur vers la page de connexion
        }


        $Tag = new Tag();
        $form = $this->createForm(TagType::class, $Tag);
        $form->handleRequest($Request);


        if($form->isSubmitted() && $form->isValid()){
            $em->persist($Tag);
            $em->flush();
            
            return $this->redirectToRoute("app_tag", [] , Response::HTTP_SEE_OTHER);
        }

        return $this->render('tag/create.html.twig', [
            'controller_name' => 'CreateController',
            'form' => $form->createView(), 
        ]);


    }
}

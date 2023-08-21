<?php

namespace App\Controller;

use App\Entity\Users;
use Symfony\Component\HttpFoundation\RedirectResponse;
use App\Repository\UsersRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;



class UserController extends AbstractController
{
    #[Route('/user', name: 'app_user')]
    public function index(UsersRepository $UR): Response
    {

        if (!$this->getUser()) {
            return new RedirectResponse($this->generateUrl('app_login')); // Rediriger l'utilisateur vers la page de connexion
        }


        $User = $UR->findall();

        return $this->render('user/index.html.twig', [
            'controller_name' => 'UserController',
            'User' => $User,
        ]);
    }


    #[Route('/admin/user', name: 'app_admin_user')]
    public function admin_user(UsersRepository $UR): Response
    {

        if (!$this->getUser()) {
            return new RedirectResponse($this->generateUrl('app_login')); // Rediriger l'utilisateur vers la page de connexion
        }

        $User = $UR->findall();

        return $this->render('user/delete.html.twig', [
            'controller_name' => 'UserController',
            'User' => $User,
        ]);
    }

    #[Route('/admin/user/delete/{id}', name: 'app_admin_user_delete')]
    public function admin_user_delete(EntityManagerInterface $em, $id): Response
    {

        if (!$this->getUser()) {
            return new RedirectResponse($this->generateUrl('app_login')); // Rediriger l'utilisateur vers la page de connexion
        }

        $row = $em->getRepository(Users::class)->find($id);
        $em->remove($row);
        $em->flush();

        return $this->redirectToRoute('app_post');
    }
}

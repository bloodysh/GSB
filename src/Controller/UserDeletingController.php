<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserDeletingController extends AbstractController
{
    #[Route('/user/deleting', name: 'app_user_deleting')]
    public function index(UserRepository $userRepository, EntityManagerInterface $entityManager): Response
    {
        $alluser=$userRepository->findAll();
        foreach($alluser as $user){
            $theEmail= $user->getEmail();
            if (str_ends_with($theEmail, "@gsb.fr")){

                $user->eraseCredentials();
            }
            $entityManager->persist($user);
            $entityManager->flush();
        }

        return $this->render('user_deleting/index.html.twig', [
            'controller_name' => 'UserDeletingController',
        ]);
    }
}

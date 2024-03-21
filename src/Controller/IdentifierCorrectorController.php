<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class IdentifierCorrectorController extends AbstractController
{
    #[Route('/identifier/corrector', name: 'app_identifier_corrector')]
    public function index(Request $request, UserRepository $userRepository, EntityManagerInterface $entityManager): Response
    {
        $alluser=$userRepository->findAll();
        foreach($alluser as $user){
            $theEmail= $user->getEmail();
            $user->setEmail($theEmail.'@gsb.fr');

            $entityManager->persist($user);
            $entityManager->flush();
        }
        return $this->render('identifier_corrector/index.html.twig', [
            'controller_name' => 'IdentifierCorrectorController',
        ]);
    }
}

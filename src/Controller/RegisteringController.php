<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use function Symfony\Component\Clock\now;

class RegisteringController extends AbstractController
{
    #[Route('/registering', name: 'app_regisering')]
    public function index(EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher): Response
    {

        $user = new User();

        $plaintextPassword = "password";
        $user->setEmail('supadmin@supadmin.com');
        $user->setRoles(['ROLE_SUPER_ADMIN']);
        $user->setCp('74000');
        $user->setAdresse('30 place carnot');
        $user->setNom('SupAdmin');
        $user->setPrenom('SupAdmin');
        $user->setVille('Annecy');
        $user->setDateEmbauche(new \DateTime('now'));
        $hashedPassword = $passwordHasher->hashPassword(
            $user,
            $plaintextPassword
        );
        $user->setPassword($hashedPassword);


        $entityManager->persist($user);
        $entityManager->flush();
        return $this->render('registering/index.html.twig', [
            'controller_name' => 'RegisteringController',
        ]);
    }
}

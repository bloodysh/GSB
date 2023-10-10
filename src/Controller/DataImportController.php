<?php

namespace App\Controller;

use App\Entity\Etat;
use App\Entity\FicheFrais;
use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\Bundle\DoctrineBundle\DoctrineBundle;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\Date;

#[Route('/import')]
class DataImportController extends AbstractController
{
    #[Route('/user', name: 'app_data_import_user')]
    public function user(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager, UserRepository $leRepo): Response
    {
        //decode des visiteurs
        $usersjson = file_get_contents('./visiteur.json');
        $users = json_decode($usersjson, true);

        //parcour du tableau old-gsb user pour les mettre dans nouvelle GSB
        foreach ($users as $userData) {
            $user = new User();

            $user->setOldId($userData['id']);
            $user->setVille($userData['ville']);
            $user->setNom($userData['nom']);
            $user->setPrenom($userData['prenom']);
            $user->setEmail($userData['login']);

            // Hash the password using UserPasswordHasherInterface
            $hashedPassword = $userPasswordHasher->hashPassword(
                $user,
                $userData['mdp'] // Assuming 'mdp' is the password field in your JSON data
            );
            $user->setPassword($hashedPassword);

            $user->setAdresse($userData['adresse']);
            $user->setCp($userData['cp']);
            $dateEmbauche = new \DateTime($userData['dateEmbauche']);
            $user->setDateEmbauche($dateEmbauche);

        }

        $entityManager->persist($user);
        $entityManager->flush();

        return $this->render('data_import/index.html.twig', [
            'controller_name' => 'DataImportController',
        ]);

    }
    #[Route('/fiche', name: 'app_data_import_fiche')]
    public function fiche(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager): Response
    {
        // Decode des fiches frais
        $fichesjson = file_get_contents('./fichefrais.json');
        $fiches = json_decode($fichesjson, true);

        // Parcour du tableau old-gsb user pour les mettre dans nouvelle GSB
        foreach ($fiches as $ficheData) {
            $fiche = new FicheFrais();

            $user = $entityManager->getRepository(User::class)->findOneBy(['oldId' => $ficheData['idVisiteur']]);

            if ($user) {
                $fiche->setUser($user);

                switch ($ficheData['idEtat']) {
                    case "CL":
                        $etat = $entityManager->getRepository(Etat::class)->find(1);
                        break;
                    case "CR":
                        $etat = $entityManager->getRepository(Etat::class)->find(2);
                        break;
                    case "RB":
                        $etat = $entityManager->getRepository(Etat::class)->find(3);
                        break;
                    case "VA":
                        $etat = $entityManager->getRepository(Etat::class)->find(4);
                        break;
                }

                $fiche->setEtat($etat);
                $fiche->setMois($ficheData['mois']);
                $dateModif = new \DateTime($ficheData['dateModif']);
                $fiche->setDateModif($dateModif);
                $fiche->setMontantValid($ficheData['montantValide']);
                $fiche->setNbJustificatifs($ficheData['nbJustificatifs']);

                $entityManager->persist($fiche);
            }
        }

        $entityManager->flush();

        return $this->render('data_import/index.html.twig', [
            'controller_name' => 'DataImportController',
        ]);

    }
}

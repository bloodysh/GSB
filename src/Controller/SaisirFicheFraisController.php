<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\AfficheFicheFraisType;
use App\Form\FicheFraisType;
use App\Repository\EtatRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SaisirFicheFraisController extends AbstractController
{
    #[Route('/saisirfiche', name: 'app_saisir_fiche_frais')]
    public function index(Request $request, ManagerRegistry $doctrine, EtatRepository $etatRepository): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $user = $this->getUser();
        $myUser = $doctrine->getRepository(User::class)->findOneBy(['email' => $user->getUserIdentifier()]);
        $fichesfrais = $myUser->getFicheFrais();
        $form = $this->createForm(FicheFraisType::class, $fichesfrais);

        if ($fichesfrais->isEmpty())
        {
            $form->handleRequest($request);
        }

        return $this->render('saisir_fiche_frais/index.html.twig', [
            'controller_name' => 'SaisirFicheFraisController',
            'form' => $form->createView()
        ]);
    }
}

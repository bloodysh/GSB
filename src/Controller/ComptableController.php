<?php

namespace App\Controller;

use App\Entity\FicheFrais;
use App\Form\TousLesUtilisateursType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class ComptableController extends AbstractController
{
    #[Route('/comptable', name: 'app_comptable')]
    public function index(Request $request, EntityManagerInterface $entity): Response
    {
        $fichesFraisForm = $this->createForm(TousLesUtilisateursType::class);
        $fichesFraisForm->handleRequest($request);

        $fichesFrais = null;
        if ($fichesFraisForm->isSubmitted() && $fichesFraisForm->isValid()) {
            $selectedUser = $fichesFraisForm->get('user')->getData();
            $fichesFrais = $entity->getRepository(FicheFrais::class)->findBy(['user' => $selectedUser]);
        }

        return $this->render('comptable/index.html.twig', [
            'controller_name' => 'ComptableController',
            'fichesFraisForm' => $fichesFraisForm,
            'fichesFrais' => $fichesFrais,
        ]);
    }
}

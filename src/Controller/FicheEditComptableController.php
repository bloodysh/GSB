<?php

namespace App\Controller;

use App\Entity\Etat;
use App\Entity\FicheFrais;
use App\Entity\FraisForfait;
use App\Form\FicheFraisType;
use App\Repository\EtatRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\ChangeEtatType;

class FicheEditComptableController extends AbstractController
{
    #[Route('/fiche/edit/comptable', name: 'app_fiche_edit_comptable', methods: ['GET', 'POST'])]
    public function index(Request $request, EntityManagerInterface $entityManager, EtatRepository $etatRepository): Response
    {
        $id = $request->query->get('id');
        $ficheFrais = $entityManager->getRepository(FicheFrais::class)->find($id);
        $fraisForfait = $entityManager->getRepository(FraisForfait::class)->findAll();

        // ... rest of your code

        $form = $this->createForm(\App\Form\ChangeEtatType::class, null, ['allEtat' => $etatRepository->findAll()]);
        $form->get('Etat')->setData($ficheFrais->getEtat());
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $etat = $form->get('Etat')->getData();
            $ficheFrais->setEtat($etat);
            $entityManager->persist($ficheFrais);
            $entityManager->flush();
            return $this->redirectToRoute('app_fiche_edit_comptable', ['id' => $id]);
        }

        return $this->render('fiche_edit_comptable/index.html.twig', [
            'controller_name' => 'FicheEditComptableController',
            'ficheFrais' => $ficheFrais,
            'fraisForfait' => $fraisForfait,
            'form' => $form,
        ]);
    }
}
<?php

namespace App\Controller;

use App\Entity\LigneFraisHorsForfait;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SupprimFHFController extends AbstractController
{
    #[Route('/supprim/f/h/f', name: 'app_supprim_f_h_f')]
    public function index($id, EntityManagerInterface $entityManager): Response
    {
        $ficheFHF = $entityManager->getRepository(LigneFraisHorsForfait::class)->find($id);

        if ($ficheFHF != null){
            $entityManager->remove($ficheFHF);
            $entityManager->flush();
        }
        return $this->render('supprim_fhf/index.html.twig', [
            'controller_name' => 'SupprimFHFController',
        ]);
    }
}

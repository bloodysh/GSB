<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\AfficheFicheFraisType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class InflationController extends AbstractController
{
    #[Route('/inflation', name: 'app_inflation')]
    public function index(Request $request, ManagerRegistry $doctrine): Response
    {

        $myUsers = $doctrine->getRepository(User::class)->findAll();

        $i = 0;
        $montantValideTotal= 0;
        $montValideParVisiteur = 0;
        $prime = 0;
        foreach ($myUsers as $user)
        {
            $i +=1;
            $fiches = $user->getFicheFrais();
            foreach ($fiches as $fiche)
            {
                if (str_starts_with($fiche->getMois(), '2023'))
                $montantValideTotal += $fiche->getMontantValid();
                $montValideParVisiteur = $montantValideTotal/$i;

            }
            $prime = $montValideParVisiteur * (9.5 /100);
        }



        var_dump($montValideParVisiteur);
        var_dump($montantValideTotal);
        return $this->render('inflation/index.html.twig', [
            'controller_name' => 'InflationController',
            'montantValideTotal' => $montantValideTotal,
            'montValideParVisiteur' => $montValideParVisiteur,
            'prime' => $prime
        ]);
    }
}

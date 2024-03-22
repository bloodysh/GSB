<?php

namespace App\Controller;

use App\Entity\Etat;
use App\Entity\FicheFrais;
use App\Entity\LigneFraisForfait;
use App\Entity\LigneFraisHorsForfait;
use App\Entity\User;
use App\Form\LigneFraisForfaitType;
use App\Form\LigneFraisHorsForfaitType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SaisirFicheFraisController extends AbstractController
{
    #[Route('/saisirfiche', name: 'app_saisir_fiche_frais')]
    public function index(Request $request, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $user = $this->getUser();
        $ficheFrais = $entityManager->getRepository(FicheFrais::class)->findOneBy(['user' => $user]);

        $dateNow = date('m');


        if ($ficheFrais== null) {





            $ficheFrais = new FicheFrais();


            $ficheFrais->setMois($dateNow);
            $ficheFrais->setUser($user);
            $ficheFrais->setDateModif(new \DateTime('now'));
            $ficheFrais->setNbJustificatifs(0);
            $ficheFrais->setMontantValid(0);
            $ficheFrais->setEtat($entityManager->getRepository(Etat::class)->findOneBy(['id' => 1]));

            $fraisForfaitRepository = $entityManager->getRepository(LigneFraisForfait::class);
            $fraisEtape = $fraisForfaitRepository->find('1');
            $fraisKm = $fraisForfaitRepository->find('2');
            $fraisNuitee = $fraisForfaitRepository->find('3');
            $fraisRepas = $fraisForfaitRepository->find('4');

            $ligneFraisForfaitEtape = new LigneFraisForfait();
            $ligneFraisForfaitEtape->setFicheFrais($ficheFrais);
            $ligneFraisForfaitEtape->setFraisForfait($fraisEtape);
            $ligneFraisForfaitEtape->setQuantite(0);
            $entityManager->persist($ligneFraisForfaitEtape);

            $ligneFraisForfaitKm = new LigneFraisForfait();
            $ligneFraisForfaitKm->setFicheFrais($ficheFrais);
            $ligneFraisForfaitKm->setFraisForfait($fraisKm);
            $ligneFraisForfaitKm->setQuantite(0);
            $entityManager->persist($ligneFraisForfaitKm);

            $ligneFraisForfaitNuitee = new LigneFraisForfait();
            $ligneFraisForfaitNuitee->setFicheFrais($ficheFrais);
            $ligneFraisForfaitNuitee->setFraisForfait($fraisNuitee);
            $ligneFraisForfaitNuitee->setQuantite(0);
            $entityManager->persist($ligneFraisForfaitNuitee);

            $ligneFraisForfaitRepas = new LigneFraisForfait();
            $ligneFraisForfaitRepas->setFicheFrais($ficheFrais);
            $ligneFraisForfaitRepas->setFraisForfait($fraisRepas);
            $ligneFraisForfaitRepas->setQuantite(0);
            $entityManager->persist($ligneFraisForfaitRepas);

            $ficheFrais->addLigneFraisForfait($ligneFraisForfaitEtape);
            $ficheFrais->addLigneFraisForfait($ligneFraisForfaitKm);
            $ficheFrais->addLigneFraisForfait($ligneFraisForfaitNuitee);
            $ficheFrais->addLigneFraisForfait($ligneFraisForfaitRepas);

            $entityManager->persist($ficheFrais);

            foreach ($ficheFrais->getLigneFraisForfait() as $ligneFraisForfait) {
                if ($ligneFraisForfait->getFraisForfait()->getId() == 1) {
                    $ligneFraisForfaitEtape = $ligneFraisForfait;
                }
                if ($ligneFraisForfait->getFraisForfait()->getId() == 2) {
                    $ligneFraisForfaitKm = $ligneFraisForfait;
                }
                if ($ligneFraisForfait->getFraisForfait()->getId() == 3) {
                    $ligneFraisForfaitNuitee = $ligneFraisForfait;
                }
                if ($ligneFraisForfait->getFraisForfait()->getId() == 4) {
                    $ligneFraisForfaitRepas = $ligneFraisForfait;
                }
            }
            $formForfait = $this->createForm(LigneFraisForfaitType::class);


            // Handle form submissions
            $formForfait->handleRequest($request);
            if ($formForfait->isSubmitted() && $formForfait->isValid()) {
                $ligneFraisForfaitEtape->setQuantite($formForfait->get('quantiteEtape')->getData());
                $ligneFraisForfaitKm->setQuantite($formForfait->get('quantiteKm')->getData());
                $ligneFraisForfaitNuitee->setQuantite($formForfait->get('quantiteNuitee')->getData());
                $ligneFraisForfaitRepas->setQuantite($formForfait->get('quantiteRepas')->getData());
                $entityManager->persist($ligneFraisForfaitEtape);
                $entityManager->persist($ligneFraisForfaitKm);
                $entityManager->persist($ligneFraisForfaitNuitee);
                $entityManager->persist($ligneFraisForfaitRepas);
                $entityManager->flush();

                return $this->redirectToRoute('app_saisir_fiche_frais');
            }

                $entityManager->persist($ligneFraisForfait);
                $entityManager->flush();
            }



        // Create form instances for each type


        // Process form submissions


        // Render the template with the forms
        return $this->render('saisir_fiche_frais/index.html.twig', [
            'controller_name' => 'SaisirFicheFraisController',
            'formFraisForfait' => $formForfait->createView()
        ]);
    }
}

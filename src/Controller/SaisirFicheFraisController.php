<?php

namespace App\Controller;

use App\Entity\Etat;
use App\Entity\FicheFrais;
use App\Entity\FraisForfait;
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
        $ficheFrais = $entityManager->getRepository(FicheFrais::class)->findOneBy(['user' => $user, 'mois' => date('m')]);

        $dateNow = date('m');
        $formHorsForfait = null;
        $formHorsForfait = $this->createForm(LigneFraisHorsForfaitType::class);
        $formForfait = null;
        if ($ficheFrais == null) {

            $ficheFrais = new FicheFrais();

            $ficheFrais->setMois($dateNow);
            $ficheFrais->setUser($user);
            $ficheFrais->setDateModif(new \DateTime('now'));
            $ficheFrais->setNbJustificatifs(0);
            $ficheFrais->setMontantValid(0);
            $ficheFrais->setEtat($entityManager->getRepository(Etat::class)->findOneBy(['id' => 1]));

            $fraisForfaitRepository = $entityManager->getRepository(FraisForfait::class);
            $fraisEtape = $fraisForfaitRepository->find(1);
            $fraisKm = $fraisForfaitRepository->find(2);
            $fraisNuitee = $fraisForfaitRepository->find(3);
            $fraisRepas = $fraisForfaitRepository->find(4);


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

            $ligneFraisHorsForfait = new LigneFraisHorsForfait();
            $ligneFraisHorsForfait->setFicheFrais($ficheFrais);
            $ligneFraisHorsForfait->setDate(new \DateTime('now'));
            $ligneFraisHorsForfait->setLibelle('test');

            $ficheFrais->addLigneFraisHorsForfait($ligneFraisHorsForfait);

            $entityManager->persist($ficheFrais);
            $entityManager->flush();

            foreach ($ficheFrais->getLigneFraisForfait() as $ligneFraisForfait) {
                $fraisForfait = $ligneFraisForfait->getFraisForfait();
                if ($fraisForfait !== null) {
                    $id = $fraisForfait->getId();
                    if ($id == 1) {
                        $ligneFraisForfaitEtape = $ligneFraisForfait;
                    }
                    if ($id == 2) {
                        $ligneFraisForfaitKm = $ligneFraisForfait;
                    }
                    if ($id == 3) {
                        $ligneFraisForfaitNuitee = $ligneFraisForfait;
                    }
                    if ($id == 4) {
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





        } else {
            $formForfait = $this->createForm(LigneFraisForfaitType::class);
        }




        return $this->render('saisir_fiche_frais/index.html.twig', [
            'controller_name' => 'SaisirFicheFraisController',
            'formFraisForfait' => $formForfait ? $formForfait->createView() : null,
            'formHorsForfait' => $formHorsForfait ? $formHorsForfait->createView() : null
        ]);
    }
}
<?php

namespace App\Controller;

use App\Repository\MarqueRepository;
use App\Repository\VoitureRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class VoitureController extends AbstractController
{
    /**
     * @Route("/voiture", name="app_voiture")
     */
    public function showAll(VoitureRepository $repo,MarqueRepository $repoMar): Response
    {
        $voitures = $repo->findAll();
        $marques = $repoMar->findAll();

        return $this->render('voiture/allVoitures.html.twig', [
            'voitures' => $voitures,
            'marques' => $marques
        ]);
    }

/**
     * @Route("/voitures/{slug}", name="app_voitures_marque")
     */
    public function showByMarque(MarqueRepository $repo, $slug)
    {    // on recupere la marque sur laquelle on a cliqué dont le slug se trouve ds la route
        $marque = $repo->findOneBy(['slug'=> $slug]);
        // on recupere ttes ls marques pour les afficher ds le menu de la page
        $marques = $repo->findAll();
       
        return $this->render('voiture/allVoitures.html.twig', [
            'marques'  => $marques,
            'voitures' => $marque ->getVoitures()

        ]);
    }

  /**
     * @Route("/voiture/{id<\d+>}", name="app_voiture_show")
     */
    public function show($id, VoitureRepository $repo)
    {
      $voiture = $repo->find($id);  
      
      return $this->render('voiture/show.html.twig', [
          
          'voiture' => $voiture
      ]);

    }









}

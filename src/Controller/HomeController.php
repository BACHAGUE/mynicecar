<?php

namespace App\Controller;

use App\Repository\VoitureRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="app_home")
     */
    public function index(VoitureRepository $repo): Response
    {   
        $voitures = $repo->findBy([], 
        ["id" =>"DESC"], 5);


        return $this->render('home/index.html.twig', [
            'voitures' => $voitures,
        ]);
        
        //    dd($voitures);


    }
   
}

<?php

namespace App\Controller;

use DateTime;
use App\Entity\Commande;
use App\Entity\CommandeDetail;
use App\Repository\VoitureRepository;
use App\Repository\CommandeRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\CommandeDetailRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CommandeController extends AbstractController
{
    /**
     * @Route("/passer_commande", name="app_commande_passer")
     */
    public function passerCommande(RequestStack $requestStack,CommandeRepository $repoCom,CommandeDetailRepository $repoDet,VoitureRepository $repoVoit,EntityManagerInterface $manager): Response
    {
          // on crée un objet commande
          $commande = new Commande();

          //on recupere l utilisateur connecté
          $user = $this->getUser();
          //si aucun utilisateur n est connecté on le renvoit à la page de connexion en lui mettant un message flash
          if(!$user)
          {
             $this->addFlash('error', "Veuillez vous connecter afin de passer commande !");

             return $this->redirectToRoute("app_login");
            }
             //on recupere notre panier depuis la session
            $session = $requestStack->getSession();
            $panier = $session->get('panier', []);
       
             if(!$panier)
             {
                $this->addFlash('error', 'Votre panier est vide. Aucune commande passer !');
                return $this->redirectToRoute("app_voitures");
            }
            $dataPanier = [];
            $total = 0;
   
            foreach($panier as $id => $quantite){
   
                $voiture = $repoVoit->find($id);
   
                $dataPanier[] = [
                     'voiture' =>$voiture,
                     'quantite' =>$quantite,
                     'sousTotal' =>$voiture->getPrix() * $quantite
   
                ];
   
                $total += $voiture->getPrix() * $quantite;
   
            }
            //on remplit ls informations pour une commande
             $commande->setUser($user)
                      ->setDateDeCommande(new DateTime("now"))
                      ->setPrix($total);
            // on persist la commande sans faire le flush car il nous faut ls details de commande puis envoyez le tout en bdd
               $repoCom->add($commande);       
            foreach ($dataPanier as $value){
               $commandeDetail = new CommandeDetail();
   
               $produit = $value['voiture'];
               $quantite = $value['quantite'];
               $sousTotal = $value['sousTotal'];
   
               $commandeDetail->setQuantite($quantite)
                              ->setPrix($sousTotal)  
                              ->setVoiture($voiture)
                              ->setCommande($commande);
                              
               $repoDet->add($commandeDetail);               
   
   
            } // on envoie tous les objets persistés (commande et les commandeDetail)
            $manager->flush();
        // une fois la commande passé on supprime le panier
        $session->remove('panier');
        $this-> addFlash ('success', 'Félicitation,commande reussit !');
   
           return $this->redirectToRoute('app_home');
       }
   }
   


   
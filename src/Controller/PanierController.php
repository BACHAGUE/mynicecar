<?php

namespace App\Controller;

use App\Repository\VoitureRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PanierController extends AbstractController
{
    /**
     * @Route("/panier_add_{id<\d+>}", name="app_panier_add")
     */
    public function add($id,RequestStack $requestStack): Response
    {
   //on creé un objet session
   $session = $requestStack->getSession();
   // on creé le panier ds la session si celui n existe pas ou bien on le recupere si il existe  
  $panier = $session->get('panier', []);
  // on verifie si l id existe deja on increment sinon on le crée
  if(empty($panier[$id]))
  {   // l id ici nexiste pas ds le panier ds ce cas la je l ajoute pour la 1ere fois
     $panier[$id] = 1;

  }else{  // l id ici existe deja ds le panier, ds ce cas la j incremente le nombre de +1
      $panier[$id]++; //$panier[]
  }

  // on sauvegarde la situation du panier a ce moment la
   $session->set('panier', $panier);

  return $this-> redirectToRoute("app_panier");
}
/**
  * @Route("/panier", name="app_panier")
  */
 public function show(RequestStack $requestStack, VoitureRepository $repo)
 {
   $session = $requestStack->getSession();
   $panier = $session->get("panier",[]);
   $dataPanier = [];
   $total = 0;

   foreach ($panier as $id => $quantite){
      $voiture = $repo->find($id);
      $dataPanier[]=
        [
         "voiture" => $voiture,
         "quantite" => $quantite

      ];
       $total += $voiture->getPrix() * $quantite;
      
   }

   // dd($dataPanier);

   return $this->render('panier/index.html.twig',[
       'dataPanier' => $dataPanier,
       'total' => $total,
       
   ]);
     // dd($datapanier);
 }
  /**
       * @Route("/panier_delete_voiture_{id<\d+>}", name="app_panier_delete_voiture")
       */
      public function deleteVoiture($id, RequestStack $requestStack)
      {
        $session = $requestStack->getSession();
        $panier = $session->get('panier',[]);

        if(!empty($panier[$id]))
        { //avec unset on vide la ligne id que l on souhaite ds le panier 
          unset($panier[$id]);    

        }else{
           $this->addFlash("error", "la voiture que vous essayez de retirer du panier n existe pas !!!");
           return $this->redirectToRoute('app_panier');
        }
          $session->set("panier",$panier);

          $this->addFlash("success", "la voiture a bien etait retirer du panier !");

          return $this->redirectToRoute('app_panier');
      }
}



















   
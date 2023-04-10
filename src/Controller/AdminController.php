<?php

namespace App\Controller;

use App\Entity\Marque;
use App\Entity\Voiture;
use App\Form\MarqueType;
use App\Form\VoitureType;
use App\Entity\CommandeDetail;
use App\Repository\MarqueRepository;
use App\Repository\VoitureRepository;
use App\Repository\CommandeRepository;
use Doctrine\Persistence\ManagerRegistry;
use App\Repository\CommandeDetailRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;



/**
     * @Route("/admin", name="admin_")
     */


class AdminController extends AbstractController
{
    
 //-----------------------------Admin marque----------------------------------------------------------------
 /**
     * @Route("/marque_add", name="app_marques_add")
     */

    public function add(Request $request,MarqueRepository $repo,
    ManagerRegistry $doctrine,SluggerInterface $slugger): Response
    {
        $form = $this->createForm(MarqueType::class);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        { // on recupere la saisie du champ du formulaire
          $marquesForm = $form->get('nom')->getData();

          //on transforme la chaine de charactere en tableau
          $tab = explode(",", $marquesForm);
          // on parcour le tableau ds voitures en affectant chacune d entres elles à un objet $voiture puis le persister
          foreach ($tab as $nom) {
          $marque = new Marque(); //nouvel objet voiture
          $marque->setNom($nom);//on affecte la voiture $modele à la proprieté "modele" de l objet voiture
          $slug = $slugger->slug($nom);//on creer le slugg à partir du modele de la voiture
          $marque->setSlug($slug);//on affecte le $slug à la proprieté "slug" de l objet voiture
          $repo->add($marque);//persiste
        }
    //on recupere le manager de doctrine pour faire le flush et donc envoyez
    //en bdd les voitures persistéés

    $manager = $doctrine->getManager();
    $manager->flush();

        return $this->redirectToRoute('admin_app_marques'); 
        
    }
    return $this->render("admin/marque/formulaire.html.twig", [
        'formMarque' =>$form->createView()
    ]);

}
/**
     * @Route("/marques", name="app_marques")
   */
  public function showAll(MarqueRepository $repo)
  {
     $marques = $repo->findAll();

     return $this->render("admin/marque/showAll.html.twig",[
        "marques" => $marques
     ]);

  }


 /**
     * @Route("/marque_update_{slug}", name="app_marque_update")
   */
  public function update($slug, Request $request, MarqueRepository $repo , sluggerInterface $slugger)
  {
       $marque = $repo->findOneBy(['slug' => $slug]);
      
       $form = $this->createForm(MarqueType::class, $marque);
       $form->handleRequest($request);
      if  ($form->isSubmitted() && $form->isValid())
      {
        $slug = $slugger->slug($marque->getNom());
        $marque->setSlug($slug);

        $repo->add($marque,1);

        return $this->redirectToRoute('admin_app_marques');

      }

      return $this->render("admin/marque/formulaire.html.twig",[
       "formMarque" => $form->createView()

      ]);
   } 
   /** 
        * @Route("/marque_delete_{id<\d+>}", name="app_marque_delete")  
        */ 
        public function deleteMarque($id, MarqueRepository $repo)
        {
          $marque = $repo->find($id);

          $repo->remove($marque, 1);

          return $this->redirectToRoute("admin_app_marques");

        }
//----------------------------- fin Admin marque----------------------------------------------------------------//




 //-----------------------------------------Debut admin voiture-----------------------------------------------------------------------------//

 /**
     * @Route("/voiture_add", name="app_voiture_add")   
     */

  public function addVoiture(Request $request, VoitureRepository $repo, SluggerInterface $slugger)
  {  
    $voiture = new Voiture();
    $form = $this->createForm(VoitureType::class, $voiture);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) 
    {
      $file = $form->get('photoForm')->getData();

      if($file){
        $fileName = $slugger->slug($voiture->getModele() .uniqid() . '.' . $file->guessExtension());
        try {
            $file->move($this->getParameter('photos_chemin'), $fileName);
      } catch (FileException $e) {
         //gestion des erreurs d'upload
      }
      $voiture->setPhotos($fileName);
      }else{
        $this->addFlash('danger' ,
        'Veuillez remplir le champ photo !');
      }
      $repo->add($voiture,1);

      return $this->redirectToRoute('admin_app_voitures_gestion');
    }
   
   return $this->render('admin/voiture/formulaire.html.twig',[
     'formVoiture' => $form->createView()
   ]);

}

        /**
     * @Route("/gestion_voitures", name="app_voitures_gestion")   
     */ 

     public function gestionVoitures(  VoitureRepository $repo)
     {
          $voitures = $repo->findAll();

          return $this->render("admin/voiture/gestionVoitures.html.twig", [

             'voitures' => $voitures
          ]);    
     }

 /**
     * @Route("/details_voiture_{id<\d+>}", name="app_voiture_details")   
     */ 

     public function detailsVoiture($id, VoitureRepository $repo)
     {
           $voiture = $repo->find($id);         

           return $this->render('admin/voiture/detailsVoiture.html.twig',[

             'voiture'=>$voiture
           ]);

     } 
     
     /**
     * @Route("/voiture_update_{id<\d+>}", name="app_voiture_update")   
     */ 
    public function updateVoiture($id, VoitureRepository $repo,Request $request,SluggerInterface $slugger)
    {
       $voiture = $repo->find($id);
       $form = $this->createForm(VoitureType::class, $voiture);
       $form->handleRequest($request);

       if ($form->isSubmitted() && $form->isValid())
       {
        
         $file = $form->get('photoForm')->getData();
         if($file)
        {
            $fileName = $slugger->slug($voiture->getModele() .uniqid() . '.' . $file->guessExtension());

         try {
               $file->move($this->getParameter('photos_voiture'), $fileName);
         } catch (FileException $e) {
            //gestion des erreurs d'upload
         }

         $voiture->setPhotos($fileName);

        }
         $repo->add($voiture,1);
        
         return $this->redirectToRoute('admin_app_voitures_gestion');
        }
          return $this->render('admin/voiture/formulaire.html.twig',[
            'formVoiture' => $form->createView()
          ]);
    }

     /** 
        * @Route("/voiture_delete_{id<\d+>}", name="app_voiture_delete")   
        */ 

        public function removeVoiture($id, VoitureRepository $repo)
        {
          $voiture = $repo->find($id);

          $repo->remove($voiture, 1);

          return $this->redirectToRoute("admin_app_voitures_gestion");

        }
//-----------------------------------------Fin admin voiture-----------------------------------------------------------------------------//
//--



//-----------------------------------------debut admin commande-----------------------------------------------------------------------------//
  /**
        * @Route("/presentation_commandes", name="app_commande_liste")   
        */ 
        public function presentationCommandes( CommandeRepository $repo, CommandeDetailRepository $repo2)
        {
          $commandes = $repo->findAll();
          //$details = 
          foreach ($commandes as $commande){
            $commande->commandeDetails = $commande->getCommandeDetailsObjects($repo2);
          }
    
             return $this->render("admin/commande/Commandes.html.twig", [
    
                'commandes' => $commandes
             ]); 
            //  dd($commandes);  
             
        }

  
// ---------------------------------------Fin admin commande-----------------------------------------------------------------------------//



}
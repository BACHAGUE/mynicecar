<?php

namespace App\Form;

use App\Entity\Marque;
use App\Entity\Voiture;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class VoitureType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('modele',
            TextType::class,[
             'required'=> false,
             'empty_data'=> '',
             'constraints'=> [
              new Length([
                'min' => 2,
                'minMessage' => "veuillez choisir un modele
                d'au moins 2 charactéres!"

              ])


             ]   
            ])
            ->add('nbPortes')
            ->add('couleur')
            ->add('annee')
            ->add('prix')
            ->add('stock')
            ->add('photoForm',
            FileType::class,[
                'mapped'=>false,
                'required'=>false,
                //'multiple'=> true
            ])
            ->add('marque',
           EntityType::class, [
            'class'=>
            Marque::class, 
            'choice_label'=>'nom',
            'label'=>'choisissez une marque'            
           ])
            ->add('envoyez',
            SubmitType::class,)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Voiture::class,
        ]);
    }
}

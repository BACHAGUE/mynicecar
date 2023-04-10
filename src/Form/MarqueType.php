<?php

namespace App\Form;

use App\Entity\Marque;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class MarqueType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
                
                       
            ->add('nom',
            TextType::class,[
                'label' => "Inserer une ou plusieurs marques en les separant par une virgule et sans espaces"

             ])   
                   
               
              
            
            ->add('envoyez',
            SubmitType::class); 
            
      
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Marque::class,
        ]);
    }
}

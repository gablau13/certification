<?php

namespace App\Form;

use App\Entity\Marque;
use App\Entity\Annonces;
use App\Entity\Categorie;
use App\Entity\Users;
use Doctrine\DBAL\Types\BooleanType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class AnnonceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('titre',TextType::class, [
                "label" => 'Titre',
                'attr' => [
                    'placeholder' => "saisissez une titre", 'class' => 'form-control'
                ]
            ])
            
            ->add('coverImage', FileType::class, [
                "label" => 'Image de couverture',
                'mapped' => false,
                'required' => false,
                'attr' => [
                    'placeholder' => "saisir l'URL", 'class' => 'form-control'
                ]
            ])
            
            ->add('Categorie', EntityType::class, [
                'class'=>Categorie::class,
                'label'=>'Categorie',
                'choice_label'=>'nom',
                'attr' => [
                    'class' => 'form-control'
                    ]
                
            ])
            ->add('Marque', EntityType::class, [
                'class'=>Marque::class,
                'label'=>'Marque',
                'choice_label'=>'nom',
                'attr' => [
                'class' => 'form-control'
                ]
            ])
            ->add('description', TextareaType::class, [
                'attr' => [
                    "label" => 'Description',
                    'placeholder' => "saisir une brève description", 'class' => 'form-control'
                ]
            ])
            ->add('adresse',TextType::class, [
                "label" => 'Adresse',
                'attr' => [
                    'placeholder' => "Adresse", 'class' => 'form-control'
                ]
            ])
            ->add('prix',IntegerType::class, [
                "label" => 'Prix',
                'attr' => [
                    'placeholder' => "Adresse", 'class' => 'form-control'
                ]
            ])
            
            ->add('images', FileType::class, [
                'label'=> false,
                'multiple'=> true,
                'mapped'=> false,
                'required'=> false
            ])
            ->add('Disponibilite',CheckboxType::class, [
                "label" => 'Disponibilité',
                
                
            ])
           
          ;  
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Annonces::class,
        ]);
    }
}

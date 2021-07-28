<?php

namespace App\Form;

use App\Entity\Commentaires;
use Symfony\Component\Form\AbstractType;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class CommentaireType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add('auteur', TextType::class, [
            "label" => 'Prénom et Nom',
            'attr' => [
                'placeholder' => "veuillez entrez votre nom complet", 'class' => 'form-control'
            ]
        ])
        ->add('email', EmailType::class, [
            "label" => 'Email',
            'attr' => [
                'placeholder' => "exemple@exemple.com", 'class' => 'form-control'
            ]
        ])
        ->add('contenu', CKEditorType::class, [
            "label" => 'Commentaire',
            'attr' => [
                'placeholder' => "votre commentaire", 'class' => 'form-control'
            ]
            
        ])
        
        ; 
}
    

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Commentaires::class,
        ]);
    }
}

<?php

namespace App\Form;

use App\Entity\Pfe;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PfeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('titre', TextType::class, [
                'required' => true,
            ])
            ->add('type', TextType::class, [
                'required' => true,
            ])
            ->add('nom1', TextType::class, [
                'required' => false,
            ])
            ->add('nom2', TextType::class, [
                'required' => false,
            ])
            ->add('specialite', TextType::class, [
                'required' => true,
            ])
            ->add('resume', TextType::class, [
                'required' => true,
            ])
            ->add('encadrePar', TextType::class, [
                'required' => true,
            ])
            ->add('technologies', CollectionType::class, [
                'entry_type' => TextType::class,  // chaque élément est un champ de texte
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,  // si vous voulez ajouter ou supprimer des éléments dans le tableau
                'required' => true,
            ])
            ->add('autreTechnologie', TextType::class, [
                'required' => false,
            ])
            ->add('file', FileType::class, [
                'label' => 'Upload File',
                'required' => false,
                'mapped' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Pfe::class,
        ]);
    }

}
<?php

namespace App\Form;

use App\Entity\Lieu;
use App\Entity\Ville;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LieuType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, [
                'label' => 'Nom du lieu : ',
                'attr' => [
                    'placeholder' => 'Musée, Parc, Bar ...'
                ]
            ])
            ->add('rue', TextType::class, [
                'label' => 'Rue : ',
                'attr' => [
                    'placeholder' => '123 Avenue des Acacias'
                ]
            ])
            ->add('latitude', NumberType::class, [
                'label' => 'Latitude : ',
                'html5' => true,
                'scale' => 15,
                'attr' => [
                    'min' => -90,
                    'max' => 90,
                    'placeholder' => 23.5
                ]
            ])
            ->add('longitude', NumberType::class, [
                    'label' => 'Longitude : ',
                    'html5' => true,
                    'scale' => 15,
                    'attr' => [
                        'min' => -180,
                        'max' => 180,
                        'placeholder' => 120.1
                    ]
            ])
            ->add('ville', EntityType::class, [
                'label' => 'Ville : ',
                'class' => Ville::class,
                'choice_label' => 'nom'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Lieu::class,
        ]);
    }
}

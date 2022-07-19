<?php

namespace App\Form;

use App\Entity\Lieu;
use App\Entity\Sortie;
use App\Repository\LieuRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\RangeType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SortieType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, [
                'label' => 'Nom de la sortie : '
            ])
            ->add('dateHeureDebut', DateTimeType::class, [
                'label' => 'Date et heure de la sortie : ',
                'html5' => 'true',
                'widget' => 'single_text'
            ])
            ->add('dateLimiteInscription', DateType::class, [
                'label' => 'Date limite d\'inscription : ',
                'html5' => 'true',
                'widget' => 'single_text'
            ])
            ->add('nbInscriptionsMax', IntegerType::class, [
                'label' => 'Nombre de places : ',
                'attr' => [
                    'min' => 10,
                    'max' => 600,
                ],
            ])
//            ->add('duree', RangeType::class, [
//                'label' => 'Durée : ',
//                'attr' => [
//                    'min' => 10,
//                    'max' => 600,
//                ]
//            ])
            ->add('duree', IntegerType::class, [
                'label' => 'Durée : ',
                'data' => '90',
            ])
            ->add('infosSortie', TextareaType::class, [
                'label' => 'Description et infos : '
            ])
            ->add('lieu', EntityType::class, [
                'class' => Lieu::class,
                'query_builder' => function (LieuRepository $lieuRepository) {
                    return $lieuRepository->createQueryBuilder('lieu')
                        ->orderBy('lieu.nom', 'ASC');
                },
                'choice_label' => 'nom'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Sortie::class,
        ]);
    }
}

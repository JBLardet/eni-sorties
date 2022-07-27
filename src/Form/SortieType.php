<?php

namespace App\Form;

use App\Entity\Campus;
use App\Entity\Lieu;
use App\Entity\Sortie;
use App\Entity\Ville;
use App\Repository\LieuRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\SubmitButton;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SortieType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, [
                'label' => 'Nom de la sortie : ',
                'attr' => [
                    'placeholder' => 'Apprenons à nous connaître au bar !'
                    ]
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
                'data' => 20,
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
                'label' => 'Description et infos : ',
                'attr' => [
                    'placeholder' => 'Décrivez votre sortie ici'
                ]
            ])
            ->add('campus', EntityType::class, [
                'label' => 'Campus : ',
                'class' => Campus::class,
                //'data' => ,
                //TODO : ajouter au repository ce qu'il faut pour avoir par défaut le campus de l'utilisateur
                'choice_label' => 'nom'
            ])
            ->add('ville', EntityType::class, [
                'label' => 'Ville : ',
                'class' => Ville::class,
                'choice_label' => 'nom',
                'mapped' => false,
            ])
            ->add('lieu', EntityType::class, [
                'label' => 'Lieu : ',
                'class' => Lieu::class,
                'query_builder' => function (LieuRepository $lieuRepository) {
                    return $lieuRepository->createQueryBuilder('lieu')
                        //->andWhere('lieu.ville')
                        //todo: Niveau 2/3 compléter ça pour ne prendre que les lieux de la ville sélectionnée
                        ->orderBy('lieu.nom', 'ASC');
                },
                'choice_label' => 'nom'
            ])
            ->add('rue', TextType::class, [
                'label' => 'Rue : ',
                'disabled' => true,
                'mapped' => false,
                'attr' => [
                    'placeholder' => 'À débloquer au niveau 2'
                ]
            ])
            ->add('codePostal', TextType::class, [
                'label' => 'Code postal : ',
                'disabled' => true,
                'mapped' => false,
                'attr' => [
                    'placeholder' => 'À débloquer au niveau 2'
                ]
            ])
            ->add('latitude', NumberType::class, [
                'label' => 'Latitude : ',
                'disabled' => true,
                'mapped' => false,
                'attr' => [
                    'placeholder' => 'À débloquer au niveau 2'
                ]
            ])
            ->add('longitude', NumberType::class, [
                'label' => 'Longitude : ',
                'disabled' => true,
                'mapped' => false,
                'attr' => [
                    'placeholder' => 'À débloquer au niveau 2'
                ]
            ])
            ->add('enregistrer', SubmitType::class, [
                'label' => 'Enregistrer',
                'attr' => [
                'class' => 'btn'
                ]
            ])
            ->add('publier', SubmitType::class, [
                'label' => 'Publier la sortie',
                'attr' => [
                'class' => 'btn'
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Sortie::class,
        ]);
    }
}

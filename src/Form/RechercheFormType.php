<?php

namespace App\Form;

use App\Entity\Campus;
use App\Entity\Sortie;
use App\Form\model\RechercheFormModel;
use App\Repository\UserRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RechercheFormType extends AbstractType
{

    private UserRepository $userRepository;

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('campus', EntityType::class, [
                'label' => 'Campus',
                'class' => Campus::class,
                'choice_label' => 'nom',
                'placeholder' => 'Tous les campus',
                'required' => false
                //TODO : par défaut, le campus de l'utilisateur connecté
            ])
            ->add('rechercheParNom', TextType::class, [
                'label' => 'Le nom de la sortie contient : ',
                'required' => false
            ])
            ->add('dateMin', DateType::class, [
                'html5' => 'true',
                'widget' => 'single_text',
                'label' => 'Entre ',
                'required' => false
            ])
            ->add('dateMax',DateType::class, [
                'html5' => 'true',
                'widget' => 'single_text',
                'label' => 'et  ',
                'required' => false
            ])
            ->add('organisateur', CheckboxType::class, [
                'label' => "Sorties dont je suis l'organisateur/trice",
                'required' => false,
                'data' => true
            ])
            ->add('participant', CheckboxType::class, [
                'label' => "Sorties auxquelles je suis inscrit/e",
                'required' => false,
                'data' => true
            ])
            ->add('nonParticipant', CheckboxType::class, [
                'label' => "Sorties auxquelles je ne suis pas inscrit/e",
                'required' => false,
                'data' => true
            ])
            ->add('sortiesPassees', CheckboxType::class, [
                'label' => "Sorties passées",
                'required' => false
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => RechercheFormModel::class,
        ]);
    }
}
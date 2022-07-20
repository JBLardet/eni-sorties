<?php

namespace App\Form;

use App\Entity\Campus;
use App\Entity\Sortie;
use App\Form\model\RechercheFormModel;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RechercheFormType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('campus', EntityType::class, [
                'label' => 'Campus',
                'class' => Campus::class,
                'choice_label' => 'nom'
            ])
            ->add('rechercheParNom', TextType::class, [
                'label' => 'Le nom de la sortie contient : '
            ])
            ->add('dateMin', DateType::class, [
                'label' => 'Entre '
            ])
            ->add('dateMax',DateType::class, [
                'label' => 'et  '
            ])
            ->add('organisateur', CheckboxType::class, [
                'label' => "Sorties dont je suis l'organisateur/trice"
            ])
            ->add('participant', CheckboxType::class, [
                'label' => "Sorties auxquelles je suis inscrit/e"
            ])
            ->add('nonParticipant', CheckboxType::class, [
                'label' => "Sorties auxquelles je ne suis pas inscrit/e"
            ])
            ->add('sortiesPassees', CheckboxType::class, [
                'label' => "Sorties passées"
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => RechercheFormModel::class,
        ]);
    }
}
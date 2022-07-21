<?php

namespace App\Form;

use App\Entity\User;
use phpDocumentor\Reflection\Types\Boolean;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProfileType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $default = [
            'actif' => new Boolean(true)
        ];
        $builder
            ->add('email')
            ->add('roles', null, ['required' => false])
            ->add('password')
            ->add('pseudo')
            ->add('nom')
            ->add('prenom')
            ->add('tel')
            ->add('actif', CheckboxType::class)
            ->add('campus')
            ->add('campus', EntityType::class, [
                'class' => Campus::class,
                'query_builder' => function (LieuRepository $lieuRepository) {
                    return $lieuRepository->createQueryBuilder('lieu')
                        ->orderBy('lieu.nom', 'ASC');
                },
                'choice_label' => 'nom'
            ])

            ->add('sorties')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}

<?php

namespace App\Form;

use App\Entity\Campus;
use App\Entity\User;
use App\Repository\LieuRepository;
use phpDocumentor\Reflection\Types\Boolean;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $default = [
            'actif' => new Boolean(true)
        ];
        $builder
            ->add('email')
 //           ->add('roles', null, ['required' => false])
 //           ->add('password')
            ->add('pseudo')
            ->add('nom')
            ->add('prenom')
            ->add('tel')
//            ->add('actif', CheckboxType::class)

            ->add('campus', EntityType::class, [
               'label' => 'Campus :',
                'class' => Campus::class,
//                'query_builder' => function (LieuRepository $lieuRepository) {
//                    return $lieuRepository->createQueryBuilder('lieu')
//                        ->orderBy('lieu.nom', 'ASC');
//                },
                'choice_label' => 'nom'
           ])
            ->add('photo',FileType::class, [
                'label' => 'Photo (jpg/gif/png max 2Mo)',
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new File([
                        'maxSize'=>'2M',
                        'mimeTypes' => ['image/*'],
                        'mimeTypesMessage' => 'Le fichier est invalide. Veuillez télécharger une photo au format .jpg ou .jpeg de moins de 2Mo'
                    ])
                ],

            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}

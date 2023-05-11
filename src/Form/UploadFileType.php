<?php

namespace App\Form;

use App\Entity\Fichier;
use App\Entity\Historique;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class UploadFileType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {

         $builder
            ->add('chargement', FileType::class, [
                'label' => 'Fichier a charger (xls, cvs)',
                'required' => true,
                'attr' => [
                    'class' => 'form-control',
                    // 'id' => 'fichier_title'
                ],
                'constraints' => [
                    new File([
                        'maxSize' => '2M',
                        'mimeTypes' => [
                            'text/csv',
                            'application/vnd.ms-excel',
                            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                        ],
                        'mimeTypesMessage' => 'Veuillez charger un document Excel!',
                    ])
                ],
            ])
            ->add('date_debut_extraction', DateTimeType::class, [
                'label' => 'Date début extraction',
                'widget' => 'single_text',
                'required' => true,
            ])
            ->add('date_fin_extraction', DateTimeType::class, [
                'label' => 'Date fin extraction',
                'widget' => 'single_text',
                'required' => true,
            ])
            ->add('charger', SubmitType::class, [
                'attr' => [
                    'class' => 'btn btn-success submit px-3',
                    'id' => 'btnLoadFile'
                ]
            ]);
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Historique::class,
        ]);
    }
}

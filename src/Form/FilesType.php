<?php

namespace App\Form;

use App\Entity\Files;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class FilesType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('fileName', FileType::class, [
                'label' => 'Fichier de l\'examen',
                // unmapped means that this field is not associated to any entity property
                'mapped' => false,
                'required' => true,
                'constraints' => [
                    new File([
                        'maxSize' => '8192k',
                        'mimeTypes' => [
                            'application/pdf',
                            'application/x-pdf',
                        ],
                        'mimeTypesMessage' => 'Le fichier sélectionné n\'est un document PDF.',
                    ]),
                ],
            ])
            ->add('FullName',TextType::class, [ "required" => true])
            ->add('AcademicYear', IntegerType::class, [ "required" => true])
            ->add('university',TextType::class, [ "required" => true])
            ->add('school',TextType::class, [ "required" => true])
            ->add('field',TextType::class, [ "required" => true])
            ->add('category',TextType::class, [ "required" => true])
            ->add('author',TextType::class, [ "required" => true])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Files::class,
        ]);
    }
}
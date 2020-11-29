<?php

namespace App\Form;

use App\Entity\Item;
use App\Service\Constants\ItemType as Types;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vich\UploaderBundle\Form\Type\VichImageType;

class ItemType extends AbstractType
{
    /**
     * @var Types
     */
    private $types;

    public function __construct(Types $types)
    {
        $this->types = $types;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class)
            ->add('type', ChoiceType::class, [
                'choices' => $this->types->getValuesTranslated(),
                'required' => true
            ])
            ->add('price', MoneyType::class)
            ->add('descriptionEn', TextareaType::class)
            ->add('descriptionFr', TextareaType::class, [
                'required' => false
            ])
            ->add('compositionEn', TextType::class, [
                'attr' => ['class' => 'composition']
            ])
            ->add('compositionFr', TextType::class, [
                'attr' => ['class' => 'composition'],
                'required' => false
            ])
            ->add('coverFile', VichImageType::class, [
                'required' => false,
                'attr' => [
                    'class' => 'cover',
                    'data-title' => 'Cover',
                    'novalidate' => 'novalidate'
                ],
                'label' => 'Cover'
            ])
            ->add('size', TextType::class)
            ->add('disabled', CheckboxType::class, [
                'required' => false
            ])
            ->add('images', CollectionType::class, [
                'entry_type' => ImageType::class,
                'entry_options' => ['label' => false],
                'by_reference' => false,
                'prototype' => true,
                'allow_add' => true,
                'allow_delete' => true,
                'required' => false
            ])
            ->add('submit', SubmitType::class, [
                'attr' => ['class' => 'btn btn-primary w-100']
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Item::class,
        ]);
    }
}

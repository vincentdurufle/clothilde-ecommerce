<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class ContactType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'about.contact.form.name',
                'translation_domain' => 'app'
            ])
            ->add('email', EmailType::class, [
                'required' => true,
            ])
            ->add('content', TextareaType::class, [
                'label' => 'about.contact.form.content',
                'translation_domain' => 'app'
            ])
            ->add('submit', SubmitType::class, [
                'attr' => ['class' => 'btn btn-primary w-100'],
                'translation_domain' => 'app',
                'label' => 'about.contact.form.submit'
            ]);
    }
}

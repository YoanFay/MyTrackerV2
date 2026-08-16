<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class HistoryAddType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {

        $builder
            ->add('id', TextType::class, [
                'label' => 'ID',
                'attr' => [
                    'class' => 'form-control',
                ],
            ])
            ->add('watchedAt', DateTimeType::class, [
                'label' => 'Date et heure de visionnage',
                'widget' => 'single_text',
                'attr' => [
                    'class' => 'form-control',
                ],
            ]);
    }


    public function configureOptions(OptionsResolver $resolver): void
    {

        $resolver->setDefaults([
            'data_class' => null, // ou ta classe HistoryEntry si tu en as une
        ]);
    }
}

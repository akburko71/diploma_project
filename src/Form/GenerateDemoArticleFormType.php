<?php

namespace App\Form;

use App\Form\Model\DemoArticleModel;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class GenerateDemoArticleFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('articleTitle', TextType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Заголовок статьи',
                    'autofocus' => true,
                    'disabled' => $options['disabled']
                ]
            ])
            ->add('articleWord', TextType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Продвигаемое слово',
                    'disabled' => $options['disabled']
                ]
            ])
            ->add('btnTry', SubmitType::class, [
                'label' => 'Попробовать',
                'attr' => [
                    'class' => 'btn btn-lg btn-primary btn-block text-uppercase',
                    'disabled' => $options['disabled'],
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            "data_class" => DemoArticleModel::class
        ]);
    }
}

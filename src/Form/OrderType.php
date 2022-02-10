<?php

namespace App\Form;

use App\Entity\Order;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class OrderType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'data' => "Test"
            ])
            ->add('start', DateType::class, [
                'widget' => 'single_text',
                'data' => (new \DateTime())->setTimestamp(1644793200)
            ])
            ->add('end', DateType::class, [
                'widget' => 'single_text',
                'data' => (new \DateTime())->setTimestamp(1645052400)
            ])
            ->add('description', TextareaType::class, [
                'label' => false,
                'data' => "Hello World"
            ])
            ->add('teacher')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Order::class,
        ]);
    }
}

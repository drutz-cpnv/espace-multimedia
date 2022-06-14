<?php

namespace App\Form\AdminType;

use App\Entity\Order;
use App\Entity\State;
use App\Entity\Teacher;
use App\Repository\StateRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AdminTestEmailOrderFormType extends AbstractType
{
    public function __construct(
        private StateRepository $stateRepository
    )
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('start', DateType::class, [
                'widget' => 'single_text',
                'data' => (new \DateTimeImmutable())->modify("+2 days")
            ])
            ->add('end', DateType::class, [
                'widget' => 'single_text',
                'data' => (new \DateTimeImmutable())->modify("+10 days")
            ])
            ->add('description', TextareaType::class, [
                'data' => "Voici une super description"
            ])
            ->add('title', TextType::class, [
                'data' => "Projet test"
            ])
            ->add('teacher', EntityType::class, [
                'class' => Teacher::class,
            ])
            ->add('items')
            ->add('equipment')
            ->add('state', EntityType::class, [
                'mapped' => false,
                'class' => State::class,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Order::class,
        ]);
    }

    private function getStateChoices(): array
    {
        $output = [];

        foreach ($this->stateRepository->findAll() as $state) {
            $output[$state->getName()] = (string)$state->getId();
        }

        return $output;

    }
}

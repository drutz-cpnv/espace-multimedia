<?php

namespace App\Form\AdminType;

use App\Entity\User;
use App\Form\FieldType\SwitchType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AdminUserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('family_name')
            ->add('given_name')
            ->add('email')
            ->add('status', ChoiceType::class, [
                'choices' => $this->getChoices(User::STATUS)
            ])
            ->add('isVerified', SwitchType::class)
            ->add('roles', ChoiceType::class, [
                'choices' => $this->getChoices(User::ROLES),
                'multiple' => true
            ])
            ->setAttribute('class', 'form')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }

    private function getChoices($choices)
    {
        return array_flip($choices);
    }
}

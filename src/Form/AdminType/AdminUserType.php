<?php

namespace App\Form\AdminType;

use App\Entity\User;
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
            ->add('isVerified')
            ->add('roles', ChoiceType::class, [
                'choices' => $this->getChoices(),
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

    private function getChoices()
    {
        $choices = User::ROLES;
        $newChoices = [];
        foreach ($choices as $key => $choice) {
            $newChoices[$choice] = $key;
        }
        return $newChoices;
    }
}

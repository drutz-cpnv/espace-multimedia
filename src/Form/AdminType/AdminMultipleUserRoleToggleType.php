<?php

namespace App\Form\AdminType;

use App\Data\MultipleUserRoleToggle;
use App\Entity\Category;
use App\Entity\Equipment;
use App\Entity\User;
use App\Form\CreateOptionEntityType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vich\UploaderBundle\Form\Type\VichImageType;

class AdminMultipleUserRoleToggleType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('users', EntityType::class, [
                'class' => User::class,
                'multiple' => true,
            ])
            ->add('role', ChoiceType::class, [
                'choices' => $this->getChoices()
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => MultipleUserRoleToggle::class,
        ]);
    }

    private function getChoices(): array
    {
        return array_flip(User::ROLES);
    }

}

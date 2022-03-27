<?php

namespace App\Form;

use App\Entity\Brand;
use App\Entity\Category;
use App\Entity\EquipmentSearch;
use App\Entity\EquipmentType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EquipmentSearchType extends AbstractType
{
    public function __construct(
        private EntityManagerInterface $entityManager
    )
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('categories', EntityType::class, [
                'required' => false,
                'class' => Category::class,
                'multiple' => true,
                'choice_label' => 'name',
            ])
            ->add('types', EntityType::class, [
                'label' => 'Types d\'équipement',
                'required' => false,
                'class' => EquipmentType::class,
                'multiple' => true,
                'choice_label' => 'name',
            ])
            ->add('brands', EntityType::class, [
                'required' => false,
                'class' => Brand::class,
                'multiple' => true,
                'choice_label' => 'name',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => EquipmentSearch::class,
            'method' => 'GET',
            'csrf_protection' => false
        ]);
    }

    public function getBlockPrefix(): string
    {
        return '';
    }

}

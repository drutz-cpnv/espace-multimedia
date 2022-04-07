<?php

namespace App\Form\AdminType;

use App\Entity\Brand;
use App\Entity\Cabinet;
use App\Entity\Category;
use App\Entity\Equipment;
use App\Entity\EquipmentType;
use App\Form\CreateOptionEntityType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\ChoiceList\ChoiceList;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AdminEquipmentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name')
            ->add('description')
            ->add('type')
            ->add('imageFile')
            ->add('categories', CreateOptionEntityType::class, [
                'class' => Category::class,
                'post' => '/api/categories'
            ])
            ->add('enabled', CheckboxType::class, [
                'label' => "Activé (l'élément peut être commandé)",
                'required' => false
            ])
            ->add('brand')
            ->add('cabinet', EntityType::class, [
                'class' => Cabinet::class,
                'group_by' => ChoiceList::groupBy($this, 'location')
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Equipment::class,
        ]);
    }
}

<?php

namespace App\Form;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\ChoiceList\View\ChoiceView;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CreateOptionEntityType extends AbstractType
{

    public function __construct(
        private EntityManagerInterface $em
    )
    {
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setRequired('class');
        $resolver->setDefaults([
            'compound' => false,
            'multiple' => true,
            'post' => '/api/',
            'value_property' => 'id',
            'label_property' => 'name',
        ]);
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addModelTransformer(new CallbackTransformer(
            function ($value) {
                return $value->map(fn($d) => (string)$d->getId())->toArray();
            },
            function (array $ids) use ($options) : ArrayCollection {
                if (empty($ids)) {
                    return new ArrayCollection([]);
                }
                return new ArrayCollection(
                    $this->em->getRepository($options['class'])->findBy(['id' => $ids])
                );
            }
        ));
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['expanded'] = false;
        $view->vars['placeholder'] = null;
        $view->vars['placeholder_in_choices'] = false;
        $view->vars['multiple'] = $options['multiple'];
        $view->vars['preferred_choices'] = [];
        $view->vars['choices'] = $this->choices2($options['class']);
        $view->vars['choice_translation_domain'] = false;
        $view->vars['full_name'] .= '[]';
        $view->vars['attr']['data-create-option'] = $options['post'];
        $view->vars['attr']['data-value'] = $options['value_property'];
        $view->vars['attr']['data-label'] = $options['label_property'];

    }

    public function getBlockPrefix()
    {
        return 'choice';
    }

    private function choices(Collection $value): array
    {
        return $value->map(fn($d) => new ChoiceView($d, (string)$d->getId(), (string)$d))->toArray();
    }

    private function choices2($class): array
    {
        $r = $this->em->getRepository($class);
        $v = new ArrayCollection($r->findAll());
        return $v->map(fn($d) => new ChoiceView($d, (string)$d->getId(), (string)$d))->toArray();
    }

}
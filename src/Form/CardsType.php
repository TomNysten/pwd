<?php

namespace App\Form;

use App\Entity\Cards;
use App\Entity\Colors;
use App\Entity\Rarities;
use App\Entity\Sets;
use App\Entity\Types;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CardsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('cardName')
            ->add('cardSetNum')
            ->add('cardAlter')
            ->add('cardImage')

            ->add('cardColor', EntityType::class, [
                // looks for choices from this entity
                'class' => Colors::class,

                // uses the User.username property as the visible option string
                'choice_label' => 'name',

                // used to render a select box, check boxes or radios
                // 'multiple' => true,
                // 'expanded' => true,
            ])
            ->add('cardRarity', EntityType::class, [
                'class' => Rarities::class,
                'choice_label' => 'name',
            ])
            ->add('cardSet', EntityType::class, [
                'class' => Sets::class,
                'choice_label' => 'name',
            ])
            ->add('cardType', EntityType::class, [
                'class' => Types::class,
                'choice_label' => 'name',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Cards::class,
        ]);
    }
}

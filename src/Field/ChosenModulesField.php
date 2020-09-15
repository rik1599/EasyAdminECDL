<?php

namespace App\Field;

use App\Entity\CertificationModule;
use App\Entity\Module;
use EasyCorp\Bundle\EasyAdminBundle\Contracts\Field\FieldInterface;
use EasyCorp\Bundle\EasyAdminBundle\Field\FieldTrait;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class ChosenModulesField implements FieldInterface
{
    use FieldTrait;

    public static function new(string $propertyName, ?string $label = null): self
    {
        return (new self())
            ->setProperty($propertyName)
            ->setLabel($label)
            ->setFormType(EntityType::class)
            ->setFormTypeOptions([
                'class' => Module::class,
                'choice_label' => 'nome',
                'expanded' => 'true',
                'multiple' => 'true'
            ]);
    }
}
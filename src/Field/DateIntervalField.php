<?php

namespace App\Field;

use EasyCorp\Bundle\EasyAdminBundle\Contracts\Field\FieldInterface;
use EasyCorp\Bundle\EasyAdminBundle\Field\FieldTrait;
use Symfony\Component\Form\Extension\Core\Type\DateIntervalType;

class DateIntervalField implements FieldInterface
{
    use FieldTrait;

    public static function new(string $propertyName, ?string $label = null): self 
    {
        return (new self())
            ->setProperty($propertyName)
            ->setLabel($label)
            ->setFormType(DateIntervalType::class)
            ->setFormTypeOptions([
                'widget' => 'choice',
                'with_years' => true,
                'with_months' => false,
                'with_days' => false
            ])
            ->setTemplatePath('fields/dateinterval.html.twig');
    }
}
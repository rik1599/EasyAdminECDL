<?php

namespace App\Field;

use EasyCorp\Bundle\EasyAdminBundle\Contracts\Field\FieldInterface;
use EasyCorp\Bundle\EasyAdminBundle\Field\FieldTrait;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;

class PasswordField implements FieldInterface
{
    use FieldTrait;

    public static function new(string $propertyName, ?string $label = null): self 
    {
        return (new self())
            ->setProperty($propertyName)
            ->setLabel($label)
            ->setFormType(RepeatedType::class)
            ->setFormTypeOptions([
                'type' => PasswordType::class,
                'invalid_message' => 'Le password non corrispondono',
                'first_options' => ['label' => $label],
                'second_options' => ['label' => "Ripeti $label"]
            ]);
    }
}

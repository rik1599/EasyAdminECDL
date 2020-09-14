<?php

namespace App\Form;

use App\Entity\CertificationModule;
use App\Entity\Module;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ModuleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('module', EntityType::class, [
                'class' => Module::class,
                'choice_label' => 'nome',
                'label' => false,
            ])
            ->add('isMandatory', CheckboxType::class, [
                'label' => 'Esame obbligatorio?',
                'required' => false
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => CertificationModule::class,
        ]);
    }
}

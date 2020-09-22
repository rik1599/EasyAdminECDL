<?php

namespace App\Form;

use App\Entity\CertificationModule;
use App\Entity\SkillCardModule;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SkillCardModuleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('module', EntityType::class, [
                'label' => false,
                'class' => CertificationModule::class,
                'choice_label' => "module.nome",
                'query_builder' => function (EntityRepository $er) use ($options) {
                    return $er->createQueryBuilder('cm')
                        ->where("cm.certification = :cId")
                        ->setParameter('cId', $options['certification']->getId());
                }
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => SkillCardModule::class,
        ]);
        $resolver->setRequired([
            'certification'
        ]);
    }
}

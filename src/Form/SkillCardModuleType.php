<?php

namespace App\Form;

use App\Entity\CertificationModule;
use App\Entity\SkillCard;
use App\Entity\SkillCardModule;
use App\Repository\CertificationModuleRepository;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SkillCardModuleType extends AbstractType
{
    /** @var CertificationModuleRepository */
    private $certificationModuleRepository;

    public function __construct(CertificationModuleRepository $certificationModuleRepository) {
        $this->certificationModuleRepository = $certificationModuleRepository;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var SkillCard */
        $skillCard = $options['skillCard'];
        $choices = array_map(function (SkillCardModule $skillCardModule) {
            return $skillCardModule->getModule();
        }, $skillCard->getSkillCardModules()->getValues());

        $choices = array_merge($choices, $this->certificationModuleRepository->findBy([
            'certification' => $skillCard->getCertification()->getId()
        ]));


        $builder
            ->add('module', EntityType::class, [
                'label' => false,
                'class' => CertificationModule::class,
                'choice_label' => "module.nome",
                'choices' => $choices
            ])
            ->add('isPassed', CheckboxType::class, [
                'label' => 'Già superato?',
                'required' => false
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => SkillCardModule::class,
        ]);
        $resolver->setRequired([
            'skillCard'
        ]);
    }
}

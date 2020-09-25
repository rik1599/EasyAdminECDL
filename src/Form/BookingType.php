<?php

namespace App\Form;

use App\Entity\Booking;
use App\Entity\Session;
use App\Entity\SkillCard;
use App\Entity\SkillCardModule;
use App\Entity\Student;
use App\Enum\EnumSkillCard;
use DateTime;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BookingType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var Student */
        $student = $options['student'];

        $builder->add('skillCard', EntityType::class, [
            'class' => SkillCard::class,
            'choices' => $student->getSkillCards()->filter(function(SkillCard $skillCard) {
                return $skillCard->getStatus() != EnumSkillCard::EXPIRED && 
                    count($skillCard->getSkillCardModulesNotPassed()) > 0;
            }),
            'placeholder' => ''
        ])
        ->add('session');

        $builder->addEventListener(FormEvents::PRE_SET_DATA, 
            function(FormEvent $formEvent) {
                /** @var Booking */
                $data = $formEvent->getData();
                $this->formModifierBySkillCard($formEvent->getForm(), $data->getSkillCard());
                $this->formModifierBySession($formEvent->getForm(), $data->getSession());
            });

        $builder->get('skillCard')->addEventListener(FormEvents::POST_SUBMIT, 
            function(FormEvent $formEvent) {
                /** @var SkillCard */
                $skillCard = $formEvent->getForm()->getData();
                $this->formModifierBySkillCard($formEvent->getForm()->getParent(), $skillCard);
            }
        );

        $builder->get('session')->addEventListener(FormEvents::POST_SUBMIT,
            function (FormEvent $formEvent) {
                /** @var Session */
                $session = $formEvent->getForm()->getData();
                $this->formModifierBySession($formEvent->getForm()->getParent(), $session);
            }
        );

        $builder->add('save', SubmitType::class);
    }

    protected function formModifierBySkillCard(FormInterface $form, SkillCard $skillCard = null) {
        $form->add('module', EntityType::class, [
            'class' => SkillCardModule::class,
            'choices' => is_null($skillCard) ? [] : $skillCard->getSkillCardModulesNotPassed(),
            'choice_label' => 'module.module.nome'
        ]);
        
        $form->add('session', EntityType::class, [
            'class' => Session::class,
            'placeholder' => '',
            'query_builder' => function (EntityRepository $er) use ($skillCard) {
                $qb = $er->createQueryBuilder('s');
                if (is_null($skillCard)) {
                    $qb->where('s = 0');
                } else {
                    $qb->where('s.certification = :id')
                        ->andWhere('s.subscribeExpireDate > :date')
                        ->setParameter('id', $skillCard->getCertification()->getId())
                        ->setParameter('date', new DateTime());
                }
                return $qb;
            },
        ]);
    }

    protected function formModifierBySession(FormInterface $form, Session $session = null) {
        $options = is_null($session) ? [] : range(0, $session->getRounds());
        $form->add('turn', ChoiceType::class, [
            'choices' => is_null($session) ? [] : range(0, $session->getRounds()),
        ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Booking::class,
        ]);

        $resolver->setRequired([
            'student'
        ]);
    }
}

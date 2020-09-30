<?php

namespace App\Form;

use App\Entity\Booking;
use App\Entity\Session;
use App\Entity\SkillCard;
use App\Entity\SkillCardModule;
use App\Entity\Student;
use App\Enum\EnumSkillCard;
use App\Repository\SkillCardRepository;
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
    /** @var SkillCardRepository */
    private $skillCardRepository;

    public function __construct(SkillCardRepository $skillCardRepository) {
        $this->skillCardRepository = $skillCardRepository;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var Student */
        $student = $options['student'];

        $builder->add('skillCard', EntityType::class, [
            'class' => SkillCard::class,
            'choices' => $student->getSkillCards()->filter(function (SkillCard $skillCard) {
                return $skillCard->getStatus() != EnumSkillCard::EXPIRED &&
                    count($skillCard->getSkillCardModulesNotPassed()) > 0;
            }),
            'placeholder' => ''
        ]);

        $builder->addEventListener(
            FormEvents::PRE_SET_DATA,
            function (FormEvent $formEvent) {
                /** @var Booking */
                $data = $formEvent->getData();
                $this->formModifierBySkillCard($formEvent->getForm(), $data->getSkillCard());
                $this->addSessionField($formEvent->getForm(), $data->getSkillCard());
            }
        );

        $builder->addEventListener(
            FormEvents::PRE_SUBMIT,
            function (FormEvent $formEvent) {
                $data = $formEvent->getData();
                $form = $formEvent->getForm();

                if (!isset($data['skillCard'])) {
                    return;
                }
                $skillCardID = $data['skillCard'];
                $this->addSessionField($form, $this->skillCardRepository->find($skillCardID));
            }
        );

        $builder->get('skillCard')->addEventListener(
            FormEvents::POST_SUBMIT,
            function (FormEvent $formEvent) {
                $skillCard = $formEvent->getForm()->getData();
                $this->formModifierBySkillCard($formEvent->getForm()->getParent(), $skillCard);
                $this->addSessionField($formEvent->getForm()->getParent(), $skillCard);
            }
        );

        $builder->add('save', SubmitType::class);
    }

    protected function formModifierBySkillCard(FormInterface $form, SkillCard $skillCard = null)
    {
        $form->add('module', EntityType::class, [
            'class' => SkillCardModule::class,
            'choices' => is_null($skillCard) ? [] : $skillCard->getSkillCardModulesNotPassed(),
            'choice_label' => 'module.module.nome'
        ]);
    }

    protected function addSessionField(FormInterface $form, SkillCard $skillCard = null)
    {
        $builder = $form->getConfig()->getFormFactory()->createNamedBuilder('session', EntityType::class, null, [
            'auto_initialize' => false,
            'class' => Session::class,
            'placeholder' => '',
            'query_builder' => function (EntityRepository $er) use ($skillCard) {
                $qb = $er->createQueryBuilder('s');
                if (is_null($skillCard)) {
                    $qb->where('s = 0');
                } else {
                    $qb->where('s.certification = :id')
                        ->andWhere('s.subscribeExpireDate > :date')
                        ->setParameter('id', $skillCard->getCertification())
                        ->setParameter('date', new DateTime());
                }
                return $qb;
            },
        ]);

        $builder->addEventListener(
            FormEvents::PRE_SET_DATA,
            function (FormEvent $formEvent) {
                $session = $formEvent->getData();
                $this->formModifierBySession($formEvent->getForm()->getParent(), $session);
            }
        );

        $builder->addEventListener(
            FormEvents::POST_SUBMIT,
            function (FormEvent $formEvent) {
                $session = $formEvent->getForm()->getData();
                $this->formModifierBySession($formEvent->getForm()->getParent(), $session);
            }
        );
        
        $form->add($builder->getForm());
    }

    protected function formModifierBySession(FormInterface $form, Session $session = null)
    {
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

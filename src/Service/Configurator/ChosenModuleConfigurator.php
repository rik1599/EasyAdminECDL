<?php

namespace App\Service\Configurator;

use App\Entity\CertificationModule;
use App\Entity\SkillCard;
use App\Field\ChosenModulesField;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Contracts\Field\FieldConfiguratorInterface;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\FieldDto;

final class ChosenModuleConfigurator implements FieldConfiguratorInterface
{
    public function supports(FieldDto $field, EntityDto $entityDto): bool
    {
        return ChosenModulesField::class === $field->getFieldFqcn();
    }

    public function configure(FieldDto $field, EntityDto $entityDto, AdminContext $context): void
    {
        /** @var SkillCard */
        $skillCard = $entityDto->getInstance();
        $certModulesIterator = $skillCard->getCertification()->getCertificationModules()->getIterator();
        $modules = [];

        foreach ($certModulesIterator as $module) {
            /** @var CertificationModule $module */
            if (!$module->getIsMandatory()) {
                $modules[] = $module->getModule();
            }
        }

        $field->setFormTypeOptionIfNotSet('choices', $modules);
    }
}

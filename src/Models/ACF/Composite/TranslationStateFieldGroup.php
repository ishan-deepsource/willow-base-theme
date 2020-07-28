<?php

namespace Bonnier\Willow\Base\Models\ACF\Composite;

use Bonnier\Willow\Base\Models\ACF\ACFField;
use Bonnier\Willow\Base\Models\ACF\ACFGroup;
use Bonnier\Willow\Base\Models\ACF\Fields\DatePickerField;
use Bonnier\Willow\Base\Models\ACF\Fields\SelectField;
use Bonnier\Willow\Base\Models\ACF\Properties\ACFLocation;
use Bonnier\Willow\Base\Models\WpComposite;

class TranslationStateFieldGroup
{
    public static function register()
    {
        if (!function_exists('acf_add_local_field_group')) {
            return;
        }

        $group = new ACFGroup('group_5940debee7ae2');
        $group->setTitle('Translation State')
            ->setLocation(new ACFLocation('post_type', ACFLocation::OPERATOR_EQUALS, WpComposite::POST_TYPE))
            ->setMenuOrder(0)
            ->setPosition(ACFGroup::POSITION_SIDE)
            ->setStyle(ACFGroup::STYLE_DEFAULT)
            ->setLabelPlacement(ACFGroup::LABEL_PLACEMENT_TOP)
            ->setInstructionPlacement(ACFGroup::INSTRUCTION_PLACEMENT_LABEL)
            ->setActive(true);

        $group->addField(self::getTranslationStateField())
            ->addField(self::getTranslationDeadlineField());

        $filteredGroup = apply_filters(sprintf('willow/acf/group=%s', $group->getKey()), $group);

        acf_add_local_field_group($filteredGroup->toArray());
    }

    public static function getTranslationStateField(): ACFField
    {
        $field = new SelectField('field_5940df2d4eff9');
        $field->setName('translation_state')
            ->addChoice('ready', 'Ready For Translation')
            ->addChoice('progress', 'In Progress')
            ->addChoice('translated', 'Translated')
            ->setAllowNull(true)
            ->setReturnFormat(ACFField::RETURN_VALUE);

        return apply_filters(sprintf('willow/acf/field=%s', $field->getKey()), $field);
    }

    public static function getTranslationDeadlineField(): ACFField
    {
        $field = new DatePickerField('field_59885bce3d421');
        $field->setLabel('Translation deadline')
            ->setName('translation_deadline')
            ->setDisplayFormat('F j, Y')
            ->setReturnFormat(ACFField::RETURN_DATE)
            ->setFirstDay(1);


        return apply_filters(sprintf('willow/acf/field=%s', $field->getKey()), $field);
    }
}

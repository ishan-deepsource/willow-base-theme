<?php

namespace Bonnier\Willow\Base\Models\ACF\User;

use Bonnier\Willow\Base\Models\ACF\ACFField;
use Bonnier\Willow\Base\Models\ACF\ACFGroup;
use Bonnier\Willow\Base\Models\ACF\Fields\DatePickerField;
use Bonnier\Willow\Base\Models\ACF\Fields\ImageField;
use Bonnier\Willow\Base\Models\ACF\Fields\TextField;
use Bonnier\Willow\Base\Models\ACF\Fields\TrueFalseField;
use Bonnier\Willow\Base\Models\ACF\Properties\ACFLocation;

class UserFieldGroup
{
    public static function register(): void
    {
        if (!function_exists('acf_add_local_field_group')) {
            return;
        }
        $group = new ACFGroup('group_5ad5e82740549');
        $group->setTitle('Willow User Profile')
            ->setLocation(new ACFLocation('user_form', ACFLocation::OPERATOR_EQUALS, 'edit'))
            ->setMenuOrder(0)
            ->setPosition(ACFGroup::POSITION_NORMAL)
            ->setStyle(ACFGroup::STYLE_DEFAULT)
            ->setLabelPlacement(ACFGroup::LABEL_PLACEMENT_TOP)
            ->setInstructionPlacement(ACFGroup::INSTRUCTION_PLACEMENT_LABEL)
            ->setActive(true);

        $group->addField(self::getAvatarField());
        $group->addField(self::getTitleField());
        $group->addField(self::getBirthdayField());
        $group->addField(self::getPublicField());

        $filteredGroup = apply_filters(sprintf('willow/acf/group=%s', $group->getKey()), $group);

        acf_add_local_field_group($filteredGroup->toArray());
    }

    public static function getAvatarField(): ACFField
    {
        $field = new ImageField('field_5ad5e867977d3');
        $field->setLabel('Profile Picture')
            ->setName('user_avatar')
            ->setReturnFormat(ACFField::RETURN_ARRAY)
            ->setPreviewSize(ImageField::PREVIEW_MEDIUM);

        return apply_filters(sprintf('willow/acf/field=%s', $field->getKey()), $field);
    }

    public static function getTitleField(): ACFField
    {
        $field = new TextField('field_5af17b5df8440');
        $field->setLabel('Title')
            ->setName('user_title');

        return apply_filters(sprintf('willow/acf/field=%s', $field->getKey()), $field);
    }

    public static function getBirthdayField(): ACFField
    {
        $field = new DatePickerField('field_5e6e0ca2219b4');
        $field->setLabel('Birthday')
            ->setName('birthday')
            ->setDisplayFormat('d/m/Y')
            ->setReturnFormat('d/m/Y')
            ->setFirstDay(1);

        return apply_filters(sprintf('willow/acf/field=%s', $field->getKey()), $field);
    }

    public static function getPublicField(): ACFField
    {
        $field = new TrueFalseField('field_5e6e0cdd219b5');
        $field->setName('Public')
            ->setLabel('public')
            ->setInstructions('Should this author have an author page and be on sitemaps in the frontend?');

        return apply_filters(sprintf('willow/acf/field=%s', $field->getKey()), $field);
    }
}

<?php

namespace Bonnier\Willow\Base\Models\ACF\Attachment;

use Bonnier\Willow\Base\Models\ACF\ACFField;
use Bonnier\Willow\Base\Models\ACF\ACFGroup;
use Bonnier\Willow\Base\Models\ACF\Fields\MarkdownField;
use Bonnier\Willow\Base\Models\ACF\Properties\ACFLocation;

class AttachmentFieldGroup
{
    public const CAPTION_FIELD_KEY =  'field_5c51b211deb49';

    public static function register()
    {
        if (!function_exists('acf_add_local_field_group')) {
            return;
        }
        $group = new ACFGroup('group_5c51a64a46db2');
        $group->setTitle('Attachments')
            ->setLocation(new ACFLocation('attachment', ACFLocation::OPERATOR_EQUALS, 'all'))
            ->setMenuOrder(0)
            ->setPosition(ACFGroup::POSITION_NORMAL)
            ->setStyle(ACFGroup::STYLE_DEFAULT)
            ->setInstructionPlacement(ACFGroup::INSTRUCTION_PLACEMENT_LABEL)
            ->setActive(true);

        $group->addField(self::getCaptionField());

        $filteredGroup = apply_filters(sprintf('willow/acf/group=%s', $group->getKey()), $group);

        acf_add_local_field_group($filteredGroup->toArray());
    }

    public static function getCaptionField(): ACFField
    {
        $caption = new MarkdownField(static::CAPTION_FIELD_KEY);
        $caption->setLabel('Caption')
            ->setName('caption')
            ->setMdeConfig(MarkdownField::CONFIG_SIMPLE);

        return apply_filters(sprintf('willow/acf/field=%s', $caption->getKey()), $caption);
    }
}

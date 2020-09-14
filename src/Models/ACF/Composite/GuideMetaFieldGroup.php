<?php

namespace Bonnier\Willow\Base\Models\ACF\Composite;

use Bonnier\Willow\Base\Models\ACF\ACFGroup;
use Bonnier\Willow\Base\Models\ACF\Fields\TextField;
use Bonnier\Willow\Base\Models\ACF\Properties\ACFLocation;

class GuideMetaFieldGroup {

    public const POST_TYPE = 'guide';
    public const DIFFICULTY_FIELD_NAME = 'difficulty';
    public const TIME_REQUIRED_FIELD_NAME = 'time_required';
    public const PRICE_FIELD_NAME = 'price';

    public static function register()
    {
        if ( ! function_exists('acf_add_local_field_group')) {
            return;
        }
        $group = new ACFGroup('group_5f560d4039a3f');
        $group->setTitle('Guide meta')
              ->setLocation(new ACFLocation('post_template', ACFLocation::OPERATOR_EQUALS, self::POST_TYPE))
              ->setMenuOrder(6)
              ->setPosition(ACFGroup::POSITION_AFTER_TITLE)
              ->setStyle(ACFGroup::STYLE_DEFAULT)
              ->setHideOnScreen([
                  'slug',
                  'author',
                  'categories',
              ]);
        $timeText = new TextField('field_5f560d8c95208');
        $timeText->setName(self::TIME_REQUIRED_FIELD_NAME)
                 ->setLabel('Time required')
                 ->setRequired(true);

        $materialPriceText = new TextField('field_5f560ec795209');
        $materialPriceText->setName(self::PRICE_FIELD_NAME)
                          ->setLabel('Price')
                          ->setRequired(true);

        $group->addField($timeText);
        $group->addField($materialPriceText);

        acf_add_local_field_group($group->toArray());
    }


}

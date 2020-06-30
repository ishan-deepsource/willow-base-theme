<?php

namespace Bonnier\Willow\Base\Models\ACF\Composite;

use Bonnier\Willow\Base\Models\ACF\ACFField;
use Bonnier\Willow\Base\Models\ACF\ACFGroup;
use Bonnier\Willow\Base\Models\ACF\Fields\TaxonomyField;
use Bonnier\Willow\Base\Models\ACF\Properties\ACFLocation;
use Bonnier\Willow\Base\Models\WpComposite;
use Illuminate\Support\Collection;

class TaxonomyFieldGroup
{
    public static function register(Collection $customTaxonomies)
    {
        if (function_exists('acf_add_local_field_group')) {
            $group = new ACFGroup('group_5937df68c8ff8');
            $group->setTitle('Taxonomy')
                ->setLocation(new ACFLocation('post_type', ACFLocation::OPERATOR_EQUALS, WpComposite::POST_TYPE))
                ->setMenuOrder(1)
                ->setPosition(ACFGroup::POSITION_SIDE)
                ->setStyle(ACFGroup::STYLE_DEFAULT)
                ->setLabelPlacement(ACFGroup::LABEL_PLACEMENT_TOP)
                ->setInstructionPlacement(ACFGroup::INSTRUCTION_PLACEMENT_LABEL)
                ->setActive(true);

            $customTaxonomies->each(function ($customTaxonomy) use ($group) {
                $taxonomy = new TaxonomyField(sprintf('field_%s', hash('md5', $customTaxonomy->machine_name)));
                $taxonomy->setLabel($customTaxonomy->name)
                    ->setName($customTaxonomy->machine_name)
                    ->setTaxonomy($customTaxonomy->machine_name)
                    ->setAllowNull(true)
                    ->setSaveTerms(true)
                    ->setReturnFormat(ACFField::RETURN_OBJECT);
                if (isset($customTaxonomy->multi_select) && $customTaxonomy->multi_select) {
                    $taxonomy->setFieldType('multi_select')
                        ->setMultiple(true);
                } else {
                    $taxonomy->setFieldType('select')
                        ->setMultiple(false);
                }

                $group->addField($taxonomy);
            });

            $filteredGroup = apply_filters(sprintf('willow/acf/group=%s', $group->getKey()), $group);

            acf_add_local_field_group($filteredGroup->toArray());
        }
    }
}

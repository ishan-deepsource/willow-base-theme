<?php

namespace Bonnier\Willow\Base\Adapters\Wp\Pages\Contents\Types;

use Bonnier\Willow\Base\Adapters\Wp\Composites\CompositeAdapter;
use Bonnier\Willow\Base\Adapters\Wp\Pages\Contents\AbstractContentAdapter;
use Bonnier\Willow\Base\Models\Base\Composites\Composite;
use Bonnier\Willow\Base\Models\Contracts\Composites\CompositeContract;
use Bonnier\Willow\Base\Models\Contracts\Pages\Contents\Types\QuoteTeaserContract;
use Bonnier\Willow\Base\Repositories\WpModelRepository;

/**
 * Class QuoteTeaserAdapter
 * @package Bonnier\Willow\Base\Adapters\Wp\Pages\Contents\Types
 * @property array $acfArray
 */
class QuoteTeaserAdapter extends AbstractContentAdapter implements QuoteTeaserContract
{

    public function getQuote(): string
    {
        return array_get($this->acfArray, 'quote') ?: '';
    }

    public function getAuthor(): ?string
    {
        return array_get($this->acfArray, 'author');
    }

    public function getLinkLabel(): ?string
    {
        if (array_get($this->acfArray, 'link_type') === 'external') {
            return array_get($this->acfArray, 'link_label');
        }
        return null;
    }

    public function getLink(): ?string
    {
        if (array_get($this->acfArray, 'link_type') === 'external') {
            return array_get($this->acfArray, 'link');
        }
        return null;
    }

    public function getComposite(): ?CompositeContract
    {
        if (array_get($this->acfArray, 'link_type') === 'composite') {
            $data = array_get($this->acfArray, 'composite_content');
            if ($data && $data[0]->ID) {
                $composite = WpModelRepository::instance()->getPost($data[0]->ID);
                return new Composite(new CompositeAdapter($composite));
            }
        }
        return null;
    }
}

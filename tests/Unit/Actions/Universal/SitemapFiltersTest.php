<?php

namespace Bonnier\Willow\Base\Tests\Unit\Actions\Universal;

use Bonnier\Willow\Base\Actions\Universal\SitemapFilters;
use Bonnier\Willow\Base\Helpers\Utils;
use Bonnier\Willow\Base\Tests\Unit\ClassTestCase;

class SitemapFiltersTest extends ClassTestCase
{

    public function testAllowedPostTypesRemovesPost()
    {
        $postTypes = [
            'post',
            'page',
            'contenthub_composite'
        ];
        $this->assertEquals(['page', 'contenthub_composite'], SitemapFilters::allowedPostTypes($postTypes));
    }

    public function testItRemovesApiSubdomain()
    {
        $permalink = 'https://api.example.test/category/subcategory/article-title';

        $this->assertEquals(
            'https://example.test/category/subcategory/article-title',
            Utils::removeApiSubdomain($permalink)
        );
    }

    public function testItRemovesNativeApiSubdomain()
    {
        $permalink = 'https://native-api.example.test/category/subcategory/article-title';

        $this->assertEquals(
            'https://example.test/category/subcategory/article-title',
            Utils::removeApiSubdomain($permalink)
        );
    }

    public function testItRemovesAdminSubdomain()
    {
        $permalink = 'https://admin.example.test/category/subcategory/article-title';

        $this->assertEquals(
            'https://example.test/category/subcategory/article-title',
            Utils::removeApiSubdomain($permalink)
        );
    }

    public function testItLeavesValidPermalinkUnchanged()
    {
        $permalink = 'https://example.test/category/subcategory/api-article-title';

        $this->assertEquals(
            'https://example.test/category/subcategory/api-article-title',
            Utils::removeApiSubdomain($permalink)
        );
    }
}

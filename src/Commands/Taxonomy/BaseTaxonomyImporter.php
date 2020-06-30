<?php

namespace Bonnier\Willow\Base\Commands\Taxonomy;

use Bonnier\Willow\Base\Commands\BaseCmd;
use Bonnier\Willow\Base\Commands\Taxonomy\Helpers\WpTerm;
use Bonnier\Willow\Base\Helpers\TermImportHelper;
use WP_CLI;

/**
 * Class BaseTaxonomyImporter
 */
class BaseTaxonomyImporter extends BaseCmd
{
    protected $taxonomy;
    protected $termImporter;
    protected $getTermCallback;
    protected $termImportHelper;

    protected function triggerSync($taxononmy, $getTermCallback)
    {
        $this->taxonomy = $taxononmy;
        $this->getTermCallback = $getTermCallback;
        $this->termImportHelper = new TermImportHelper($taxononmy);
        $this->syncTerms();
    }

    protected function triggerImport($taxonomy, $getTermCallback)
    {
        $this->taxonomy = $taxonomy;
        $this->termImporter = new TermImportHelper($taxonomy);
        $this->getTermCallback = $getTermCallback;
        $this->mapTerms($this->getSite(), function ($externalTag) {
            $this->termImporter->importTermAndLinkTranslations($externalTag);
        });
    }

    protected function mapTerms($site, $callable)
    {
        $termQuery = call_user_func($this->getTermCallback, $site->brand->id);

        while (!is_null($termQuery)) {
            WP_CLI::line("Beginning import of page: " . $termQuery->meta->pagination->current_page);
            collect($termQuery->data)->each($callable);
            if (isset($termQuery->meta->pagination->links->next)) {
                $nextPage = $termQuery->meta->pagination->current_page + 1;
                $termQuery = call_user_func($this->getTermCallback, $site->brand->id, $nextPage);
                continue;
            }
            $termQuery = null;
        }
    }

    public function clean_terms($taxononmy, $removeEmpty = false)
    {
        collect(get_terms([
            'taxonomy'   => $taxononmy,
            'hide_empty' => false,
            'number'     => 0
        ]))->filter(function (\WP_Term $term) use ($removeEmpty) {
            if (!get_term_meta($term->term_id, 'content_hub_id', true) || $term->count === 0 && $removeEmpty) {
                return true;
            }
            return false;
        })->pipe(function ($terms) {
            WP_CLI::line('A total of: ' . $terms->count() . ' will be removed');
            return $terms;
        })->each(function (\WP_Term $term) use ($taxononmy) {
            wp_delete_term($term->term_id, $taxononmy);
            WP_CLI::line('Removed term: ' . $term->term_id);
        });

        WP_CLI::success('Done cleaning ' . $taxononmy);
    }

    private function syncTerms()
    {
        collect(get_terms([
            'taxonomy'   => $this->taxonomy,
            'hide_empty' => false,
            'number'     => 0
        ]))->reject(function (\WP_Term $term) {
            return str_contains(strtolower($term->name), 'uncategorized');
        })->reject(function (\WP_Term $term) {
            $externalTerm = call_user_func($this->getTermCallback, WpTerm::content_hub_id($term->term_id));
            if ($externalTerm) {
                WP_CLI::line(sprintf('Term: %s from taxonomy: %s exist skipping', $term->name, $this->taxonomy));
                return true;
            }
            WP_CLI::warning(sprintf(
                'Term: %s from taxonomy: %s is missing form sm will remove',
                $term->name,
                $this->taxonomy
            ));
            WP_CLI::warning(WpTerm::content_hub_id($term->term_id));
            return false;
        })->pipe(function ($terms) {
            WP_CLI::warning('A total of: ' . $terms->count() . ' will be removed');
            return $terms;
        })->each(function (\WP_Term $term) {
            if ($this->termImportHelper->deleteTerm($term)) {
                WP_CLI::line(sprintf('Removed term: %s from taxonomy: %s', $term->name, $this->taxonomy));
            } else {
                WP_CLI::warning(sprintf('Failed removing term: %s from taxonomy: %s', $term->name, $this->taxonomy));
            }
        });
    }
}

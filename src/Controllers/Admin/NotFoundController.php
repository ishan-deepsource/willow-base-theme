<?php

namespace Bonnier\Willow\Base\Controllers\Admin;

use Bonnier\Willow\Base\Repositories\NotFoundRepository;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Request;

class NotFoundController extends \WP_List_Table
{
    /** @var NotFoundRepository */
    private $notFoundRepository;
    /** @var Request */
    private $request;

    private $notices = [];

    private $locales;

    public function __construct(NotFoundRepository $notFoundRepository, Request $request)
    {
        parent::__construct([
            'singular' => 'notfound',
            'plural' => 'notfounds',
        ]);
        $this->notFoundRepository = $notFoundRepository;
        $this->request = $request;
    }

    public static function loadNotFoundTable()
    {
        $arguments = [
            'label' => 'URLs per page',
            'default' => 20,
            'option' => 'notfound_urls_per_page',
        ];

        add_screen_option('per_page', $arguments);
    }

    public function displayNotFoundTable()
    {
        $this->prepare_items();

        $view = sprintf('%s/Views/notFoundTable.php', rtrim(dirname(dirname(__DIR__)), '/'));

        include_once($view);
    }

    public function displaySearch()
    {
        $this->search_box('Find Not Found Url', 'bonnier-willow-base-notfound-find');
    }

    /**
     * @return array
     */
    public function getNotices()
    {
        return $this->notices;
    }

    /**
     * @return array
     */
    public function get_columns()
    {
        return [
            'notfound_url' => 'URL',
            'notfound_locale' => 'Locale',
            'notfound_hits' => 'Hits',
            'notfound_updated_at' => 'Last hit',
            'id' => 'ID',
        ];
    }

    /**
     * @param object $item
     * @param string $column_name
     * @return mixed
     */
    public function column_default($item, $column_name)
    {
        if (Str::startsWith($column_name, 'notfound_')) {
            $column_name = Str::after($column_name, 'notfound_');
        }
        return $item[$column_name];
    }

    public function column_notfound_updated_at($item)
    {
        return (new \DateTime($item['updated_at']))->format('H:i d-m-Y');
    }

    /**
     * @return bool|false|mixed|string
     */
    public function current_action()
    {
        $params = $this->request->query;
        if ($params->get('filter_action')) {
            return false;
        }

        if (($action = $params->get('action')) && $action != -1) {
            return $action;
        }

        if (($action = $params->get('action2')) && $action != -1) {
            return $action;
        }

        return false;
    }

    /**
     * @return array
     */
    protected function get_sortable_columns()
    {
        return [
            'id' => ['id', true],
            'notfound_url' => 'url',
            'notfound_locale' => 'locale',
            'notfound_hits' => 'hits',
            'notfound_updated_at' => 'updated_at',
        ];
    }

    public function no_items()
    {
        echo "No 'Not Found'-URLs found.";
    }

    public function prepare_items()
    {
        // Check if a search was performed
        $redirectSearchKey = wp_unslash(trim($this->request->get('s'))) ?: null;

        // Column headers
        $this->_column_headers = $this->get_column_info();

        // Pagination
        $itemsPerPage = $this->get_items_per_page('notfound_urls_per_page');
        $tablePage = $this->get_pagenum();

        $offset = ($tablePage - 1) * $itemsPerPage;

        $filters = [];
        $locale = wp_unslash(trim($this->request->get('notfound_locale')));
        if ($locale) {
            $filters['locale'] = $locale;
        }

        // Fetch table data
        $this->items = $this->fetchTableData($redirectSearchKey, $offset, $itemsPerPage, $filters);
        $this->locales = $this->fetchLocales($filters);

        try {
            // Set pagination arguments
            $total = $this->notFoundRepository->countRows($redirectSearchKey);
        } catch (\Exception $exception) {
            $total = 0;
        }
        $this->set_pagination_args([
            'total_items' => $total,
            'per_page' => $itemsPerPage,
            'total_pages' => ceil($total / $itemsPerPage),
        ]);
    }

    /**
     * @param string|null $searchKey
     * @param int $offset
     * @param int $perPage
     * @param array $filters
     * @return array
     * @throws \Exception
     */
    private function fetchTableData(?string $searchKey = null, int $offset = 0, int $perPage = 20, array $filters = [])
    {
        $orderby = esc_sql($this->request->get('orderby', 'id'));
        $order = esc_sql($this->request->get('order', 'DESC'));

        return $this->notFoundRepository->find($searchKey, $orderby, $order, $perPage, $offset, $filters);
    }

    private function fetchLocales(array $filters = []): array
    {
        $query = $this->notFoundRepository->query()->select('locale, COUNT(locale) AS amount');
        $query->groupBy('locale');
        return $this->notFoundRepository->results($query) ?: [];
    }
}

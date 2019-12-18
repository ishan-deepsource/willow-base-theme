<?php

namespace Bonnier\Willow\Base\Controllers\Admin;

use Bonnier\Willow\Base\Repositories\NotFoundRepository;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Request;

class NotFoundListController extends \WP_List_Table
{
    const DELETE_NONCE_KEY = 'delete_notfound_nonce';
    const IGNORE_NONCE_KEY = 'ignore_notfound_nonce';

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
            'cb' => '<input type="checkbox" />',
            'notfound_url' => 'URL',
            'notfound_locale' => 'Locale',
            'notfound_hits' => 'Hits',
            'notfound_updated_at' => 'Last hit',
            'id' => 'ID',
        ];
    }

    protected function column_cb($item)
    {
        return sprintf(
            '<label class="screen-reader-text" for="notfound_%s">Select %s</label>
            <input type="checkbox" name="notfounds[]" id="notfound_%s" value="%s" />',
            $item['id'],
            $item['id'],
            $item['id'],
            $item['id']
        );
    }

    public function get_bulk_actions()
    {
        return [
            'bulk-delete' => 'Delete entries',
            'bulk-ignore' => 'Ignore entries'
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

    public function column_notfound_url($item)
    {
        $pageUrl = admin_url('admin.php');

        $deleteNotfoundArgs = [
            'page' => $this->request->get('page'),
            'action' => 'delete_notfound',
            'notfound_id' => absint($item['id']),
            '_wpnonce' => wp_create_nonce(self::DELETE_NONCE_KEY),
        ];

        $ignoreNotfoundArgs = [
            'page' => $this->request->get('page'),
            'action' => 'ignore_notfound',
            'notfound_id' => absint($item['id']),
            '_wpnonce' => wp_create_nonce(self::IGNORE_NONCE_KEY),
        ];

        $createRedirectArgs = [
            'page' => 'add-redirect',
            'from' => urlencode($item['url']),
            'language' => urlencode($item['locale'])
        ];

        $deleteNotfoundLink = esc_url(add_query_arg($deleteNotfoundArgs, $pageUrl));
        $ignoreNotfoundLink = esc_url(add_query_arg($ignoreNotfoundArgs, $pageUrl));
        $createRedirectLink = esc_url(add_query_arg($createRedirectArgs, $pageUrl));

        $actions = [
            'ignore' => sprintf(
                '<a href="%s">%s</a>',
                $ignoreNotfoundLink,
                'Ignore entry'
            ),
            'trash' => sprintf(
                '<a href="%s" onclick="return confirm(\'%s\')">%s</a>',
                $deleteNotfoundLink,
                'Are you sure, you want to delete this entry?',
                'Delete entry'
            ),
            'new' => sprintf(
                '<a href="%s">%s</a>',
                $createRedirectLink,
                'Create redirect'
            )
        ];

        return sprintf(
            '<strong>%s</strong>%s',
            $item['url'],
            $this->row_actions($actions)
        );
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

        // Check and process any actions such as bulk actions.
        $this->handleTableActions();

        // Pagination
        $itemsPerPage = $this->get_items_per_page('notfound_urls_per_page');
        $tablePage = $this->get_pagenum();

        $offset = ($tablePage - 1) * $itemsPerPage;

        $filters = ['ignore_entry' => 0];
        $locale = wp_unslash(trim($this->request->get('notfound_locale')));
        if ($locale) {
            $filters['locale'] = $locale;
        }
        $ignored = wp_unslash(trim($this->request->get('notfound_ignored')));
        if ($ignored) {
            $filters['ignore_entry'] = 1;
        }

        // Fetch table data
        $this->items = $this->fetchTableData($redirectSearchKey, $offset, $itemsPerPage, $filters);
        $this->locales = $this->fetchLocales();

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

    private function fetchLocales(): array
    {
        $query = $this->notFoundRepository->query()->select('locale, COUNT(locale) AS amount');
        $query->groupBy('locale');
        return $this->notFoundRepository->results($query) ?: [];
    }

    private function handleTableActions()
    {
        $tableAction = $this->current_action();

        if ('delete_notfound' === $tableAction) {
            $this->deleteEntry();
        }

        if ('ignore_notfound' === $tableAction) {
            $this->ignoreEntry();
        }

        if ($tableAction === 'bulk-delete') {
            $this->bulkDeleteEntries();
        }

        if ($tableAction === 'bulk-ignore') {
            $this->bulkIgnoreEntries();
        }
    }

    private function deleteEntry()
    {
        $nonce = wp_unslash($this->request->get('_wpnonce'));
        if (!wp_verify_nonce($nonce, self::DELETE_NONCE_KEY)) {
            $this->invalidNonceRedirect();
        } else {
            if (($notfoundID = $this->request->get('notfound_id')) &&
                $notFound = $this->notFoundRepository->getNotFoundById($notfoundID)
            ) {
                try {
                    $this->notFoundRepository->delete($notFound);
                    $this->addNotice('The entry was deleted!', 'success');
                } catch (\Exception $exception) {
                    $this->addNotice(
                        sprintf('An error occured while deleting entry (%s)', $exception->getMessage())
                    );
                }
            }
        }
    }

    private function bulkDeleteEntries()
    {
        $nonce = wp_unslash($this->request->get('_wpnonce'));
        if (!wp_verify_nonce($nonce, 'bulk-' . $this->_args['plural'])) {
            $this->invalidNonceRedirect();
        } elseif ($notfounds = $this->request->get('notfounds')) {
            $this->notFoundRepository->deleteMultipleByIDs($notfounds);
            $this->addNotice(sprintf('%s entries(s) was deleted!', count($notfounds)), 'success');
        }
    }

    private function ignoreEntry()
    {
        $nonce = wp_unslash($this->request->get('_wpnonce'));
        if (!wp_verify_nonce($nonce, self::IGNORE_NONCE_KEY)) {
            $this->invalidNonceRedirect();
        } else {
            if (($notfoundID = $this->request->get('notfound_id')) &&
                $notFound = $this->notFoundRepository->getNotFoundById($notfoundID)
            ) {
                try {
                    $notFound->setIgnored(true);
                    $this->notFoundRepository->save($notFound);
                    $this->addNotice('The entry was ignored!', 'success');
                } catch (\Exception $exception) {
                    $this->addNotice(
                        sprintf('An error occured while ignoring entry (%s)', $exception->getMessage())
                    );
                }
            }
        }
    }

    private function bulkIgnoreEntries()
    {
        $nonce = wp_unslash($this->request->get('_wpnonce'));
        if (!wp_verify_nonce($nonce, 'bulk-' . $this->_args['plural'])) {
            $this->invalidNonceRedirect();
        } elseif ($notfounds = $this->request->get('notfounds')) {
            $this->notFoundRepository->ignoreMultipleByIDs($notfounds);
            $this->addNotice(sprintf('%s entries(s) was ignored!', count($notfounds)), 'success');
        }
    }

    private function invalidNonceRedirect()
    {
        wp_die('Invalid Nonce', 'Error', [
            'response' => 403,
            'back_link' => esc_url(
                add_query_arg([
                    'page' => wp_unslash($this->request->get('page'))
                ], admin_url('admin.php'))
            ),
        ]);
    }

    /**
     * @param string $notice
     * @param string $type
     */
    private function addNotice(string $notice, string $type = 'error')
    {
        $this->notices[] = [$type => $notice];
    }
}

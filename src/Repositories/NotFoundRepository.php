<?php

namespace Bonnier\Willow\Base\Repositories;

use Bonnier\Willow\Base\Database\DB;
use Bonnier\Willow\Base\Database\Migrations\Migrate;
use Bonnier\Willow\Base\Database\Query;
use Bonnier\Willow\Base\Models\Admin\NotFound;
use Bonnier\Willow\Base\Notifications\NotFoundRegistered;
use Illuminate\Support\Collection;

class NotFoundRepository
{
    private static $instance;

    protected $database;
    protected $tableName;

    public function __construct(DB $database)
    {
        $this->tableName = Migrate::NOT_FOUND_TABLE;
        $this->database = $database;
        $this->database->setTable($this->tableName);
    }

    public static function instance(): NotFoundRepository
    {
        if (!self::$instance) {
            self::$instance = new self(new DB());
        }

        return self::$instance;
    }

    public function register(string $path, string $locale)
    {
        $query = $this->query()
            ->select('*')
            ->where(['url_hash', hash('md5', $path)])
            ->andWhere(['locale', $locale]);
        $notFound = $this->getNotFound($query);
        if ($notFound) {
            $notFound->setHits($notFound->getHits() + 1);
        } else {
            $notFound = NotFound::createFromArray([
                'url' => $path,
                'locale' => $locale,
                'hits' => 1
            ]);
        }
        $this->save($notFound);
        NotFoundRegistered::notify($notFound);
    }

    public function query(): Query {
        return $this->database->query();
    }

    public function results(Query $query): ?array {
        try {
            return $this->database->getResults($query);
        } catch (\Exception $e) {
            return null;
        }
    }

    public function getNotFound(Query $query): ?NotFound
    {
        if ($results = $this->results($query)) {
            return $this->mapNotFounds($results)->first();
        }
        return null;
    }

    public function getNotFounds(Query $query): ?Collection
    {
        if ($results = $this->results($query)) {
            return $this->mapNotFounds($results);
        }
        return null;
    }

    /**
     * @param string|null $searchQuery
     * @param string|null $orderBy
     * @param string|null $order
     * @param int|null $perPage
     * @param int|null $offset
     * @param array|null $filters
     * @return array
     * @throws \Exception
     */
    public function find(
        ?string $searchQuery = null,
        ?string $orderBy = null,
        ?string $order = null,
        ?int $perPage = null,
        ?int $offset = null,
        ?array $filters = []
    ) {
        $query = $this->database->query()->select('*');
        $andWhere = false;
        if ($searchQuery) {
            $query->where(['url', '%' . $searchQuery . '%', 'LIKE']);
            $andWhere = true;
        }
        if (!empty($filters)) {
            foreach ($filters as $column => $value) {
                if ($andWhere) {
                    $query->andWhere([$column, $value, '=']);
                } else {
                    $query->where([$column, $value, '=']);
                    $andWhere = true;
                }
            }
        }
        if ($orderBy) {
            $query->orderBy($orderBy, $order);
        }
        if (!is_null($perPage)) {
            $query->limit($perPage);
        }
        if (!is_null($offset)) {
            $query->offset($offset);
        }

        return $this->database->getResults($query);
    }

    /**
     * @param string|null $searchKey
     * @return int
     * @throws \Exception
     */
    public function countRows(?string $searchKey = null): int
    {
        $query = $this->database->query()->select('COUNT(id)');
        if ($searchKey) {
            $query->where(['url', '%' . $searchKey . '%', 'LIKE']);
        }

        return intval($this->database->getVar($query));
    }

    public function save(NotFound $notFound, bool $updateOnDuplicate = false)
    {
        $data = $notFound->toArray();
        unset($data['id']);

        if ($redirectId = $notFound->getID()) {
            $this->database->update($redirectId, $data);
        } else {
            if ($updateOnDuplicate) {
                $notFound->setID($this->database->insertOrUpdate($data));
            } else {
                $notFound->setID($this->database->insert($data));
            }
        }

        return $notFound;
    }

    /**
     * @param array $notFounds
     * @return Collection
     */
    private function mapNotFounds(array $notFounds): Collection
    {
        return collect($notFounds)->map(function (array $data) {
            return NotFound::createFromArray($data);
        });
    }
}

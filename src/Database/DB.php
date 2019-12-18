<?php

namespace Bonnier\Willow\Base\Database;

use Bonnier\WP\Redirect\Database\Exceptions\DuplicateEntryException;
use Illuminate\Support\Str;

class DB
{
    /** @var \wpdb */
    private $wpdb;
    /** @var string */
    private $table;

    public function __construct()
    {
        global $wpdb;
        $this->wpdb = $wpdb;
    }

    /**
     * @param string $tableName
     * @return string
     */
    public function setTable(string $tableName)
    {
        return $this->table = Str::start($tableName, $this->wpdb->prefix);
    }

    /**
     * @param int $rowID
     * @return array|null
     */
    public function findById(int $rowID): ?array
    {
        return $this->wpdb->get_row(
            $this->wpdb->prepare("SELECT * FROM $this->table WHERE id = %d", $rowID),
            ARRAY_A
        );
    }

    /**
     * @return Query
     */
    public function query(): Query
    {
        return new Query($this->table);
    }

    /**
     * @param Query $query
     * @return array
     * @throws \Exception
     */
    public function getResults(Query $query): array
    {
        return $this->wpdb->get_results($query->getSQL(), ARRAY_A);
    }

    /**
     * @param Query $query
     * @return string|null
     * @throws \Exception
     */
    public function getVar(Query $query)
    {
        return $this->wpdb->get_var($query->getSQL());
    }

    /**
     * @param array $data
     * @return int
     * @throws DuplicateEntryException
     * @throws \Exception
    */
    public function insert(array $data)
    {
        $this->disableErrorOutput();
        if (!$this->wpdb->insert($this->table, $data, $this->getDataFormat($data))) {
            $error = $this->wpdb->last_error;
            if (Str::startsWith($error, 'Duplicate entry ')) {
                $uniqueKey = Str::after($error, ' for key ');
                $exception = new DuplicateEntryException(
                    sprintf('Cannot create entry, due to key constraint %s', $uniqueKey)
                );
                $exception->setData($data);
                throw $exception;
            } else {
                throw new \Exception(sprintf('Unable to insert row in `%s`! (%s)', $this->table, $error));
            }
        }
        return $this->wpdb->insert_id;
    }

    /**
     * @param array $data
     * @return int
     * @throws \Exception
     */
    public function insertOrUpdate(array $data)
    {
        $this->disableErrorOutput();
        try {
            return $this->insert($data);
        } catch (DuplicateEntryException $exception) {
            if (!$this->wpdb->replace($this->table, $data, $this->getDataFormat($data))) {
                $error = $this->wpdb->last_error;
                throw new \Exception(sprintf('Unable to replace row in `%s`! (%s)', $this->table, $error));
            }
        }
        return $this->wpdb->insert_id;
    }

    /**
     * @param int $rowId
     * @param array $data
     *
     * @return bool
     * @throws \Exception
     */
    public function update(int $rowId, array $data)
    {
        $this->disableErrorOutput();
        if ($this->wpdb->update(
            $this->table,
            $data,
            ['id' => $rowId],
            self::getDataFormat($data),
            ['%d']
        ) === false) {
            $error = $this->wpdb->last_error;
            if (Str::startsWith($error, 'Duplicate entry ')) {
                $uniqueKey = Str::after($error, ' for key ');
                $exception = new DuplicateEntryException(
                    sprintf('Cannot update row with ID: %s, due to key constraint %s', $rowId, $uniqueKey)
                );
                $exception->setData($data);
                throw $exception;
            } else {
                throw new \Exception(
                    sprintf('Unable to update row in `%s` with ID: %s! (%s)', $this->table, $rowId, $error)
                );
            }
        }

        return true;
    }

    public function updateMultipleByIDs(array $rowIDs, array $data)
    {
        $this->disableErrorOutput();
        $query = sprintf("UPDATE %s SET ", $this->table);
        $escaped = [];

        $i = 0;
        foreach( $data as $column => $value ) {
            $format = is_int($value) ? '%d' : '%s';
            $escaped[] = esc_sql( $column ) . " = " . $this->wpdb->prepare( $format, $value );
            $i++;
        }

        $query .= implode(', ', $escaped);
        $query .= ' WHERE id IN (';

        $escaped = array();

        foreach ($rowIDs as $id) {
            $escaped[] = $this->wpdb->prepare('%d', $id);
        }

        $query .= implode( $escaped, ', ' ) . ') LIMIT ' . count($rowIDs);

        return $this->wpdb->query( $query );
    }

    /**
     * @param int $rowID
     * @return bool
     * @throws \Exception
     */
    public function delete(int $rowID)
    {
        $this->disableErrorOutput();
        if ($this->wpdb->delete($this->table, ['id' => $rowID], ['%d']) === false) {
            throw new \Exception(
                sprintf(
                    'Could not delete row with ID %s from table \'%s\' (%s)',
                    $rowID,
                    $this->table,
                    $this->wpdb->last_error
                )
            );
        }

        return true;
    }

    /**
     * @param array $rowIDs
     * @return bool
     * @throws \Exception
     */
    public function deleteMultiple(array $rowIDs)
    {
        $this->disableErrorOutput();
        $placeholder = implode(',', array_fill(0, count($rowIDs), '%d'));
        $result = $this->wpdb->query(
            $this->wpdb->prepare(
                "DELETE FROM $this->table WHERE id IN ($placeholder);",
                $rowIDs
            )
        );
        if ($result === false) {
            throw new \Exception(
                sprintf('Could not delete rows! (%s)', $this->wpdb->last_error)
            );
        }

        return true;
    }

    /**
     * @param array $data
     * @return array
     */
    private function getDataFormat(array $data)
    {
        $format = [];
        foreach ($data as $item) {
            if (is_int($item)) {
                $format[] = '%d';
            } else {
                $format[] = '%s';
            }
        }
        return $format;
    }

    private function disableErrorOutput()
    {
        $this->wpdb->show_errors(false);
        $this->wpdb->suppress_errors(true);
    }
}

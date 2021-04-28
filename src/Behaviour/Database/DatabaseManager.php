<?php
declare(strict_types=1);

namespace PhiSYS\Testing\Behaviour\Database;

interface DatabaseManager
{
    public function find(string $statement, array $values): array;

    public function insert(string $statement, array $values): void;

    public function delete(string $statement, array $values): void;

    /**
     * @param string $storeName Most specific name of the data store (ie: "db.schema.table")
     */
    public function clear(string $storeName): void;
}

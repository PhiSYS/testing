<?php
declare(strict_types=1);

namespace DosFarma\Testing\Behaviour\Database\Dbal;

use Doctrine\DBAL\Driver\Connection;
use Doctrine\DBAL\FetchMode;
use Doctrine\DBAL\ParameterType;
use Doctrine\DBAL\Statement;

trait DbalDatabaseManagerTrait
{
    private Connection $connection;
    private array $statements;

    /**
     * @inheritDoc
     */
    public function clear(string $storeName): void
    {
        $preparedStatement = $this->prepareStatement("TRUNCATE TABLE $storeName"); // ANSI SQL:2008
        $preparedStatement->execute();
    }

    public function find(string $statement, array $params): array
    {
        return $this->executeAndFetch($statement, $params);
    }

    public function insert(string $statement, array $params): void
    {
        $this->execute($statement, $params);
    }

    public function delete(string $statement, array $params): void
    {
        $this->execute($statement, $params);
    }

    private function prepareStatement(string $statement): Statement
    {
        if (!array_key_exists($statement, $this->statements)) {
            $this->statements[$statement] = $this->connection->prepare($statement);
        }

        return $this->statements[$statement];
    }

    private function executeAndFetch(string $statement, array $params): ?array
    {
        $preparedStatement = $this->prepareStatement($statement);
        $preparedStatement->execute($params);

        return $preparedStatement->fetchAll(FetchMode::ASSOCIATIVE);
    }

    private function execute(string $statement, array $params): void
    {
        $preparedStatement = $this->prepareStatement($statement);
        $preparedStatement->execute($params);
    }
}

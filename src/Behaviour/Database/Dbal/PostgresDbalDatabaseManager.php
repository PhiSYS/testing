<?php
declare(strict_types=1);

namespace DosFarma\Testing\Behaviour\Database\Dbal;

use Doctrine\DBAL\Driver\Connection;
use Doctrine\DBAL\ParameterType;
use DosFarma\Testing\Behaviour\Database\DatabaseManager;

final class PostgresDbalDatabaseManager implements DatabaseManager
{
    use DbalDatabaseManagerTrait;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
        $this->statements = [];
    }

    /**
     * @inheritDoc
     */
    public function clear(string $storeName): void
    {
        $storeName = $this->connection->quote($storeName. ParameterType::STRING);
        $preparedStatement = $this->prepareStatement("TRUNCATE TABLE $storeName RESTART IDENTITY");
        $preparedStatement->execute();
    }
}

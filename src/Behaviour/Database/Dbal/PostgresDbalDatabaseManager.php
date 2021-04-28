<?php
declare(strict_types=1);

namespace PhiSYS\Testing\Behaviour\Database\Dbal;

use Doctrine\DBAL\Driver\Connection;
use PhiSYS\Testing\Behaviour\Database\DatabaseManager;

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
        $preparedStatement = $this->prepareStatement("TRUNCATE TABLE $storeName RESTART IDENTITY");
        $preparedStatement->execute();
    }
}

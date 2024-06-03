<?php
declare(strict_types=1);

namespace tests;

use Mockery as mockery;
use PHPUnit\Framework\TestCase;
use PDO;
use PDOStatement;
use App\Models\CsvModel;
use App\Config\DatabaseConn;

class CsvModelTest extends TestCase
{
    protected function tearDown(): void
    {
        mockery::close();
    }

    #[\Test]
    public function testWriteToDatabase():void
    {
        $databaseConnMock = mockery::mock(DatabaseConn::class);

        $pdoMock = mockery::mock(PDO::class);
        $stmtMock = mockery::mock(PDOStatement::class);

        $databaseConnMock->shouldReceive('getPDO')->once()->andReturn($pdoMock);
        $pdoMock->shouldReceive('prepare')->times(2)->andReturn($stmtMock);
        $stmtMock->shouldReceive('execute')->times(2)->andReturn(true);
        $stmtMock->shouldReceive('fetchColumn')->once()->andReturn(false);


        $csvModel = new CsvModel($databaseConnMock);

        $data = ['name' => 'John Doe', 'email' => 'john@example.com'];

        $this->assertTrue($csvModel->insertData($data));
    }
}

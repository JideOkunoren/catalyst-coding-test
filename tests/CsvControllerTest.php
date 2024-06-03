<?php

declare(strict_types=1);

namespace tests;

use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;
use App\Controllers\CsvController;
use App\Models\CsvModel;

class CsvControllerTest extends TestCase
{
    private string $filename;
    private CsvModel $mockCsvModel;
    private CsvController $csvCtrl;

    /**
     * @return void
     * @throws Exception
     */
    protected function setUp(): void
    {
        $this->filename = dirname(__DIR__ ). '/app/assets/users.csv';
        $this->mockCsvModel = $this->createMock(CsvModel::class);
        $this->csvCtrl = new CsvController($this->mockCsvModel, $this->filename);
    }

    #[\Test]
    #[\Covers(CsvController::class)]
    public function testCanUpdateNames(): void
    {
        $this->assertEquals('Chris', $this->csvCtrl->updateName('chris'));
        $this->assertEquals('Jill', $this->csvCtrl->updateName('jill45'));
    }

    #[\Test]
    #[\Covers(CsvController::class)]
    public function testValidateEmail(): void
    {
        $this->assertEquals($this->csvCtrl->validateEmail('Samuel.Jackson@Catalyst.Co.Uk'), 'samuel.jackson@catalyst.co.uk');
        $this->assertEquals($this->csvCtrl->validateEmail('Una!@Thurnam!!!@catalyst.co.uk'), 'INVALID EMAIL FORMAT Una!@Thurnam!!!@catalyst.co.uk');
        $this->assertEquals($this->csvCtrl->validateEmail('Vivica@Fox@catalyst.co.uk'), 'INVALID EMAIL FORMAT Vivica@Fox@catalyst.co.uk');
    }

    #[\Test]
    #[\Covers(CsvController::class)]
    public function testGetInvalidData(): void
    {
        $invalidData = $this->csvCtrl->getInvalidData();
        $this->assertCount(4, $invalidData);
    }

    #[\Test]
    #[\Covers(CsvController::class)]
    public function testGetValidData(): void
    {
        $validData = $this->csvCtrl->getValidData();
        $this->assertCount(7, $validData);
    }

    #[\Test]
    #[\Covers(CsvController::class)]
    public function testWriteToDatabase(): void
    {
        $this->mockCsvModel->expects($this->exactly(7))
            ->method('insertData')
            ->willReturnCallback(function ($userData) {
                return true;
            });

        $this->csvCtrl->writeToDatabase();
    }

}

<?php
declare(strict_types=1);

namespace App\Controllers;

use Exception;
use App\Models\CsvModel;

class CsvController
{
    private string $filename;
    private array $validData = [];
    private array $invalidData = [];

    /**
     * @throws Exception
     */
    public function __construct(
        private readonly CsvModel $csvModel,
        ?string                   $filename = null
    )
    {
        $this->filename = $filename;
        if ($this->filename !== null) {
            try {
                $this->readCsv();
            } catch (Exception $e) {
                throw new Exception("Error reading file': {$e->getMessage()}");
            }
        }
    }

    /**
     * @return array
     */
    public function getValidData(): array
    {
        return $this->validData;
    }

    /**
     * @return array
     */
    public function getInvalidData(): array
    {
        return $this->invalidData;
    }

    /**
     * @throws Exception
     */
    public function writeToDatabase(): void
    {
        try {
            foreach ($this->validData as $data) {
                $this->csvModel->insertData($data);
            }
        } catch (Exception $e) {
            throw new Exception("Error Writing to Database': {$e->getMessage()}");
        }
    }

    /**
     * @throws Exception
     */
    private function readCsv(): void
    {
        try {
            $fileHandle = fopen($this->filename, 'r');

            if ($fileHandle === false) {
                throw new Exception("Cannot open file '{$this->filename}'");
            }

            $emails = [];

            fgetcsv($fileHandle);

            while (($fileData = fgetcsv($fileHandle, 1000, ",")) !== false) {
                $this->processCsvRow($fileData, $emails);
            }
            fclose($fileHandle);
        } catch (Exception $e) {
            throw new Exception("Error reading file '{$this->filename}': {$e->getMessage()}");
        }
    }

    /**
     * @param array $fileData
     * @param array $emails
     * @return void
     */
    private function processCsvRow(array $fileData, array &$emails): void
    {
        if (count($fileData) === 3) {
            $name = $this->updateName($fileData[0]);
            $surname = $this->updateName($fileData[1]);
            $email = $this->validateEmail(trim(strtolower($fileData[2])));

            if (!str_contains($email, 'INVALID EMAIL FORMAT')) {
                $this->addValidOrDuplicateData($name, $surname, $email, $emails);
            } else {
                $this->addInvalidData($name, $surname, $email);
            }
        } else {
            $this->addInvalidData(
                $fileData[0] ?? 'INVALID NAME FORMAT',
                $fileData[1] ?? 'INVALID SURNAME FORMAT',
                $fileData[2] ?? 'INVALID EMAIL FORMAT'
            );
        }
    }

    private function addValidOrDuplicateData(string $name, string $surname, string $email, array &$emails): void
    {
        if (!in_array($email, $emails)) {
            $this->validData[] = [
                'name' => $name,
                'surname' => $surname,
                'email' => $email
            ];
            $emails[] = $email;
        } else {
            $this->invalidData[] = [
                'name' => $name,
                'surname' => $surname,
                'email' => $email
            ];
        }
    }

    private function addInvalidData(string $name, string $surname, string $email): void
    {
        $this->invalidData[] = [
            'name' => $name,
            'surname' => $surname,
            'email' => $email
        ];
    }

    /**
     * @param string $name
     * @param bool $strictCheck
     * @return string
     */
    public function updateName(string $name, bool $strictCheck = true): string
    {
        if ($strictCheck) {
            $regex = '/^[a-zA-Z]+$/';
            $formattedName = preg_replace('/[^a-zA-Z\s]/', '', $name);
            $formattedName = trim($formattedName);
            $formattedName = ucwords(strtolower($formattedName));

            if (preg_match($regex, $formattedName)) {
                return $formattedName;
            } else {
                return 'INVALID NAME FORMAT ' . $name;
            }
        } else {
            return ucfirst(strtolower($name));
        }
    }

    /**
     * @param string $email
     * @return string
     */
    public function validateEmail(string $email): string
    {
        $cleanEmail = trim($email);
        $emailRegex = '/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/';

        if (!filter_var($cleanEmail, FILTER_VALIDATE_EMAIL) || !preg_match($emailRegex, $cleanEmail)) {
            echo 'Unable to process invalid email ' . $email . PHP_EOL;
            return 'INVALID EMAIL FORMAT ' . $cleanEmail;
        }
        return strtolower($cleanEmail);
    }
}

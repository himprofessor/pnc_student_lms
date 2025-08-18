<?php

namespace App\Imports;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class StudentsImport implements ToModel, WithHeadingRow
{
    private $errors = [];
    private $rowNumber = 1; // Start at 1 for readability

    public function model(array $row)
    {
        $this->rowNumber++;

        // Validate row data
        if (empty($row['name']) || empty($row['email']) || empty($row['password'])) {
            $this->errors[] = "Row {$this->rowNumber}: Name, email, and password are required.";
            return null; // Skip this row
        }

        // Check if email already exists
        if (User::where('email', $row['email'])->exists()) {
            $this->errors[] = "Row {$this->rowNumber}: Email {$row['email']} already exists.";
            return null;
        }

        return new User([
            'name'     => $row['name'],
            'email'    => $row['email'],
            'password' => Hash::make($row['password']),
        ]);
    }

    public function getErrors()
    {
        return $this->errors;
    }
}

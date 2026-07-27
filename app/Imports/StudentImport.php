<?php

namespace App\Imports;

use App\Models\ClassModel;
use App\Models\Student;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class StudentImport implements ToModel, WithHeadingRow, WithValidation
{
    public function model(array $row): ?Student
    {
        $classId = null;
        if (!empty($row['class_name'])) {
            $class = ClassModel::where('name', $row['class_name'])->first();
            $classId = $class?->id;
        }

        // Create or find guardian
        $guardianId = null;
        if (!empty($row['guardian_email'])) {
            $guardian = User::firstOrCreate(
                ['email' => $row['guardian_email']],
                [
                    'name' => $row['guardian_name'] ?? 'Guardian',
                    'phone' => $row['guardian_phone'] ?? '',
                    'password' => Hash::make('password'),
                    'role' => 'guardian',
                    'status' => 'active',
                ]
            );
            $guardianId = $guardian->id;
        }

        if (!$classId || !$guardianId) {
            return null;
        }

        $gender = strtolower(trim($row['gender'] ?? ''));
        $gender = match($gender) {
            'male', 'm' => 'm',
            'female', 'f' => 'f',
            default => 'other',
        };

        $student = Student::create([
            'name' => $row['name'],
            'dob' => $row['dob'] ?? null,
            'gender' => $gender,
            'class_id' => $classId,
            'guardian_id' => $guardianId,
            'admission_date' => $row['admission_date'] ?? now()->toDateString(),
            'admission_no' => 'ADM-' . str_pad(Student::withTrashed()->max('id') + 1, 4, '0', STR_PAD_LEFT),
            'blood_group' => $row['blood_group'] ?? null,
            'status' => 'active',
        ]);

        // Link guardian via pivot
        $student->guardians()->syncWithoutDetaching([
            $guardianId => ['relationship_type' => 'parent', 'is_primary' => true]
        ]);

        return $student;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'gender' => 'required|string',
            'class_name' => 'required|string',
            'guardian_email' => 'required|email',
            'guardian_name' => 'nullable|string',
            'guardian_phone' => 'nullable|string',
            'dob' => 'nullable|date',
            'admission_date' => 'nullable|date',
            'blood_group' => 'nullable|string',
        ];
    }
}

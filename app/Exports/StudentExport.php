<?php

namespace App\Exports;

use App\Models\Student;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class StudentExport implements FromQuery, WithHeadings, WithMapping, WithStyles
{
    public function query()
    {
        return Student::with(['class', 'guardian'])
            ->where('status', 'active');
    }

    public function headings(): array
    {
        return [
            'Admission No',
            'Name',
            'Gender',
            'Date of Birth',
            'Class',
            'Section',
            'Guardian Name',
            'Guardian Phone',
            'Guardian Email',
            'Admission Date',
            'Blood Group',
        ];
    }

    public function map($student): array
    {
        return [
            $student->admission_no,
            $student->name,
            $student->gender === 'm' ? 'Male' : ($student->gender === 'f' ? 'Female' : 'Other'),
            $student->dob?->format('Y-m-d'),
            $student->class?->name,
            $student->class?->section,
            $student->guardian?->name,
            $student->guardian?->phone,
            $student->guardian?->email,
            $student->admission_date?->format('Y-m-d'),
            $student->blood_group,
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}

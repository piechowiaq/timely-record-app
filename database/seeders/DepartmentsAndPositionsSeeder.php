<?php

namespace Database\Seeders;

use App\Models\Department;
use App\Models\Position;
use Illuminate\Database\Seeder;

class DepartmentsAndPositionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $departmentPositions = [
            'Kitchen' => [
                'Executive Chef',
                'Sous Chef',
                'Chef de Partie',
                'Cook',
                'Junior Cook',
            ],
            'Stewarding' => [
                'Head Steward',
                'Steward',
            ],
            'F&D Service' => [
                'F&D Manager',
                'F&D Assistant',
            ],
            'M&E Service' => [
                'M&E Supervisor',
                'M&E Assistant',
            ],
            'Front Office' => [
                'FO Manager',
                'FO Assistant',
            ],
            'Guest Relations' => [
                'GR Manager',
            ],
            'Reservations' => [
                'Reservations Attendant',
            ],
            'Housekeeping' => [
                'HSK Manager',
                'Houseman',
            ],
            'Spa & Wellness' => [
                'Spa Receptionist',
                'Life Guard',
            ],
            'Repair & Maintenance' => [
                'Chief Engineer',
                'Electrician',
            ],
            'Administration' => [
                'General Manager',
            ],
            'Human Resources' => [
                'HR Manager',
            ],
            'IT' => [
                'IT Manager',
            ],
            'Accounting' => [
                'Financial Controller',
                'Chief Accountant',
                'Accountant',
            ],
            'Security' => [
                'Security Manager',
            ],
            'Sales' => [
                'Director of Sales',
                'Sales Manager',
            ],
            'M&E Sales' => [
                'M&E Coordinator',
            ],
            'Marketing' => [
                'Marketing Specialist',
            ],
        ];

        foreach ($departmentPositions as $departmentName => $positions) {
            $department = Department::create(['name' => $departmentName]);

            foreach ($positions as $position) {
                Position::create(['name' => $position, 'department_id' => $department->id]);
            }
        }
    }
}

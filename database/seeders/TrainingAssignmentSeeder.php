<?php

namespace Database\Seeders;

use App\Models\Department;
use App\Models\Position;
use App\Models\Training;
use Illuminate\Database\Seeder;

class TrainingAssignmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $trainings = [
            'Szkolenie wstępne BHP' => [
                'description' => 'Introductory training on occupational safety and health.',
                'validity_period' => 12, // Valid for 12 months
                'departments' => [
                    'Kitchen',
                    'Stewarding',
                    'F&D Service',
                    'M&E Service',
                    'Front Office',
                    'Guest Relations',
                    'Reservations',
                    'Housekeeping',
                    'Spa & Wellness',
                    'Repair & Maintenance',
                    'Administration',
                    'Human Resources',
                    'IT',
                    'Accounting',
                    'Security',
                    'Sales',
                    'M&E Sales',
                    'Marketing',
                ],
            ],
            'Fire Safety' => [
                'description' => 'Essential training for fire safety procedures.',
                'validity_period' => 24, // Valid for 24 months
                'departments' => [
                    'Kitchen',
                    'Stewarding',
                    'F&D Service',
                    'M&E Service',
                    'Front Office',
                    'Guest Relations',
                    'Reservations',
                    'Housekeeping',
                    'Spa & Wellness',
                    'Repair & Maintenance',
                    'Administration',
                    'Human Resources',
                    'IT',
                    'Accounting',
                    'Security',
                    'Sales',
                    'M&E Sales',
                    'Marketing',
                ],
            ],
            'Advanced Culinary Skills' => [
                'description' => 'Advanced techniques for experienced chefs.',
                'validity_period' => 36, // Valid indefinitely or set a long period
                'positions' => [
                    'Executive Chef',
                    'Sous Chef',
                ],
            ],
            'Customer Relationship Management' => [
                'description' => 'Training on managing and improving customer relationships.',
                'validity_period' => 18, // Valid for 18 months
                'departments' => [
                    'F&D Service',
                    'M&E Service',
                    'Front Office',
                    'Guest Relations',
                    'Reservations',
                ],
            ],
        ];

        foreach ($trainings as $trainingName => $details) {
            $training = Training::firstOrCreate(
                ['name' => $trainingName],
                [
                    'description' => $details['description'],
                    'validity_period' => $details['validity_period'],
                ]
            );

            if (isset($details['departments'])) {
                foreach ($details['departments'] as $departmentName) {
                    $department = Department::where('name', $departmentName)->first();
                    foreach ($department->positions as $position) {
                        $training->positions()->attach($position);
                    }
                }
            }

            if (isset($details['positions'])) {
                foreach ($details['positions'] as $positionName) {
                    $position = Position::where('name', $positionName)->first();
                    $training->positions()->attach($position);
                }
            }
        }
    }
}

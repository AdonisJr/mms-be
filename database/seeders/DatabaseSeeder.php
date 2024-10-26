<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Service;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'firstname' => 'admin',
            'middlename' => '',
            'lastname' => 'admin',
            'email' => 'admin@gmail.com',
            'type' => 'general_service',
            'role' =>'admin',
            'department' => '',
            'password' => Hash::make('password'),
        ]);

        User::factory()->create([
            'firstname' => 'Sample',
            'middlename' => '',
            'lastname' => 'User',
            'email' => 'user@gmail.com',
            'type' => 'utility_worker',
            'role' =>'user',
            'department' => 'user department',
            'password' => Hash::make('password'),
        ]);

        User::factory()->create([
            'firstname' => 'Sample',
            'middlename' => '',
            'lastname' => 'faculty',
            'email' => 'faculty@gmail.com',
            'type' => 'faculty',
            'role' =>'user',
            'department' => 'Engineering',
            'password' => Hash::make('password'),
        ]);
        // Faculty users

        $facultyUsers = [
            ['firstname' => 'Alice', 'middlename' => 'B.', 'lastname' => 'Smith', 'email' => 'alice.smith@gmail.com'],
            ['firstname' => 'Bob', 'middlename' => 'C.', 'lastname' => 'Johnson', 'email' => 'bob.johnson@gmail.com'],
            ['firstname' => 'Carol', 'middlename' => 'D.', 'lastname' => 'Williams', 'email' => 'carol.williams@gmail.com'],
            ['firstname' => 'David', 'middlename' => 'E.', 'lastname' => 'Jones', 'email' => 'david.jones@gmail.com'],
            ['firstname' => 'Eva', 'middlename' => 'F.', 'lastname' => 'Brown', 'email' => 'eva.brown@gmail.com'],
        ];

        foreach ($facultyUsers as $faculty) {
            User::create([
                'firstname' => $faculty['firstname'],
                'middlename' => $faculty['middlename'],
                'lastname' => $faculty['lastname'],
                'email' => $faculty['email'],
                'gender' => 'female',
                'type' => 'faculty',
                'role' => 'user',
                'department' => 'Faculty Department',
                'password' => Hash::make('password'),
            ]);
        }

        // Utility worker users
        $utilityWorkers = [
            ['firstname' => 'George', 'middlename' => 'H.', 'lastname' => 'Taylor', 'email' => 'george.taylor@gmail.com'],
            ['firstname' => 'Hannah', 'middlename' => 'I.', 'lastname' => 'Anderson', 'email' => 'hannah.anderson@gmail.com'],
            ['firstname' => 'Ian', 'middlename' => 'J.', 'lastname' => 'Thomas', 'email' => 'ian.thomas@gmail.com'],
            ['firstname' => 'Jessica', 'middlename' => 'K.', 'lastname' => 'Moore', 'email' => 'jessica.moore@gmail.com'],
            ['firstname' => 'Kevin', 'middlename' => 'L.', 'lastname' => 'Jackson', 'email' => 'kevin.jackson@gmail.com'],
            ['firstname' => 'Laura', 'middlename' => 'M.', 'lastname' => 'White', 'email' => 'laura.white@gmail.com'],
            ['firstname' => 'Michael', 'middlename' => 'N.', 'lastname' => 'Harris', 'email' => 'michael.harris@gmail.com'],
            ['firstname' => 'Nina', 'middlename' => 'O.', 'lastname' => 'Martin', 'email' => 'nina.martin@gmail.com'],
            ['firstname' => 'Oscar', 'middlename' => 'P.', 'lastname' => 'Thompson', 'email' => 'oscar.thompson@gmail.com'],
            ['firstname' => 'Pamela', 'middlename' => 'Q.', 'lastname' => 'Garcia', 'email' => 'pamela.garcia@gmail.com'],
        ];

        foreach ($utilityWorkers as $worker) {
            User::create([
                'firstname' => $worker['firstname'],
                'middlename' => $worker['middlename'],
                'lastname' => $worker['lastname'],
                'email' => $worker['email'],
                'gender' => 'female',
                'type' => 'utility_worker',
                'role' => 'user',
                'department' => 'Utility Department',
                'password' => Hash::make('password'),
            ]);
        }

        $services = [
            [
                'name' => 'Repair',
                'type_of_service' => 'repair',
                'description' => 'Repairing various types of equipment and appliances.',
            ],
            [
                'name' => 'Replacement',
                'type_of_service' => 'replacement',
                'description' => 'Replacing old or damaged equipment with new ones.',
            ],
            [
                'name' => 'Transfer of Equipment',
                'type_of_service' => 'transfer_of_equipment',
                'description' => 'Transferring equipment from one location to another.',
            ],
            [
                'name' => 'Installation',
                'type_of_service' => 'installation',
                'description' => 'Installing new equipment or appliances.',
            ],
            [
                'name' => 'General Cleaning',
                'type_of_service' => 'general_cleaning',
                'description' => 'Providing general cleaning services for residential and commercial spaces.',
            ],
            [
                'name' => 'Garbage Removal',
                'type_of_service' => 'garbage_removal',
                'description' => 'Removing garbage and waste materials from designated areas.',
            ],
        ];

        foreach ($services as $service) {
            Service::create($service);
        }

    }
}

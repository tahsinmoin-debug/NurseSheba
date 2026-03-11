<?php

namespace Database\Seeders;

use App\Models\NurseProfile;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoNurseSeeder extends Seeder
{
    public function run(): void
    {
        $maleNames = [
            'Bryan Mbeumo',
            'Senne Lammens',
            'Bruno Fernandes',
            'Matheus Cunha',
            'Amad',
            'Benjamin Sesko',
            'Casemiro',
            'Harry Maguire',
            'Lisandro Martinez',
            'Shafin Ahmed',
            'Md. Ashraful Islam',
            'Tanvir Hasan',
            'Nayeem Hasan',
            'Rafsan Karim',
            'Mahmudul Hasan',
            'Ahsan Habib',
            'Fahim Rahman',
            'Rakibul Islam',
            'Sajid Hossain',
            'Minhaj Uddin',
            'Tariqul Alam',
            'Imran Kabir',
            'Sabbir Ahmed',
            'Hasibul Bashar',
            'Nafis Chowdhury',
        ];

        $femaleNames = [
            'Mamduha Jabnir',
            'Shornali Roy Toma',
            'Maimuna Anjum',
            'Rahnuma Rusammi Riya',
            'Samiha Nowrin Nikita',
            'Effat Habiba',
            'Zerin Hassan',
            'Mashrin Mahbub',
            'Nusrat Jahan',
            'Fariha Tabassum',
            'Sadia Afrin',
            'Tanjila Islam',
            'Afsana Mimi',
            'Rumana Akter',
            'Farzana Yasmin',
            'Mst. Aklima Khatun',
            'Shamima Sultana',
            'Tahsin Nawar',
            'Ishrat Jahan',
            'Raihana Sultana',
            'Lubna Rahman',
            'Sharmin Akter',
            'Maliha Ahmed',
            'Nabila Noor',
            'Sanjida Rahman',
        ];

        $specializations = [
            'General Nursing',
            'Elderly Care',
            'ICU Care',
            'Post-Surgery Care',
            'Pediatric Care',
            'Wound Dressing',
            'Injection and IV',
            'Physiotherapy Support',
            'Mother and Newborn Care',
            'Diabetes Care',
        ];

        $qualifications = [
            'Diploma in Nursing',
            'BSc in Nursing',
            'MSc in Nursing',
            'Registered Nurse (RN)',
            'Certificate in Critical Care Nursing',
        ];

        $locations = config('dhaka_areas', ['Dhanmondi', 'Uttara', 'Gulshan']);
        $index = 1;

        foreach ($maleNames as $name) {
            $this->createDemoNurse($index, $name, 'male', $locations, $specializations, $qualifications);
            $index++;
        }

        foreach ($femaleNames as $name) {
            $this->createDemoNurse($index, $name, 'female', $locations, $specializations, $qualifications);
            $index++;
        }
    }

    private function createDemoNurse(
        int $index,
        string $name,
        string $gender,
        array $locations,
        array $specializations,
        array $qualifications
    ): void {
        $location = $locations[($index - 1) % count($locations)];
        $specialization = $specializations[($index - 1) % count($specializations)];
        $qualification = $qualifications[($index - 1) % count($qualifications)];
        $email = 'demo.nurse' . str_pad((string) $index, 3, '0', STR_PAD_LEFT) . '@nursesheba.com';
        $phone = '017' . str_pad((string) (7000000 + $index), 8, '0', STR_PAD_LEFT);

        $user = User::updateOrCreate(
            ['email' => $email],
            [
                'name' => $name,
                'email' => $email,
                'phone' => $phone,
                'address' => $location . ', Dhaka',
                'location' => $location,
                'password' => Hash::make('Password123'),
                'role' => 'nurse',
            ]
        );

        NurseProfile::updateOrCreate(
            ['user_id' => $user->id],
            [
                'qualification' => $qualification,
                'gender' => $gender,
                'specialization' => $specialization,
                'experience_years' => (($index - 1) % 12) + 1,
                'district' => 'Dhaka',
                'thana' => $location,
                'bio' => 'Experienced home-care nurse available in ' . $location . ', Dhaka.',
                'availability' => true,
                'is_approved' => true,
            ]
        );
    }
}

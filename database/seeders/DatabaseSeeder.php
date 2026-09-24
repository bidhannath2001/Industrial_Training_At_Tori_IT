<?php

namespace Database\Seeders;

use App\Models\District;
use App\Models\Doctor;
use App\Models\Organization;
use App\Models\SaloonService;
use App\Models\Subcategory;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        //create districts
        $districts = [
            ['name'=>'Pune','state'=>'Maharashtra'],
            ['name'=>'Mumbai','state'=>'Maharashtra'],
            ['name'=>'Bangalore','state'=>'Karnataka'],
            ['name'=>'Hyderabad','state'=>'Telangana'],
        ];
        foreach($districts as $district){
            District::create($district);
        }
        //create subcategories
        $subcategories = [
            ['name'=>'Cardiologist','description'=>'Heart speacialist'],
            ['name' => 'Dermatologist', 'description' => 'Skin specialist'],
            ['name' => 'Dentist', 'description' => 'Teeth specialist'],
            ['name' => 'General Physician', 'description' => 'General doctor']
        ];

        foreach($subcategories as $subcategory){
            Subcategory::create($subcategory);
        }
        
        //create user
        $user = User::create([
            'name'=>'Test User',
            'email'=>'user@test.com',
            'phone'=>'1234567890',
            'password'=>Hash::make('password'),
            'gender'=>'Male',
            'age'=>25,
            'district_id'=>1,
            'state'=>'Maharashtra',
            'village_name'=>'Pune',
            'height'=>175,
            'weight'=>70,
            'is_active'=>true
        ]);

        $clinic = Organization::create([
            'name'=>'Health Care Clinic',
            'type'=>'doctor',
            'owner_name'=>'Dr. Sharma',
            'email'=>'clinic@test.com',
            'phone'=>'9876543210',
            'location'=>'Pune',
            'district_id'=>1,
            'state'=>'Maharashtra',
            'description'=>'Multi-specialty clinic with experienced doctors',
            'is_approved'=>true,
            'gst_amount'=>18,
            'base_amount'=>500,
            'commission'=>10,
            'zapmor_commission'=>5,
            'booking_fee'=>20
        ]);

        //create saloon
        $saloon = Organization::create([
            'name' => 'Glow Beauty Salon',
            'type' => 'beautician',
            'owner_name' => 'Priya Singh',
            'email' => 'salon@test.com',
            'phone' => '9876543222',
            'location' => 'Main Street, Pune',
            'district_id' => 1,
            'state' => 'Maharashtra',
            'description' => 'Premium beauty salon',
            'is_approved' => true,
            'booking_fee' => 10
        ]);

        //create doctors
        $doctorUser = User::create([
            'name' => 'Dr. Amit Sharma',
            'email' => 'doctor1@test.com',
            'phone' => '9876543211',
            'password' => Hash::make('password123'),
            'gender' => 'Male',
            'age' => 45,
            'district_id' => 1,
            'state' => 'Maharashtra',
            'village_name' => 'Pune',
            'is_active' => true
        ]);

        Doctor::create([
            'user_id' => $doctorUser->id,
            'organization_id' => $clinic->id,
            'registration_number' => 'MCI12345',
            'designation'=>'MBBS, MD',
            'higher_degree'=>'MD Cardiology',
            'subcategory_id'=>1,
            'bio'=>'Experienced Cardiologist',
            'status'=>'approved',
            'experience_years'=>10,
            'fee'=>500,
            'is_available'=>true,
            'avg_delay_minutes'=>10,
            'additional_time_minutes'=>5,
            'available_days'=>'Mon, Tue, Wed, Thu, Fri',
            'starting_time'=>'09:00:00',
            'ending_time'=>'13:00:00',
            'break_time_start'=>'12:00:00',
            'break_time_end'=>'12:30:00'
        ]);

        //create saloon services
        SaloonService::create([
           'organization_id'=>$saloon->id,
           'service_name'=>'Haircut',
           'price'=>300,
           'duration_minutes'=>30,
           'description'=>'Professional hair cutting service',
        ]);
        SaloonService::create([
            'organization_id'=>$saloon->id,
            'service_name'=>'Hair Coloring',
            'price'=>1500,
            'duration_minutes'=>90,
            'description'=>'Full hair coloring service with treatment',
        ]);
    }
}

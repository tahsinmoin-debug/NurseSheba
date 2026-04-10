<?php

namespace Tests\Feature;

use App\Models\NurseProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_can_view_patient_homepage(): void
    {
        $response = $this->get('/');

        $response->assertOk();
        $response->assertSee('Quality Home Nursing Services');
        $response->assertSee('Register as Patient');
    }

    public function test_guest_can_view_nurse_homepage(): void
    {
        $response = $this->get('/for-nurses');

        $response->assertOk();
        $response->assertSee('Grow Your Nursing Career With NurseSheba');
        $response->assertSee('Register as Nurse');
    }

    public function test_nurse_sees_nurse_homepage_on_root(): void
    {
        $nurse = User::factory()->create([
            'role' => 'nurse',
            'location' => 'Dhanmondi',
        ]);

        NurseProfile::create([
            'user_id' => $nurse->id,
            'specialization' => 'ICU',
            'experience_years' => 3,
            'district' => 'Dhaka',
            'thana' => 'Dhanmondi',
            'availability' => true,
            'is_approved' => true,
        ]);

        $response = $this->actingAs($nurse)->get('/');

        $response->assertOk();
        $response->assertSee('Nurse Home');
        $response->assertSee('Welcome back, ' . $nurse->name);
        $response->assertSee('Your Quick Actions');
        $response->assertDontSee('Register as Nurse');
    }

    public function test_patient_sees_patient_homepage_on_root(): void
    {
        $patient = User::factory()->create([
            'role' => 'patient',
            'location' => 'Banani',
        ]);

        $response = $this->actingAs($patient)->get('/');

        $response->assertOk();
        $response->assertSee('Patient Home');
        $response->assertSee('Welcome back, ' . $patient->name);
        $response->assertSee('Quick Search');
        $response->assertDontSee('Register as Patient');
    }

    public function test_admin_sees_admin_homepage_on_root(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $response = $this->actingAs($admin)->get('/');

        $response->assertOk();
        $response->assertSee('Admin Home');
        $response->assertSee('Platform overview for ' . $admin->name);
        $response->assertDontSee('Find Nurses');
    }

    public function test_logged_in_nurse_does_not_see_register_cta_on_for_nurses_page(): void
    {
        $nurse = User::factory()->create([
            'role' => 'nurse',
        ]);

        $response = $this->actingAs($nurse)->get('/for-nurses');

        $response->assertOk();
        $response->assertSee('Nurse Home');
        $response->assertDontSee('Register as Nurse');
    }

    public function test_register_page_supports_nurse_role_preselection(): void
    {
        $response = $this->get('/register?role=nurse');

        $response->assertOk();
        $response->assertSee('id="roleInput" value="nurse"', false);
    }
}

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
    }

    public function test_guest_can_view_nurse_homepage(): void
    {
        $response = $this->get('/for-nurses');

        $response->assertOk();
        $response->assertSee('Grow Your Nursing Career With NurseSheba');
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
        $response->assertSee('Your Quick Actions');
    }

    public function test_patient_sees_patient_homepage_on_root(): void
    {
        $patient = User::factory()->create([
            'role' => 'patient',
            'location' => 'Banani',
        ]);

        $response = $this->actingAs($patient)->get('/');

        $response->assertOk();
        $response->assertSee('Quality Home Nursing Services');
    }

    public function test_admin_is_redirected_to_admin_dashboard_from_root(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $response = $this->actingAs($admin)->get('/');

        $response->assertRedirect('/admin/dashboard');
    }

    public function test_register_page_supports_nurse_role_preselection(): void
    {
        $response = $this->get('/register?role=nurse');

        $response->assertOk();
        $response->assertSee('id="roleInput" value="nurse"', false);
    }
}

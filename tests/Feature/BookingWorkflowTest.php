<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\NurseProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookingWorkflowTest extends TestCase
{
    use RefreshDatabase;

    public function test_patient_can_create_pending_booking_with_service_address(): void
    {
        $patient = User::factory()->create([
            'role' => 'patient',
        ]);

        $nurse = User::factory()->create([
            'role' => 'nurse',
            'location' => 'Dhanmondi',
        ]);

        NurseProfile::create([
            'user_id' => $nurse->id,
            'specialization' => 'ICU',
            'experience_years' => 4,
            'district' => 'Dhaka',
            'thana' => 'Dhanmondi',
            'availability' => true,
            'is_approved' => true,
        ]);

        $response = $this->actingAs($patient)->post(route('patient.book.store'), [
            'nurse_id' => $nurse->id,
            'date' => now()->addDays(2)->toDateString(),
            'time' => '10:30',
            'service_type' => 'General Nursing',
            'service_address' => 'House 12, Road 5, Dhanmondi, Dhaka',
        ]);

        $response->assertRedirect(route('patient.dashboard'));

        $this->assertDatabaseHas('bookings', [
            'patient_id' => $patient->id,
            'nurse_id' => $nurse->id,
            'service_type' => 'General Nursing',
            'service_address' => 'House 12, Road 5, Dhanmondi, Dhaka',
            'status' => 'pending',
        ]);
    }

    public function test_nurse_can_accept_reject_and_complete_bookings(): void
    {
        $patient = User::factory()->create([
            'role' => 'patient',
        ]);

        $nurse = User::factory()->create([
            'role' => 'nurse',
        ]);

        $acceptedBooking = Booking::create([
            'patient_id' => $patient->id,
            'nurse_id' => $nurse->id,
            'date' => now()->addDay()->toDateString(),
            'time' => '09:00:00',
            'service_type' => 'Accepted Case',
            'service_address' => 'Accepted Address',
            'status' => 'pending',
        ]);

        $rejectedBooking = Booking::create([
            'patient_id' => $patient->id,
            'nurse_id' => $nurse->id,
            'date' => now()->addDays(2)->toDateString(),
            'time' => '11:00:00',
            'service_type' => 'Rejected Case',
            'service_address' => 'Rejected Address',
            'status' => 'pending',
        ]);

        $this->actingAs($nurse)->post(route('nurse.booking.accept', $acceptedBooking))
            ->assertRedirect();

        $acceptedBooking->refresh();
        $this->assertSame('accepted', $acceptedBooking->status);

        $this->actingAs($nurse)->post(route('nurse.booking.complete', $acceptedBooking))
            ->assertRedirect();

        $acceptedBooking->refresh();
        $this->assertSame('completed', $acceptedBooking->status);

        $this->actingAs($nurse)->post(route('nurse.booking.reject', $rejectedBooking))
            ->assertRedirect();

        $rejectedBooking->refresh();
        $this->assertSame('cancelled', $rejectedBooking->status);
    }

    public function test_patient_dashboard_shows_booking_history_and_status_filter(): void
    {
        $patient = User::factory()->create([
            'role' => 'patient',
        ]);

        $nurse = User::factory()->create([
            'role' => 'nurse',
        ]);

        Booking::create([
            'patient_id' => $patient->id,
            'nurse_id' => $nurse->id,
            'date' => now()->addDays(3)->toDateString(),
            'time' => '10:00:00',
            'service_type' => 'Accepted Visit',
            'service_address' => 'Accepted Address',
            'status' => 'accepted',
        ]);

        Booking::create([
            'patient_id' => $patient->id,
            'nurse_id' => $nurse->id,
            'date' => now()->subDays(3)->toDateString(),
            'time' => '09:00:00',
            'service_type' => 'Completed Visit',
            'service_address' => 'Completed Address',
            'status' => 'completed',
        ]);

        $response = $this->actingAs($patient)->get(route('patient.dashboard'));

        $response->assertOk();
        $response->assertSee('Upcoming Appointments');
        $response->assertSee('Booking History');
        $response->assertSee('Accepted Address');
        $response->assertSee('Completed Address');

        $filteredResponse = $this->actingAs($patient)->get(route('patient.dashboard', ['status' => 'accepted']));

        $filteredResponse->assertOk();
        $filteredResponse->assertSee('Accepted Address');
        $filteredResponse->assertDontSee('Completed Address');
    }

    public function test_admin_can_view_and_manage_all_bookings(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $patient = User::factory()->create([
            'role' => 'patient',
        ]);

        $nurse = User::factory()->create([
            'role' => 'nurse',
        ]);

        $booking = Booking::create([
            'patient_id' => $patient->id,
            'nurse_id' => $nurse->id,
            'date' => now()->addDays(4)->toDateString(),
            'time' => '15:00:00',
            'service_type' => 'Oversight Visit',
            'service_address' => 'Admin Oversight Address',
            'status' => 'pending',
        ]);

        $pageResponse = $this->actingAs($admin)->get(route('admin.bookings'));

        $pageResponse->assertOk();
        $pageResponse->assertSee('All Bookings');
        $pageResponse->assertSee('Admin Oversight Address');

        $updateResponse = $this->actingAs($admin)->post(route('admin.bookings.status', $booking), [
            'status' => 'accepted',
        ]);

        $updateResponse->assertRedirect();

        $booking->refresh();
        $this->assertSame('accepted', $booking->status);
    }
}

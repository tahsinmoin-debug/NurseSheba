<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\PasswordResetOtpController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\NurseController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\PaymentController;

// Public routes
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/for-nurses', [HomeController::class, 'nurseHome'])->name('home.nurse');
Route::get('/nurses', [HomeController::class, 'nurses'])->name('nurses.index');
Route::get('/nurses/{id}', [HomeController::class, 'nurseProfile'])->name('nurses.show');

// Auth routes
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::get('/admin/login', [LoginController::class, 'showAdminLoginForm'])->name('admin.login');
Route::post('/admin/login', [LoginController::class, 'adminLogin'])->name('admin.login.submit');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [RegisterController::class, 'register']);
Route::get('/forgot-password', [PasswordResetOtpController::class, 'showRequestForm'])->name('password.otp.request.form');
Route::post('/forgot-password', [PasswordResetOtpController::class, 'sendOtp'])->name('password.otp.send');
Route::get('/forgot-password/verify', [PasswordResetOtpController::class, 'showVerifyForm'])->name('password.otp.verify.form');
Route::post('/forgot-password/verify', [PasswordResetOtpController::class, 'verifyOtp'])->name('password.otp.verify');
Route::get('/forgot-password/reset/{token}', [PasswordResetOtpController::class, 'showResetForm'])->name('password.otp.reset.form');
Route::post('/forgot-password/reset', [PasswordResetOtpController::class, 'resetPassword'])->name('password.otp.reset');

// Redirect /dashboard based on role
Route::middleware('auth')->get('/dashboard', function() {
    $role = auth()->user()->role;
    if ($role === 'admin') return redirect('/admin/dashboard');
    if ($role === 'nurse') return redirect('/nurse/dashboard');
    return redirect('/patient/dashboard');
})->name('dashboard');

// Patient routes
Route::middleware(['auth', 'patient'])->prefix('patient')->name('patient.')->group(function () {
    Route::get('/dashboard', [PatientController::class, 'dashboard'])->name('dashboard');
    Route::get('/book/{nurseId}', [BookingController::class, 'create'])->name('book');
    Route::post('/book', [BookingController::class, 'store'])->name('book.store');
    Route::post('/booking/{booking}/cancel', [BookingController::class, 'cancel'])->name('booking.cancel');
    Route::post('/review', [ReviewController::class, 'store'])->name('review.store');

    // Payment routes
    Route::get('/payment/{booking}', [PaymentController::class, 'show'])->name('payment.show');
    Route::post('/payment/{booking}/stripe-intent', [PaymentController::class, 'createStripeIntent'])->name('payment.stripe.intent');
    Route::get('/payment/{booking}/success', [PaymentController::class, 'stripeSuccess'])->name('payment.stripe.success');
    Route::post('/payment/{booking}/mobile', [PaymentController::class, 'simulateMobile'])->name('payment.mobile');
    Route::get('/payment-history', [PaymentController::class, 'patientHistory'])->name('payment.history');
    Route::get('/invoice/{payment}', [PaymentController::class, 'invoice'])->name('invoice');
});

// Nurse routes
Route::middleware(['auth', 'nurse'])->prefix('nurse')->name('nurse.')->group(function () {
    Route::get('/dashboard', [NurseController::class, 'dashboard'])->name('dashboard');
    Route::get('/profile', [NurseController::class, 'profile'])->name('profile');
    Route::post('/profile', [NurseController::class, 'updateProfile'])->name('profile.update');
    Route::post('/booking/{booking}/accept', [BookingController::class, 'accept'])->name('booking.accept');
    Route::post('/booking/{booking}/reject', [BookingController::class, 'reject'])->name('booking.reject');
    Route::post('/booking/{booking}/complete', [BookingController::class, 'complete'])->name('booking.complete');
    Route::get('/earnings', [PaymentController::class, 'nurseEarnings'])->name('earnings');
});

// Admin routes
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/nurses', [AdminController::class, 'nurses'])->name('nurses');
    Route::post('/nurses/{id}/approve', [AdminController::class, 'approveNurse'])->name('nurses.approve');
    Route::post('/nurses/{id}/reject', [AdminController::class, 'rejectNurse'])->name('nurses.reject');
    Route::get('/patients', [AdminController::class, 'patients'])->name('patients');
    Route::get('/bookings', [AdminController::class, 'bookings'])->name('bookings');
    Route::post('/bookings/{booking}/status', [BookingController::class, 'adminUpdateStatus'])->name('bookings.status');
    Route::get('/complaints', [AdminController::class, 'complaints'])->name('complaints');
    Route::get('/announcements', [AdminController::class, 'announcements'])->name('announcements');
    Route::post('/announcements', [AdminController::class, 'storeAnnouncement'])->name('announcements.store');
    Route::get('/support', [AdminController::class, 'supportRequests'])->name('support');
    Route::post('/payment/{payment}/refund', [PaymentController::class, 'refund'])->name('payment.refund');
});

<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\NurseController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ReviewController;

// Public routes
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/nurses', [HomeController::class, 'nurses'])->name('nurses.index');
Route::get('/nurses/{id}', [HomeController::class, 'nurseProfile'])->name('nurses.show');

// Auth routes
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [RegisterController::class, 'register']);

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
});

// Nurse routes
Route::middleware(['auth', 'nurse'])->prefix('nurse')->name('nurse.')->group(function () {
    Route::get('/dashboard', [NurseController::class, 'dashboard'])->name('dashboard');
    Route::get('/profile', [NurseController::class, 'profile'])->name('profile');
    Route::post('/profile', [NurseController::class, 'updateProfile'])->name('profile.update');
    Route::post('/booking/{booking}/accept', [BookingController::class, 'accept'])->name('booking.accept');
    Route::post('/booking/{booking}/complete', [BookingController::class, 'complete'])->name('booking.complete');
});

// Admin routes
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/nurses', [AdminController::class, 'nurses'])->name('nurses');
    Route::post('/nurses/{id}/approve', [AdminController::class, 'approveNurse'])->name('nurses.approve');
    Route::post('/nurses/{id}/reject', [AdminController::class, 'rejectNurse'])->name('nurses.reject');
    Route::get('/patients', [AdminController::class, 'patients'])->name('patients');
    Route::get('/bookings', [AdminController::class, 'bookings'])->name('bookings');
    Route::get('/complaints', [AdminController::class, 'complaints'])->name('complaints');
    Route::get('/announcements', [AdminController::class, 'announcements'])->name('announcements');
    Route::post('/announcements', [AdminController::class, 'storeAnnouncement'])->name('announcements.store');
    Route::get('/support', [AdminController::class, 'supportRequests'])->name('support');
});

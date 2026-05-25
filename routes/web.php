<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DentalAIController;
use App\Http\Controllers\PasswordResetController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\TreatmentController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/


// login, signup, forgot-pass, reset-pass....



Route::get('/', [AuthController::class, 'homepage'])->name("home");


Route::get('/login', [AuthController::class, 'login'])->name("login");

Route::post('/auth', [AuthController::class , 'authenticate'])->name("user.auth");

Route::get('/signup', [AuthController::class, 'signup'])->name("signup");

Route::post('/signup', [AuthController::class,'store'])->name('users.store');

Route::post('/logout', [AuthController::class , 'logout'])->name('logout');

Route::get('password/reset', [PasswordResetController::class, 'showLinkRequestForm'])
    ->name('password.request');

// Handle sending the password reset link
Route::post('password/email', [PasswordResetController::class, 'sendResetLinkEmail'])
    ->name('password.email');

// Show the form to reset the password
Route::get('password/reset/{token}', [PasswordResetController::class, 'showResetForm'])
    ->name('password.reset');

// Handle the password reset form submission
Route::post('password/reset/', [PasswordResetController::class, 'reset'])
    ->name('password.update');


// posting reservationsss
Route::post('/assess_dental_symptoms', [DentalAIController::class, 'assessSymptoms'])->name('patient.assess');

Route::get('/appointment/create/', [PatientController::class, 'createReservations'])->name('patient.create');

Route::post('/appointment/post', [ReservationController::class, 'store'])
    // Add the throttle middleware here
    ->middleware('throttle:10,60') 
    ->name('patient.store');

Route::post('/appointment/post/existing', [ReservationController::class, 'existingPatient'])->name('patient.existing');

Route::get('/reservation/{reservation}/confirm', [ReservationController::class, 'confirm'])->name('reservations.confirm');

Route::get('/cancel-appointment/{id}', [ReservationController::class, 'cancelAppointment'])->name('reservations.cancelAppointment');

Route::post('/cancel-appointment/post/', [ReservationController::class, 'cancelApp'])->name('reservations.cancel');

// admin

Route::get('/admin/appointments_history/download/{id}', [AdminController::class, 'generateReport'])->name('admin.patientDownload');

Route::post('/admin/appointments_history/download/all/', [AdminController::class, 'generateClinicReport'])->name('admin.generateClinicReport');

Route::get('/admin/appointments/pending', [AdminController::class, 'appointments'])->name('admin.appointments');

Route::get('/admin/appointments/create/{id}', [AdminController::class, 'createReservations'])->name('admin.create');

Route::get('/admin/appointments/reschedule/{id}', [AdminController::class, 'createReservations'])->name('admin.reschedAppointment');

Route::get('/admin/appointments/new-appointment/', [AdminController::class, 'newAppointment'])->name('admin.newAppointment');

Route::get('/admin/appointments/new-appointment/create', [AdminController::class, 'newReservation'])->name('admin.newReservation');

Route::post('/admin/appointments/post', [AdminController::class, 'storeAppointment'])->name('admin.store');

Route::post('/admin/appointments/post/existing', [AdminController::class, 'existingPatient'])->name('admin.existing');

Route::get('/admin/appointments/pending-and-confirmed', [AdminController::class, 'forApprovalAppointments'])->name('admin.forApprovalAppointments');

Route::get('/admin/appointments/ongoing', [AdminController::class, 'ongoingAppointments'])->name('admin.ongoingAppointments');

Route::get('/admin/appointments/approved/{id}', [AdminController::class, 'approveReservation'])->name('admin.approvedReservation');

Route::post('/admin/appointments/rejected/{id}', [AdminController::class, 'rejectReservation'])->name('admin.rejectedReservation');

Route::post('/admin/appointments/canceled/{id}', [AdminController::class, 'cancelAppointment'])->name('admin.cancelAppointment');

Route::get('/admin/appointment/track/{id}', [AdminController::class, 'trackAppointment'])->name('admin.trackReservation');

Route::get('/admin/track-notification/{appointment_number}', [AdminController::class, 'trackNotification'])->name('admin.trackNotification');

// Inside your admin middleware route group
Route::get('/admin/track-request/{appointment_number}', [AdminController::class, 'trackRequestNotification'])->name('admin.trackRequestNotif');
Route::get('/admin/track-cancellation/{appointment_number}', [AdminController::class, 'trackCancellationNotification'])->name('admin.trackCancellationNotif');

Route::get('/admin/appointment/complete/{id}', [AdminController::class, 'completeReservation'])->name('admin.completeReservation');

Route::get('/admin/appointment/noShow/{id}', [AdminController::class, 'noshowReservation'])->name('admin.rejectReservation');

Route::get('/admin/appointment/delete/{id}', [AdminController::class, 'deleteReservationAppointment'])->name('admin.deleteReservation');

Route::get('/admin/records/delete/{id}', [AdminController::class, 'deleteRecordAppointment'])->name('admin.deleteRecord');

Route::get('/admin/records/completed/', [AdminController::class, 'completedRecords'])->name('admin.records');

Route::get('/admin/records/no-show/', [AdminController::class, 'noshowRecords'])->name('admin.noshowRecords');

Route::get('/admin/records/all/patient/{id}', [AdminController::class, 'patientHistory'])->name('admin.showPatient');

Route::get('/admin/profile/', [AdminController::class, 'profile'])->name('admin.profile');

Route::get('/admin/profile/edit/{id}', [AdminController::class, 'editProfile'])->name('admin.editProfile');

Route::post('/admin/profile/update/{id}', [AdminController::class, 'updateProfile'])->name('admin.updateProfile');

Route::get('/admin/profile/editPassword/{id}', [AdminController::class, 'editPassword'])->name('admin.editPassword');

Route::post('/admin/profile/updatePassword/{id}', [AdminController::class, 'updatePassword'])->name('admin.updatePassword');

Route::get("/admin/treatments", [TreatmentController::class, "index"])->name("admin.treatments");

Route::get("/admin/time_slots", [AdminController::class, "viewTime"])->name("admin.timeSlots");

Route::get("/admin/time_slots/delete/{id}", [AdminController::class, "deleteTime"])->name("admin.deleteTime");

Route::post("/admin/time_slots/store", [AdminController::class, "storeClinicHours"])->name("admin.storeClinicHours");

Route::patch("/admin/time_slots/update/{id}", [AdminController::class, "updateTime"])->name("admin.updateTime");

Route::get("/admin/time_slots/edit/{id}", [AdminController::class, "editTime"])->name("admin.editTime");

Route::get("/admin/time_slots/add/", [AdminController::class, "addTime"])->name("admin.addTime");

Route::get("/admin/treatments/new", [TreatmentController::class, "add"])->name("treatment.add");

Route::post("/admin/treatments/store", [TreatmentController::class, "store"])->name("treatment.store");

Route::get("/admin/treatment/edit/{id}", [TreatmentController::class, "edit"])->name("treatment.edit");

Route::post("/admin/treatments/update/{id}", [TreatmentController::class, "update"])->name("treatment.update");

Route::get("/admin/treatments/delete/{id}", [TreatmentController::class, "delete"])->name("treatment.delete");

Route::get('/admin/notifications/clear', [AdminController::class, 'markAllRead'])->name('admin.notifications.markAllRead');

Route::get('/admin/notifications/unread-count', function() {
    $user = auth()->user();
    
    return response()->json([
        'count' => $user ? $user->unreadNotifications->count() : 0,
        // We use the property directly, then sort and filter using collection methods instead
        'notifications' => $user ? $user->notifications->sortByDesc('created_at')->take(5)->values() : []
    ]);
})->name('admin.notifications.count');


<?php

namespace App\Http\Controllers;

use App\Mail\CancelAppointment;
use App\Models\EmergencyContact;
use App\Models\MedicalHistory;
use App\Models\Reservation;
use App\Models\TimeSlot;
use App\Models\Treatment;
use App\Notifications\PatientNumberNotif;
use App\Notifications\ReservationConfirmed;
use App\Notifications\ReservationPending;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Illuminate\Validation\Rule;


class ReservationController extends Controller
{
    public function store()
    {
        try {
            $validation = request()->validate([
                'patient_number' => 'required|string|max:255',
                'appointment_number' => 'required|string|max:255',
                "age" => 'required|string|max:255',
                "address" => 'required|string|max:255',
                'firstname' => 'required|string|max:255',
                'lastname' => 'required|string|max:255',
                'middlename' => 'nullable|string|max:255',
                'extension_name' => 'nullable|string|max:255',
                'phone_number' => 'required|string|max:255',
                'email' =>  'required|string|indisposable',
                'emergency_name' => 'required|string|max:255',
                'emergency_contact' => 'required|string|max:255',
                'emergency_relationship' => 'required|string|max:255',
                'reservation_date' => 'required|date',
                'time_slot' => 'required|string|max:255',
                'treatment_choice' => 'required|string|max:255',
                'medical_history' => 'required|string|max:255',
                'medical_description' => [
                    'nullable',
                    'string',
                    'max:255',
                    Rule::requiredIf(function () {
                        return request()->input('medical_history') === 'Yes';
                    }),
                ],
            ]);
            
            // ─── LOCAL REAL-TIME MAIL TRACKING LAYER ─────────────────────────────────────
            $emailToCheck = $validation['email'];

            try {
                // We target the exact REPUTATION subdomain you tested in your browser
                $response = \Illuminate\Support\Facades\Http::timeout(6)
                    ->withoutVerifying() // Bypasses local SSL cURL issues on localhost
                    ->get('https://emailreputation.abstractapi.com/v1/', [
                        'api_key' => '325d46e49b954b68897661d6f1ebe51f',
                        'email'   => $emailToCheck
                    ]);

                if ($response->successful()) {
                    $data = $response->json();
                    
                    // Mapping exactly to your screenshot: $data['email_deliverability']['status']
                    if (
                        isset($data['email_deliverability']['status']) && 
                        $data['email_deliverability']['status'] === 'undeliverable'
                    ) {
                        return redirect()->back()
                            ->withInput() // Retains user input in your DaisyUI form
                            ->with('failed', "Our mail system tracking checked '{$emailToCheck}' and confirmed this inbox does not exist. Please use a valid email address.");
                    }
                } else {
                    // Fallback catch if the API keys are mismatched or server is down
                    \Illuminate\Support\Facades\Log::error("AbstractAPI returned an unhandled status code: " . $response->status());
                }

            } catch (\Exception $e) {
                // Log any local network connection problems safely to avoid breaking the application flow
                \Illuminate\Support\Facades\Log::error("Email validation network error: " . $e->getMessage());
            }


             $existing_patient = Reservation::where("email", $validation["email"])->first();

            // If any existing patient found, return appropriate error message
            if ($existing_patient) {

                Notification::route('mail', $existing_patient->email)->notify(new PatientNumberNotif($existing_patient));
                $message = "We identified that you already have a record in our appointment system based on your email address. Please check your registered email for your Patient Number and use the 'Existing Patient' option to make your appointment.";
                
                return redirect()->back()->with("failed", $message);
            }
            

             $reservation = Reservation::create([
                "patient_number" => $validation["patient_number"],
                "firstname" => $validation["firstname"],
                "lastname" => $validation["lastname"],
                "middlename" => $validation["middlename"] ?? '',
                "extensionname" => $validation["extension_name"] ?? '',
                "phone_number" => $validation["phone_number"],
                "email" => $validation["email"],
                "age" => $validation["age"],
                "address" => $validation["address"],
                "emergency_name" => $validation["emergency_name"],
                "emergency_contact" => $validation["emergency_contact"],
                "emergency_relationship" => $validation["emergency_relationship"],
            ]);

            $timeslot = TimeSlot::create([
                'date' => $validation["reservation_date"],
                "time_range" => $validation["time_slot"],
                "is_occupied" => 1,
                "treatment_choice" => $validation["treatment_choice"],
                "appointment_number" => $validation["appointment_number"],
                "description" => $validation["medical_description"] ?? '',
                "medical_history" => $validation["medical_history"],
                "reservation_id" => $reservation->id
            ]);

            $admin = \App\Models\User::where('username', '@Jlencina30')->first();
            if ($admin) {
                $admin->notify(new \App\Notifications\ClinicNotification(
                    'New Appointment Request',
                    "Patient booked appointment #{$timeslot->appointment_number} for {$timeslot->treatment_choice} on {$timeslot->date}."
                ));
            }
            
            
            
            Notification::route('mail', $validation['email']) // the email entered by the user
            ->notify(new ReservationPending($reservation, $timeslot));
            return redirect()->route("patient.create")->with("success", "Appointment created successfully.");
    
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Capture validation errors
            return redirect()->back()
            ->withErrors($e->errors());
        }
    }

     public function existingPatient()
    {
        try {
            $validation = request()->validate([
                'patient_number' => 'required|string|max:255',
                'appointment_number' => 'required|string|max:255',
                'reservation_date' => 'required|date',
                'time_slot' => 'required|string|max:255',
                'treatment_choice' => 'required|string|max:255',
                'medical_history' => 'required|string|max:255',
                'medical_description' => [
                    'nullable',
                    'string',
                    'max:255',
                    Rule::requiredIf(function () {
                        return request()->input('medical_history') === 'Yes';
                    }),
                ],
            ]);


            $app_number = TimeSlot::where('appointment_number', $validation["appointment_number"])->first();

            if ($app_number){
                return redirect()->back()->with("failed", "The Appointment Number is already been taken");
            }

            // Get the reservation (patient) by patient number
            $reservation = Reservation::where('patient_number', $validation["patient_number"])->first();

            if (!$reservation) {
                return redirect()->back()->with("failed", "There is no patient number that exists like that.");
            }

            // ✅ Check if the patient (via reservation_id) has 3 or more appointments today
            $appointmentsToday = TimeSlot::where('reservation_id', $reservation->id)
                ->whereIn('reservation_status', ['pending', 'approved'])
                ->whereDate('created_at', now()->toDateString())
                ->count();

            if ($appointmentsToday >= 3) {
                return redirect()->back()->with("failed", "You have reached the maximum of 3 appointments today. Try again tomorrow.");
            }


            $timeslot = TimeSlot::create([
                'date' => $validation["reservation_date"],
                "time_range" => $validation["time_slot"],
                "is_occupied" => 1,
                "treatment_choice" => $validation["treatment_choice"],
                "appointment_number" => $validation["appointment_number"],
                "description" => $validation["medical_description"] ?? '',
                "medical_history" => $validation["medical_history"],
                "reservation_id" => $reservation->id
            ]);

              $admin = \App\Models\User::where('username', '@Jlencina30')->first();
            if ($admin) {
                $admin->notify(new \App\Notifications\ClinicNotification(
                    'New Appointment Request',
                    "Patient booked appointment #{$timeslot->appointment_number} for {$timeslot->treatment_choice} on {$timeslot->date}."
                ));
            }
            
            
            
            Notification::route('mail', $reservation->email) // the email entered by the user
            ->notify(new ReservationPending($reservation, $timeslot));
            return redirect()->route("patient.create")->with("success", "Appointment created successfully.");
    
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Capture validation errors
            return redirect()->back()
            ->withErrors($e->errors())
            ->with('failed', 'Please fill out all required fields correctly.');
        }
    }

    public function confirm($reservationId)
    {
        // Find the reservation by ID
        $reservation = Reservation::findOrFail($reservationId);
    
        // Check if the reservation is already confirmed or if it's pending
        if ($reservation->reservation_status == 'pending') {
            $reservation->reservation_status = 'pending';
            $reservation->save();
    
            // Send confirmation email to the patient
            Notification::route('mail', $reservation->email)  // Using the email from the reservation
                ->notify(new ReservationConfirmed($reservation));
    
            return redirect()->route('home')->with('success', 'Your appointment has been confirmed.');
        }
    
        // If it's already confirmed, show a message
        return redirect()->route('home')->with('error', 'This appointment has already been confirmed.');
    }

   public function cancelAppointment($appointmentNumber)
{
    $timeslot = TimeSlot::where('appointment_number', $appointmentNumber)->with('reservation')->firstOrFail();
    $treatments = Treatment::all();
    return view('homepage', [
        'timeslot' => $timeslot,
        'treatments' => $treatments,
        'cancellation' => true,
    ]);
}
    public function cancelApp(Request $request)
{
    $validate = request()->validate([
     'reason' => 'required|string',
    'appointment_number' => 'required|string',
    ]);

    $timeslot = TimeSlot::where("appointment_number", $validate['appointment_number'])->first();

// 2. Check if the timeslot record exists
if (!$timeslot) {
    return redirect()->route('home')->with('error', 'Your appointment number is incorrect.');
}

// 3. Check if it's already cancelled
if ($timeslot->reservation_status === 'cancelled') {
    return redirect()->route('home')->with('error', 'The appointment is already cancelled.');
}

// 4. Update the cancellation status and free up the slot immediately on this row!
$timeslot->reservation_status = 'cancelled';
$timeslot->is_occupied = 0; // Since everything is in this table, free it right here
$timeslot->save();

$admin = \App\Models\User::where('username', '@Jlencina30')->first();
if ($admin) {
    $admin->notify(new \App\Notifications\ClinicNotification(
        'Appointment Cancelled',
        "Appointment #{$timeslot->appointment_number} has been cancelled by the patient. Reason: " . $validate['reason']
    ));
}

// 5. Send the cancellation email cleanly
// We access the patient's email and details through the 'reservation' relationship on your TimeSlot model
if ($timeslot->reservation) {
    Mail::to($timeslot->reservation->email)->send(new CancelAppointment(
        $validate['reason'], 
        $timeslot->date, 
        $timeslot->time_range,        // Direct column on timeslot
        $timeslot->treatment_choice,   // Direct column on timeslot
        $timeslot->appointment_number, 
        $timeslot->reservation->patient_number // Pulled from the related table
    ));
}

return redirect()->route('home')->with('success', 'Your appointment has been successfully cancelled.');
}






}


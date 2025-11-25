<?php

namespace App\Http\Controllers;

use App\Mail\ApproveEmail;
use App\Mail\ApproveReservation;
use App\Mail\RejectAppointment;
use App\Mail\CancelAppointment;
use App\Models\ClinicHours;
use App\Models\Reservation;
use App\Models\TimeSlot;
use App\Models\Treatment;
use App\Models\User;
use App\Notifications\ReservationPending;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class AdminController extends Controller
{

    public function storeClinicHours(Request $request){
         $request->validate([
        'start_time' => 'required|date_format:H:i',
        'end_time'   => 'required|date_format:H:i|after:start_time',
    ]);

    $slot = ClinicHours::where("start_time", $request->input('start_time'))
    ->where("end_time", $request->input('end_time'));

    if($slot){
        redirect()->back()->with('failed', 'Time slot is already set!');
    }

    // Convert the submitted times to Carbon objects
    $startTime = Carbon::createFromFormat('H:i', $request->input('start_time'));
    $endTime = Carbon::createFromFormat('H:i', $request->input('end_time'));

    // Automatically generate the time_range string
    $timeRange = $startTime->format('gA') . '-' . $endTime->format('gA');
    // For example, '08:00' to '09:00' becomes '8AM-9AM'

    ClinicHours::create([
        'start_time' => $startTime,
        'end_time'   => $endTime,
        'time_range' => $timeRange,
    ]);

    return redirect()->route("admin.timeSlots")->with('success', 'Time slot created successfully!');
    }

    public function viewTime() {
         $query = ClinicHours::orderBy('created_at');

         $clinicHours = $query->paginate(5);

        return view("admin.timeSlots", compact("clinicHours"));
    }

    public function editTime(ClinicHours $id) {
        return view("admin.trackTime", compact("id"));
    }

    public function addTime(){
        return view("admin.addTime");
    }

    public function deleteTime(ClinicHours $id){
        $id->delete();

        return redirect()->route("admin.timeSlots")->with('success', 'Time slot deleted successfully!');
    }

    public function updateTime(Request $request, ClinicHours $id)
{
    // The validation should remain outside the try-catch block
    // as it handles common input errors and Laravel manages the redirection.
    $request->validate([
        'start_time' => 'required|date_format:H:i',
        'end_time'   => 'required|date_format:H:i|after:start_time',
    ]);

    try {
        // Convert the submitted times to Carbon objects
        $startTime = Carbon::createFromFormat('H:i', $request->input('start_time'));
        $endTime = Carbon::createFromFormat('H:i', $request->input('end_time'));

        // Automatically generate the time_range string
        $timeRange = $startTime->format('gA') . '-' . $endTime->format('gA');

        $id->update([
            'start_time' => $startTime,
            'end_time'   => $endTime,
            'time_range' => $timeRange,
        ]);

        return redirect()->route("admin.timeSlots")->with('success', 'Time slot updated successfully!');

    } catch (\Throwable $th) {
        // Log the exception for detailed server-side debugging
        Log::error("Error updating time slot: " . $th->getMessage());
        
        // Return to the previous page with a failure message and the error details
        return redirect()->back()->with('failed', 'Time slot update failed: ' . $th->getMessage());
    }
}

    public function reschedAppointment(Reservation $id){
        return view("admin.addAppointment", compact("id"));
    }

    public function newAppointment(){
        return view("admin.newAppointment");
    }

    public function newReservation(Request $request) {
         $today = Carbon::today()->format('Y-m-d');
        $endDate = Carbon::today()->addMonths(1)->format('Y-m-d');

        // Fetch all clinic time slots, sorted by start_time
        $allClinicTimeSlots = ClinicHours::orderBy('start_time')->pluck('time_range');

        // Fetch all appointments for the next month
        $appointments = TimeSlot::whereBetween('date', [$today, $endDate])->get();

        $dates = [];
        $period = CarbonPeriod::create($today, $endDate);

        foreach ($period as $date) {
            $dateFormatted = $date->format('Y-m-d');
            $appointmentsForDate = $appointments->where('date', $dateFormatted);
            
            if ($appointmentsForDate->isNotEmpty()) {
                $bookedTimeSlots = $appointmentsForDate->pluck('time_range')->toArray();
                $isFullyBooked = collect($allClinicTimeSlots)->diff($bookedTimeSlots)->isEmpty();
                $dates[$dateFormatted] = $isFullyBooked ? 'fully-booked' : 'available';
            } else {
                $dates[$dateFormatted] = 'available';
            }
        }
        
        $timeSlots = [];
        if ($request->has('reservation_date')) {
            $selectedDate = $request->input('reservation_date');
            $appointmentsForDate = TimeSlot::where('date', $selectedDate)->get();

            // Fetch and sort clinic time slots by start_time for the selected day
            $clinicTimeSlots = ClinicHours::orderBy('start_time')->get();

            foreach ($clinicTimeSlots as $slot) {
                $isOccupied = $appointmentsForDate->where('time_range', $slot->time_range)->isNotEmpty();
                $timeSlots[$slot->time_range] = ['is_occupied' => $isOccupied];
            }
        }

        $treatments = Treatment::orderBy("created_at")->get();

        do {
            $randomNumber = str_pad(mt_rand(0, 99999999), 8, '0', STR_PAD_LEFT);
            $generatedPatientNumber = 'P-' . $randomNumber;
        } while (Reservation::where('patient_number', $generatedPatientNumber)->exists());
        
        do {
            $randomNumber = str_pad(mt_rand(0, 99999999), 8, '0', STR_PAD_LEFT);
            $generatedAppointmentNumber = 'APP-' . $randomNumber;
        } while (TimeSlot::where('appointment_number', $generatedAppointmentNumber)->exists());
        
        return view('admin.newAppointment', [
            "dates" => $dates,
            "timeSlots" => $timeSlots,
            "today" => $today,
            "endDate" => $endDate,
            "treatments" => $treatments,
            "patient_number" => $generatedPatientNumber,
            "appointment_number" => $generatedAppointmentNumber,
        ]);
        
    }


    public function createReservations(Request $request, Reservation $id) {
        $today = Carbon::today()->format('Y-m-d');
        $endDate = Carbon::today()->addMonths(1)->format('Y-m-d');

        // Fetch all clinic time slots, sorted by start_time
        $allClinicTimeSlots = ClinicHours::orderBy('start_time')->pluck('time_range');

        // Fetch all appointments for the next month
        $appointments = TimeSlot::whereBetween('date', [$today, $endDate])->get();

        $dates = [];
        $period = CarbonPeriod::create($today, $endDate);

        foreach ($period as $date) {
            $dateFormatted = $date->format('Y-m-d');
            $appointmentsForDate = $appointments->where('date', $dateFormatted);
            
            if ($appointmentsForDate->isNotEmpty()) {
                $bookedTimeSlots = $appointmentsForDate->pluck('time_range')->toArray();
                $isFullyBooked = collect($allClinicTimeSlots)->diff($bookedTimeSlots)->isEmpty();
                $dates[$dateFormatted] = $isFullyBooked ? 'fully-booked' : 'available';
            } else {
                $dates[$dateFormatted] = 'available';
            }
        }
        
        $timeSlots = [];
        if ($request->has('reservation_date')) {
            $selectedDate = $request->input('reservation_date');
            $appointmentsForDate = TimeSlot::where('date', $selectedDate)->get();

            // Fetch and sort clinic time slots by start_time for the selected day
            $clinicTimeSlots = ClinicHours::orderBy('start_time')->get();

            foreach ($clinicTimeSlots as $slot) {
                $isOccupied = $appointmentsForDate->where('time_range', $slot->time_range)->isNotEmpty();
                $timeSlots[$slot->time_range] = ['is_occupied' => $isOccupied];
            }
        }

        $treatments = Treatment::orderBy("created_at")->get();

        do {
            $randomNumber = str_pad(mt_rand(0, 99999999), 8, '0', STR_PAD_LEFT);
            $generatedPatientNumber = 'P-' . $randomNumber;
        } while (Reservation::where('patient_number', $generatedPatientNumber)->exists());
        
        do {
            $randomNumber = str_pad(mt_rand(0, 99999999), 8, '0', STR_PAD_LEFT);
            $generatedAppointmentNumber = 'APP-' . $randomNumber;
        } while (TimeSlot::where('appointment_number', $generatedAppointmentNumber)->exists());

        return view('admin.addAppointment',
        [
            "dates" => $dates,
            "timeSlots" => $timeSlots,
            "today" => $today, 
            "endDate" => $endDate,
            "treatments" => $treatments,
            "id" => $id,
              "patient_number" => $generatedPatientNumber,
            "appointment_number" => $generatedAppointmentNumber,
        ]);
        
    }

    public function storeAppointment()
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
                'email' => 'required|string|email|max:255',
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

             $app_number = TimeSlot::where('appointment_number', $validation["appointment_number"])->first();
            $p_number = Reservation::where("patient_number", $validation["patient_number"])->first();

            if ($app_number){
                return redirect()->back()->with("error", "The Appointment Number is already been taken");
            }

            if ($p_number){
                return redirect()->back()->with("error", "The Patient Number is already been taken");
            }

           // Check for existing patient by name, email, or phone number
            $existing_patient = null;
            $conflict_type = '';

            // Check by name first
            $p_name = Reservation::where("firstname", $validation["firstname"])
                ->where("lastname", $validation["lastname"])
                ->first();

            if ($p_name) {
                $existing_patient = $p_name;
                $conflict_type = 'name';
            }

            // Check by email if no name conflict found
            if (!$existing_patient) {
                $email = Reservation::where("email", $validation["email"])->first();
                if ($email) {
                    $existing_patient = $email;
                    $conflict_type = 'email';
                }
            }

            // Check by phone number if no previous conflicts found
            if (!$existing_patient) {
                $phone_number = Reservation::where("phone_number", $validation["phone_number"])->first();
                if ($phone_number) {
                    $existing_patient = $phone_number;
                    $conflict_type = 'phone number';
                }
            }

            // If any existing patient found, return appropriate error message
            if ($existing_patient) {
                $patient_number = $existing_patient->patient_number;
                $message = "We identified that you already have a record in our appointment system based on your {$conflict_type}. Your patient number is {$patient_number}. Please use the 'Existing Patient' option to make your appointment.";
                
                return redirect()->back()->with("error", $message);
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
            
            
           
            
            Notification::route('mail', $validation['email']) // the email entered by the user
            ->notify(new ReservationPending($reservation, $timeslot));
            return redirect()->route("admin.records")->with("success", "Appointment created successfully.");
    
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Capture validation errors
            return redirect()->back()
            ->withErrors($e->errors())
            ->with('error', 'Please fill out all required fields correctly.');
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

            $reservation = Reservation::where('patient_number', $validation["patient_number"])->first();

             if (!$reservation){
                return redirect()->back()->with("failed", "There is no patient number exists like that.");
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
            
            
            
            Notification::route('mail', $reservation->email) // the email entered by the user
            ->notify(new ReservationPending($reservation, $timeslot));
            return redirect()->route("admin.appointments")->with("success", "Appointment created successfully.");
    
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Capture validation errors
            return redirect()->back()
            ->withErrors($e->errors())
            ->with('failed', 'Please fill out all required fields correctly.');
        }
    }


    public function appointments() {
         
        $query = TimeSlot::with('reservation')
            ->join('reservations', 'time_slots.reservation_id', '=', 'reservations.id')
            ->where('time_slots.reservation_status', 'pending');

        if (request()->has('search')) {
            $search = request()->input('search');
            $query->where('reservations.firstname', 'like', "%{$search}%");
        }

        $reservations = $query->orderBy('reservations.firstname', 'asc')
                           ->select('time_slots.*')
                           ->paginate(6)
                           // THIS IS THE FIX: Use appends(request()->query())
                           ->appends(request()->query()); // <-- This will work universally

        return view("admin.appointments", compact("reservations"));
    }

    public function forApprovalAppointments() {
       $query = Reservation::with(['timeSlots' => function ($query) {
                $query->where('reservation_status', 'pending');
            }])
            ->whereHas('timeSlots', function ($query) {
                $query->where('reservation_status', 'pending');
            })
            ->orderBy('firstname');

    if (request()->has('search')) {
        $search = request()->input('search');
        $query->where('firstname', 'like', "%{$search}%");
    }

    $reservations = $query->paginate(5);
    return view("admin.appointments", compact("reservations"));

    }


   public function ongoingAppointments() {
       $query = TimeSlot::with('reservation')
            ->join('reservations', 'time_slots.reservation_id', '=', 'reservations.id')
            ->where('time_slots.reservation_status', 'ongoing');

        if (request()->has('search')) {
            $search = request()->input('search');
            $query->where('reservations.firstname', 'like', "%{$search}%");
        }

        $reservations = $query->orderBy('reservations.firstname', 'asc')
                           ->select('time_slots.*')
                           ->paginate(6)
                           // THIS IS THE FIX: Use appends(request()->query())
                           ->appends(request()->query()); // <-- This will work universally

        return view("admin.appointments", compact("reservations"));
}

    public function trackAppointment(TimeSlot $id) {
          $reservation = $id->reservation; // get the related reservation

    return view('admin.trackReservation', [
        'slot' => $id,
        'reservation' => $reservation
    ]);
    }

    public function patientHistory(Reservation $id)
{
    $appointmentHistory = $id->timeSlots()
        ->whereIn('reservation_status', ['cancelled', 'completed', 'no-show'])
        ->orderByDesc('date')
        ->paginate(5);


    $name = $id->firstname . " " . $id->middlename .  " " . $id->lastname . " " .  $id->extensionname ;

    $patientID = $id->id;

    return view("admin.patientHistory", compact("appointmentHistory", "name", "patientID"));
}

    public function generateReport(Reservation $id)
{
    $appointments = $id->timeSlots()
        ->whereIn('reservation_status', ['cancelled', 'completed', 'no-show'])
        ->orderByDesc('date')
        ->get();

    $name = $id->firstname . " " . $id->middlename .  " " . $id->lastname . " " .  $id->extensionname;

    $currentDate = Carbon::now()->format('F j, Y'); 
    
    $pdf = Pdf::loadView('reports.appointment_history', compact('appointments', 'name', 'id', 'currentDate'));
     // Generate a clean filename from the patient's name
    $filename = Str::slug($name, '-') . '_appointment_history.pdf';

    // Download the PDF with the new filename
    return $pdf->download($filename);
}

   public function generateClinicReport(Request $request)
{
    // 1. Get the start and end dates from the request.
    // If no dates are provided, default to a sensible range (e.g., last 30 days).
    $startDate = Carbon::parse($request->input('start_date'))->startOfDay();
    $endDate = Carbon::parse($request->input('end_date'))->endOfDay();

    // 2. Query the appointments table using the whereBetween method.
    // Make sure to query the main Appointment model, not a single patient's relationship.
    $appointments = TimeSlot::whereBetween('date', [$startDate, $endDate])
                               ->whereIn('reservation_status', ['cancelled', 'completed', 'no-show'])
                               ->orderByDesc('date')
                               ->get();

    // 3. Pass the appointments and date range to the view.
    $currentDate = Carbon::now()->format('F j, Y');

    $pdf = Pdf::loadView('reports.clinic_appointment_history', compact('appointments', 'currentDate', 'startDate', 'endDate'));
    
    // 4. Create a dynamic filename for the PDF.
    $filename = 'clinic_report_from_' . $startDate->format('Y-m-d') . '_to_' . $endDate->format('Y-m-d') . '.pdf';

    // 5. Download the PDF.
    return $pdf->download($filename);
}


    public function approveReservation(TimeSlot $id) {

        $id->update([
        "reservation_status" => "ongoing"
        ]);
        $message = "Good day! Your Appointment is finally approved. Please be reminded with your upcoming appointment.";
        $date = $id->date;
        $time = $id->time_range;
        $treatment = $id->treatment_choice;
        $appointment_number = $id->appointment_number;
        $patient_number = $id->reservation->patient_number;

        Mail::to($id->reservation->email)->send(new ApproveReservation($message, $date, $time, $treatment, $appointment_number, $patient_number));

        return redirect()->route("admin.appointments")->with("success", "The appointment is finally approved!");
    }

    public function rejectReservation(TimeSlot $id) {
        
        $validate = request()->validate([
           "reason" => "required|string|" 
        ]);

        $message = $validate["reason"];
        $date = $id->date;
        $time = $id->time_range;
        $treatment = $id->treatment_choice;
        $appointment_number = $id->appointment_number;
        $patient_number = $id->reservation->patient_number;

        $id->delete();

        Mail::to($id->reservation->email)->send(new RejectAppointment($message, $date, $time, $treatment,$appointment_number, $patient_number));
        return redirect()->route("admin.appointments")->with("success", "The appointment is rejected!");
    }

    public function cancelAppointment(Timeslot $id){
         $validate = request()->validate([
           "reason" => "required|string|" 
        ]);

        $message = $validate["reason"];
        $date = $id->date;
        $time = $id->time_range;
        $treatment = $id->treatment_choice;
        $appointment_number = $id->appointment_number;
        $patient_number = $id->reservation->patient_number;

         $id->update([
            "reservation_status" => "canceled"
            ]);
        
        Mail::to($id->reservation->email)->send(new CancelAppointment($message, $date, $time, $treatment,$appointment_number, $patient_number));
        return redirect()->route("admin.appointments")->with("success", "The appointment is canceled!");
    }

    public function completeReservation(TimeSlot $id) {

            $id->update([
            "reservation_status" => "completed"
            ]);

            return redirect()->route("admin.appointments")->with("success", "The appointment is finally completed!");
    }

    public function noshowReservation(TimeSlot $id) {

        $id->update([
            "reservation_status" => "no-show"
            ]);

        return redirect()->route("admin.appointments")->with("success", "The appointment changed to no show!");
    }
    
   public function completedRecords()
{
    $query = Reservation::withCount([
        'timeSlots as time_slots_count' => function ($query) {
            $query->whereIn('reservation_status', ['cancelled', 'completed', 'no-show']);
        }
    ]);

    if (request()->has('search')) {
        $search = request()->input('search');
        $query->where('firstname', 'like', "%{$search}%");
    }

    $query->orderBy('firstname', 'asc');

    $appointmentHistory = $query->paginate(5);

    return view("admin.patientHistory", compact("appointmentHistory"));
}


    public function noshowRecords() {

            $query = Reservation::with('timeSlots') // Eager load the related timeSlot
            ->whereHas('timeSlots', function ($query) {
                // Filter reservations where the related timeSlot has 'no-show' status
                $query->where('reservation_status', 'no-show');
            })
            ->orderBy('firstname');

        // Apply search filter if 'search' input is provideds
        if (request()->has('search')) {
            $search = request()->input('search');
            $query->where('firstname', 'like', "%{$search}%");
        }
        
        // Execute the query to retrieve the filtered results
        $reservations = $query->paginate(5);
    
        return view("admin.patientHistory", compact("reservations"));
    }

    public function deleteReservationAppointment(TimeSlot $id) {
        $id->delete();

         return redirect()->route("admin.appointments")->with("success", "The appointment is successfully deleted!");
    }

    public function deleteRecordAppointment (TimeSlot $id){
        $id->delete();

         return redirect()->route("admin.records")->with("success", "The appointment is successfully deleted!");
    }


    public function profile(){
        return view("admin.profile");
    }
    
    public function editProfile(User $id) {
        return view('admin.editProfile', compact('id'));
    }

    public function updateProfile(User $id) {
        try {
            
            $validation = request()->validate([
                "username" => "string|required|min:10",
                "email" => [
                "required",
                "email",
                Rule::unique('users', 'email')->ignore($id->id),
            ],
            ], [
                'email.unique' => 'The email address is already registered. Please use a different email address.' // Custom error message for unique email
            ]);

            $id->update([
                "username" => $validation["username"],
                "email" => $validation["email"]
            ]);

            return redirect()->route("admin.profile")->with("success", "Profile updated successfully");

        }catch(\Illuminate\Validation\ValidationException $e){
            return redirect()->back()
            ->withErrors($e->errors())
            ->with('failed', 'Please fill out all required fields correctly.');
        }
    }

    public function updatePassword(User $id) {
        try {
            
            $validation = request()->validate([
                "current_password" => "string|required",
                "new_password" => [
                    'required',
                    'string',
                    'min:8',
                    'regex:/[a-z]/', // must contain at least one lowercase letter
                    'regex:/[A-Z]/', // must contain at least one uppercase letter
                    'regex:/[0-9]/', // must contain at least one number
                    'regex:/[@$!%*?&#]/',
                    'confirmed'
                ],
                 // Optional field with validation for image file
            ], [
                'password.regex' => 'Password must contain at least one lowercase letter, one uppercase letter, one number, and one special character.',
                'email.unique' => 'The email address is already registered. Please use a different email address.' // Custom error message for unique email
            ]);

               // Check if the current password matches the user's stored password
                if (!Hash::check($validation["current_password"], $id->password)) {
                    return redirect()->back()
                        ->with('failed', 'The current password does not match our records.');
                };
                
                $id->update([
                   "password" => Hash::make($validation["new_password"]), 
                ]);

            return redirect()->route("admin.profile")->with("success", "Password updated successfully");

        }catch(\Illuminate\Validation\ValidationException $e){
            return redirect()->back()
            ->withErrors($e->errors())
            ->with('failed', 'Please fill out all required fields correctly.');
        }
    }
    

    public function editPassword(User $id) {
        return view("admin.updatePassword", compact("id"));
    }
    
}

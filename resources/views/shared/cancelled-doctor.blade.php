<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Cancelled: Appointment #{{$mailappointment}}</title>
</head>
<body>
    
    <h3>REASON OF CANCELLATION: {{$mailmessage}}</h3>
    <br>
    <p>NOTICE: Your upcoming appointment has been cancelled by the clinic.</p>
    <p>APPOINTMENT DETAILS:</p>
    <p class="text-lg">APPOINTMENT NUMBER: <span class="font-bold">{{$mailappointment}}</span></p>
    <p class="text-lg">PATIENT NUMBER: <span class="font-bold">{{$mailpatient}}</span></p>
    <p class="text-lg">DATE: <span class="font-bold">{{\Carbon\Carbon::parse($maildate)->format('F j, Y')}}</span></p>
    <p class="text-lg">TIME: <span class="font-bold">{{$mailtime}}</span></p>
    <p class="text-lg">TREATMENT: <span class="font-bold">{{$mailtreatment}}</span></p>
    <br>
    <p>If you want to make an appointment again into our clinic, just reply to this email for your desired schedule or visit our website below and make a new appointment to our clinic.Sorry for the inconvenience.</p>
    <p>{{ route('home') }}</p>
    <br>
    <p>Best Regards,</p>
    <h3>ESPINELI-PARADEZA DENTAL CLINIC</h3>
</body>
</html>
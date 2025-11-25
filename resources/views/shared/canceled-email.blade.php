<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Your Appointment is Canceled!</title>
</head>
<body>
    
    <h3>{{$mailmessage}}</h3>
    <br>
    <p class="text-lg">DATE: <span class="font-bold">{{\Carbon\Carbon::parse($maildate)->format('F j, Y')}}</span></p>
    <p class="text-lg">TIME: <span class="font-bold">{{$mailtime}}</span></p>
    <p class="text-lg">TREATMENT: <span class="font-bold">{{$mailtreatment}}</span></p>
    <br>
    <p>If you want to make an appointment again into our clinic, just click the link provided below.</p>
    <p>{{ route('patient.create') }}</p>
    <br>
    <p>Best Regards,</p>
    <h3>ESPINELI-PARADEZA DENTAL CLINIC</h3>
</body>
</html>
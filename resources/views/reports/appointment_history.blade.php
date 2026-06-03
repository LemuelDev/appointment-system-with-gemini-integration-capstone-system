<!DOCTYPE html>
<html>
<head>
    <title>Appointment History Report</title>
    <style>
        body { font-family: 'Arial', sans-serif; }
        h1, h3 { text-align: center; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        #patient {display: grid; grid-column: 1fr 1fr ; justify-content: space-between; align-items: center; gap: 1rem; padding: 1rem;}
        #patient h4 span {font-weight: 400;}
        #date, #time {min-width: 100px;}
        #app {min-width: 120px;}
    </style>
</head>
<body>
    <h1>Espineli-Paradeza Dental Clinic <span></span></h1>
    <h3>Appointment History Report</h3>
   
    <div id="patient">
        <h4>Name: <span>{{$name}}</span></h4>
        <h4>Patient Number: <span>{{$id->patient_number}}</span></h4>
        <h4>Phone Number: <span>{{$id->phone_number}}</span></h4>
        <h4>Email Address: <span> {{$id->email}}</span></h4>
        <h4>Date Report: <span> {{$currentDate}}</span> </h4>
    </div>

    <table>
        <thead>
            <tr>
                <th>Appointment Number</th>
                <th>Treatment</th>
                <th>Date</th>
                <th>Time</th>
                <th>Status</th>
                <th>Medical History</th>
                <th>Medical Description</th>
            </tr>
        </thead>
        <tbody>
            @foreach($appointments as $appointment)
            <tr>
                <td id="app">{{$appointment->appointment_number}}</td>
                <td>{{$appointment->treatment_choice}}</td>
                <td id="date">{{ \Carbon\Carbon::parse($appointment->date)->format('F j, Y') }}</td>
                <td id="time">{{$appointment->time_range}}</td>
                <td>{{$appointment->reservation_status}}</td>
                <td>{{$appointment->medical_history}}</td>
                 <td>{{ !empty($appointment->description) ? $appointment->description : 'N/A' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div style="margin-top: 50px; text-align: center;">
    <p style="margin-bottom: 40px;">Approved by:</p>
    <p style="font-weight: bold; margin: 0;">Dr. Ma. Elena Espineli-Paradeza, DMD</p>
    <p style="font-size: 0.9em; color: #555; margin: 0;">Clinic Director / Attending Dentist</p>
    <p style="font-weight: bold; margin-top: 5px;">Espineli-Paradeza Dental Clinic</p>
</div>
</body>
</html>
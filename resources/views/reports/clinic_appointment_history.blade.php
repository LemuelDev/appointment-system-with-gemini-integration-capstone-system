<!DOCTYPE html>
<html>
<head>
    <title>Appointment History Report</title>
    <style>
        body { 
            font-family: 'Arial', sans-serif;
            font-size: 10px; /* Reduced font size for more content to fit */
        }
        h1, h3 { 
            text-align: center; 
        }
        table { 
            width: 100%; 
            border-collapse: collapse; 
            margin-top: 10px; 
            table-layout: fixed; /* Fix table layout to respect column widths */
        }
        th, td { 
            border: 1px solid #ddd; 
            padding: 5px; /* Reduced padding to save space */
            text-align: left; 
            word-wrap: break-word; /* Crucial for long text in cells */
            overflow: hidden; /* Prevents content from overflowing cells */
        }
        th { 
            background-color: #f2f2f2; 
        }
        #patient {
            display: grid;
            grid-template-columns: 1fr 1fr;
            justify-content: space-between;
            align-items: center;
            gap: 1rem;
            padding: 1rem;
        }
        #patient h4 span {
            font-weight: 400;
        }
        /* New CSS for column widths */
        th:nth-child(1), td:nth-child(1) { width: 15%; } /* Patient Name */
        th:nth-child(2), td:nth-child(2) { width: 15%; } /* Appointment Number */
        th:nth-child(3), td:nth-child(3) { width: 10%; } /* Treatment */
        th:nth-child(4), td:nth-child(4) { width: 12%; } /* Date */
        th:nth-child(5), td:nth-child(5) { width: 11%; } /* Time */
        th:nth-child(6), td:nth-child(6) { width: 8%; } /* Status */
        th:nth-child(7), td:nth-child(7) { width: 8%; } /* Medical History */
        th:nth-child(8), td:nth-child(8) { width: 10%; } /* Medical Description (given more space) */
    </style>
</head>
<body>
    <h1>Espineli-Paradeza Dental Clinic <span></span></h1>
    <h3>Appointment History Report</h3>
    
    <div id="patient">
        <h4>Date of Report Generated: <span>{{$currentDate}}</span></h4>
        <h4>Start Date: <span>{{$startDate->format('F j, Y')}}</span></h4>
        <h4>End Date: <span>{{$endDate->format('F j, Y')}}</span></h4>
    </div>

    <table>
        <thead>
            <tr>
                <th>Patient Name</th> 
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
                <td>{{$appointment->reservation->firstname}} {{$appointment->reservation->middlename}} {{$appointment->reservation->lastname}} {{$appointment->reservation->extensionname}}</td>
                <td>{{$appointment->appointment_number}}</td>
                <td>{{$appointment->treatment_choice}}</td>
                <td>{{ \Carbon\Carbon::parse($appointment->date)->format('F j, Y') }}</td>
                <td>{{$appointment->time_range}}</td>
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
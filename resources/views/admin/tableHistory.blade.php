@if (count($appointmentHistory) > 0)
<div class="overflow-x-auto pt-7">
  <table class="table table-zebra">
    <!-- head -->
    <thead>
       <tr>
      <th class="text-lg text-center">Patient Number</th>
      <th class="text-lg text-center">Patient Name</th>
      <th class="text-lg text-center">Age</th>
      <th class="text-lg text-center">Address</th>
      <th class="text-lg text-center">Total Appointments</th>
      <th class="text-lg text-center">Action Taken</th>
    </tr>
    </thead>
    <tbody>
            @forelse ($appointmentHistory as $patient)
      <tr>
        <td class="text-center font-bold text-md">{{ $patient->patient_number }}</td>
        <td class="text-md text-center min-w-[100px]">
          {{ $patient->firstname }}
          {{ $patient->middlename }}
          {{ $patient->lastname }}
          {{ $patient->extensionname }}
        </td>
        <td class="text-center  text-md">{{ $patient->age }}</td>
        <td class="text-center  text-md">{{ $patient->address }}</td>
        <td class="text-center font-bold text-md">{{ $patient->time_slots_count }}</td>
        <td class="text-center">
            <a href="{{ route('admin.showPatient', $patient->id) }}" class="btn btn-accent">View</a>
        </td>
      </tr>
    @empty
      <tr>
        <td colspan="3" class="text-center text-gray-500">No appointment history found.</td>
      </tr>
    @endforelse

      
    </tbody>
  </table>
</div>
{{ $appointmentHistory->links() }}
@else
<div class="flex flex-col items-center justify-center pt-10">
    <div class="text-3xl font-bold  mb-4">No Appointments</div>
    <p class="text-xl mb-6">It looks like there are no Appointments at the moment.</p>
    {{-- <a href="{{route('patient.create')}}" class="px-6 py-3 bg-blue-500 text-white rounded-lg hover:bg-blue-600">
        Book a Reservation
    </a> --}}
</div>
@endif

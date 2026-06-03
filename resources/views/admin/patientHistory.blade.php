@extends('layout.admin')

@section('content')
<div class="flex h-screen" data-theme="light">
    <!-- Sidebar -->
    @include('admin.sidebar')

    <!-- Main content -->
    <div class="flex-1 flex flex-col w-full">
        <!-- Navbar -->
        @include('admin.navbar')

        <!-- Main content area -->
        <main class="flex-1 p-6 " id="main-content">
            <div class=" ">
                <!-- Your main content goes here -->
                <div class="flex max-md:flex-col justify-center max-sm:gap-5 sm:justify-between items-center">
                    <h1 class="lg:text-3xl text-2xl font-bold max-md:pb-3 ">Appointment History</h1>
                          <!-- Status Filter -->
                          <div class="flex items-center justify-center gap-4 max-sm:flex-col max-sm:pt-3">
                             @if (request()->route()->getName() !== "admin.showPatient")
                                <form action="{{request()->route()->getName() === 'admin.records' ? route('admin.records') : route('admin.noshowRecords')}}"
                                    method="GET">
                                    <input type="text" placeholder="Search Name" name="search" class="px-4 py-2 max-sm:w-full rounded-lg shadow-md border border-gray-500 bg-transparent">
                                    <button class="btn btn-primary text-white max-sm:w-full max-sm:mt-3">Search</button>
                                </form>
<button class="btn btn-accent text-black" onclick="my_modal_100.showModal()">Generate Report</button>

<dialog id="my_modal_100" class="modal">
    <div class="modal-box">
        <form method="dialog" id="close-form">
            <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2">✕</button>
        </form>
        <h3 class="text-lg font-bold">Generate Clinic Report</h3>
        <p class="py-4">Please select the date range for the report.</p>

        <form action="{{route('admin.generateClinicReport')}}" method="POST">
            @csrf
            <div class="form-control">
                <label class="label">
                    <span class="label-text font-bold">Start Date</span>
                </label>
                <input type="date" id="start_date" name="start_date" class="input input-bordered" required>
            </div>
            <div class="form-control mt-4">
                <label class="label">
                    <span class="label-text font-bold">End Date</span>
                </label>
                <input type="date" id="end_date" name="end_date" class="input input-bordered" required>
            </div>

            <div class="mt-6 flex flex-wrap gap-2 justify-center ">
                <button type="button" onclick="setLastWeek()" class="btn btn-outline btn-sm">Last Week</button>
                <button type="button" onclick="setLastMonth()" class="btn btn-outline btn-sm">Last Month</button>
                <button type="button" onclick="setLastThreeMonths()" class="btn btn-outline btn-sm">Last 2 Months</button>
                <button type="button" onclick="setLastTwoMonths()" class="btn btn-outline btn-sm">Last 3 Months</button>
                <button type="button" onclick="setLastSixMonths()" class="btn btn-outline btn-sm">Last 6 Months</button>
                    <button type="button" onclick="setLastYear()" class="btn btn-outline btn-sm">Last 1 Year</button>
            </div>

            <div class="modal-action">
                <button type="submit" class="btn btn-primary text-white">Generate Report</button>
            </div>
                </form>
            </div>
        </dialog>

            <script>
                function setDates(startDate, endDate) {
                    document.getElementById('start_date').value = startDate;
                    document.getElementById('end_date').value = endDate;
                }

                function setLastWeek() {
                    const today = new Date();
                    const lastWeek = new Date(today);
                    lastWeek.setDate(today.getDate() - 7);
                    setDates(lastWeek.toISOString().split('T')[0], today.toISOString().split('T')[0]);
                }

                function setLastMonth() {
                    const today = new Date();
                    const lastMonth = new Date(today);
                    lastMonth.setMonth(today.getMonth() - 1);
                    setDates(lastMonth.toISOString().split('T')[0], today.toISOString().split('T')[0]);
                }
                
                function setLastTwoMonths() {
                    const today = new Date();
                    const lastTwoMonths = new Date(today);
                    lastTwoMonths.setMonth(today.getMonth() - 2);
                    setDates(lastTwoMonths.toISOString().split('T')[0], today.toISOString().split('T')[0]);
                }

                 function setLastThreeMonths() {
                    const today = new Date();
                    const lastTwoMonths = new Date(today);
                    lastTwoMonths.setMonth(today.getMonth() - 3);
                    setDates(lastTwoMonths.toISOString().split('T')[0], today.toISOString().split('T')[0]);
                }

                 function setLastSixMonths() {
                    const today = new Date();
                    const lastTwoMonths = new Date(today);
                    lastTwoMonths.setMonth(today.getMonth() - 6);
                    setDates(lastTwoMonths.toISOString().split('T')[0], today.toISOString().split('T')[0]);
                }
                 function setLastYear() {
                    const today = new Date();
                    const lastTwoMonths = new Date(today);
                    lastTwoMonths.setMonth(today.getMonth() - 12);
                    setDates(lastTwoMonths.toISOString().split('T')[0], today.toISOString().split('T')[0]);
                }
            </script>
                            @else 
                                    @if (isset($name) && $name)
                                         <h1 class=" text-xl font-bold ">({{$name}})</h1>
                                         <a href="{{route('admin.patientDownload', $patientID)}}" class="btn btn-accent text-black">Print Patient Record</a>
                                    @endif
                                
                            @endif
                            
                           {{-- <select name="" id="filter-appointment" class="py-2 px-4 rounded-lg border border-slate-500">
                                <option value="{{route('admin.records')}}" {{request()->route()->getName() === 'admin.records' ? 'selected' : ''}}>Completed</option>
                                <option value="{{route('admin.noshowRecords')}}" {{request()->route()->getName() === 'admin.noshowRecords' ? 'selected' : ''}}>No-show</option>
                           </select>
                           <script>
                            // Get the select element
                            const filter = document.getElementById('filter-appointment');
                        
                            // Add an event listener for the change event
                            filter.addEventListener('change', function () {
                                // Redirect to the selected option's value (URL)
                                window.location.href = this.value;
                            });
                        </script> --}}
                          </div>
                </div>
            </div>
                 @if (request()->route()->getName() === "admin.showPatient")
                    @include('admin.tablePatients') 
                 @else
                    @include('admin.tableHistory') 
                 @endif
            </div>
                       
        </main>
       
    </div>
</div>

 <!-- Confirmation Modal -->
 <div class="fixed inset-0 z-50 overflow-y-auto hidden" id="deleteConfirmationModal" tabindex="-1" aria-labelledby="deleteConfirmationModalLabel" aria-hidden="true">
    <div class="flex items-center justify-center min-h-screen">
        <div class="bg-white rounded-lg shadow-xl max-w-lg mx-auto p-6">
            <div class="modal-header flex justify-start items-center py-1">
                <h5 class="text-lg font-medium" id="deleteConfirmationModalLabel">Confirm Deletion</h5>
            </div>
            <div class="modal-body my-4 text-red-500 py-4">
                Are you sure you want to delete this record? 
            </div>
            <div class="modal-footer flex justify-end gap-4">
                <button type="button" class="text-white py-2 px-6 bg-gray-500 hover:bg-gray-600 rounded-md" data-close-modal>Cancel</button>
                <form id="deleteForm" method="POST" action="">
                    @csrf
                    @method('GET')
                    <button type="submit" class="text-white py-2 px-6 bg-red-500 hover:bg-red-600 rounded-md">Delete</button>
                </form>
            </div>
        </div>
    </div>
  </div>
  
  <script>
    document.addEventListener('DOMContentLoaded', function() {
    // Open modal when a button with data-toggle-modal is clicked
    document.querySelectorAll('[data-toggle-modal]').forEach(button => {
        button.addEventListener('click', function() {
            const modalSelector = button.getAttribute('data-toggle-modal');
            const modal = document.querySelector(modalSelector);
            const fileId = button.getAttribute('data-file-id');
  
            // Ensure fileId is available
            if (fileId) {
                // Set the form action URL
                const deleteForm = modal.querySelector('#deleteForm');
                
                // Construct the URL with the file ID parameter
                const deleteUrl = `/admin/records/delete/${fileId}`;
                
                // Set the form action to the constructed URL
                deleteForm.setAttribute('action', deleteUrl);
                
                // Show the modal
                modal.classList.remove('hidden');
            }
        });
    });
  
    // Close modal when a button with data-close-modal is clicked
    document.querySelectorAll('[data-close-modal]').forEach(button => {
        button.addEventListener('click', function() {
            const modal = button.closest('#deleteConfirmationModal');
            // Hide the modal
            modal.classList.add('hidden');
        });
    });
    });
  
  
  </script>

<script>
    function toggleSidebar() {
        const sidebar = document.getElementById('sidebar');
        sidebar.classList.toggle('-translate-x-full');
    }
</script>

@endsection

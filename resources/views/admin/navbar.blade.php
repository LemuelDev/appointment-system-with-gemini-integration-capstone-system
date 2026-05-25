<nav class="shadow p-4" data-theme="light">
    <div class="container mx-auto flex justify-between px-2 sm:px-4 items-center sm:gap-4 ">

      <div class="flex items-center gap-4">
        <button class="block lg:hidden" onclick="toggleSidebar()">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="w-6 h-6">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7" />
        </svg>
       </button>
       <button class="btn btn-info max-sm:hidden block" onclick="window.history.back()"><box-icon name='left-arrow-alt'></box-icon></button>
      </div>

       <div class="flex justify-end gap-4 items-center">
        
      @php
            // Pull real unread counts and the 5 latest messages for the current user
            $unreadCount = auth()->user()->unreadNotifications->count();
            $latestNotifications = auth()->user()->notifications->take(5);
        @endphp

        <div class="dropdown dropdown-end">
           <div tabindex="0" role="button" class="btn btn-ghost btn-circle bg-base-100 border border-gray-200 shadow-sm">
              <div class="indicator">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-700" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                  </svg>
                  
                  <span id="notification-badge" class="badge badge-sm badge-error indicator-item font-bold text-white animate-pulse {{ $unreadCount > 0 ? '' : 'hidden' }}">
                      {{ $unreadCount }}
                  </span>
              </div>
          </div>

            <div tabindex="0" class="dropdown-content card card-compact w-80 bg-base-100 shadow-2xl z-[100] mt-3 border border-base-200 text-gray-800">
                <div class="card-body">
                   <div class="flex justify-between items-center border-b border-base-200 pb-2">
                      <span class="font-bold text-sm tracking-wide">Notifications</span>
                      
                      <a href="{{ route('admin.notifications.markAllRead') }}" 
                        id="mark-all-read-link" 
                        class="text-xs link link-primary no-underline hover:underline {{ $unreadCount > 0 ? '' : 'hidden' }}">
                          Mark all as read
                      </a>
                  </div>

                 <div id="notification-list" class="max-h-64 overflow-y-auto divide-y divide-gray-100">
    @forelse($latestNotifications as $notification)
        @php
            // Safe fallback value extraction arrays
            $appNum = $notification->data['appointment_number'] ?? '';
            $isCancel = str_contains($notification->data['title'] ?? '', 'Cancel');
        @endphp

        <div class="relative z-10 hover:bg-gray-50 transition duration-150 rounded-md my-0.5 {{ $notification->read_at ? 'opacity-60' : 'bg-blue-50/40 font-medium' }}">
            
          
                <a href="{{ route('admin.trackNotification', $appNum) }}?notification_id={{ $notification->id }}" class="block p-2.5 w-full h-full clearfix">
                <div class="flex justify-between items-baseline gap-2 pointer-events-none">
                    <span class="text-xs {{ $isCancel ? 'text-red-600' : 'text-blue-600' }} font-bold">
                        {{ $notification->data['title'] ?? 'Notification Update' }}
                    </span>
                    <span class="text-[10px] text-gray-400 whitespace-nowrap">
                        {{ $notification->created_at->diffForHumans() }}
                    </span>
                </div>
                <p class="text-[11px] text-gray-600 mt-1 line-clamp-2 leading-relaxed pointer-events-none">
                    {{ $notification->data['message'] ?? '' }}
                </p>
            </a>
        </div>
    @empty
        <div class="py-8 text-center text-xs text-gray-400 no-notif-placeholder">
            No new notifications.
        </div>
    @endforelse
</div>
                </div>
            </div>
        </div>
        <a href="{{route("admin.newReservation")}}" class="btn btn-primary text-white max-sm:text-sm">Add Appointment</a>
        <button class="btn btn-error" onclick="my_modal_1.showModal()">Logout</button>
        
        {{-- modal --}}
        <dialog id="my_modal_1" class="modal" data-theme="light">
          <div class="modal-box">
            <h3 class="text-xl font-bold">Confirmation</h3>
            <p class="pt-4 text-lg text-center">Are you sure you want to logout ?</p>
            <div class="modal-action">
              <form action="{{route('logout')}}" method="POST">
                @csrf
                <button class="btn btn-error">Logout</button>
              </form>
              <form method="dialog">
                <button class="btn">Close</button>
              </form>
            </div>
          </div>
        </dialog>
       </div>
    </div>
</nav>

<script>
    function checkNotifications() {
        fetch("{{ route('admin.notifications.count') }}")
            .then(response => response.json())
            .then(data => {
                // 1. Manage the Red Badge Number Icon
                const badge = document.getElementById('notification-badge');
                if (badge) {
                    if (data.count > 0) {
                        badge.innerText = data.count;
                        badge.classList.remove('hidden');
                    } else {
                        badge.classList.add('hidden');
                    }
                }

                // 2. Manage the Dropdown Message List Rows
                const listContainer = document.getElementById('notification-list');
                if (listContainer && data.notifications.length > 0) {
                    // Wipe the container clear so we don't duplicate rows stacked on top of each other
                    listContainer.innerHTML = ''; 

                    // Loop through the data collection sent by Laravel
                    data.notifications.forEach(notif => {
                        const titleColor = notif.data.title.includes('Cancel') ? 'text-red-600' : 'text-blue-600';
                        const backgroundStyle = notif.read_at ? 'opacity-60' : 'bg-blue-50/40 font-medium';
                        
                        // Format the timestamp text
                        const timeText = new Date(notif.created_at).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});

                        // Generate the row elements markup matches your layout
                        const rowHtml = `
                            <div class="py-2.5 px-2 hover:bg-gray-50 transition duration-150 rounded-md my-0.5 ${backgroundStyle}">
                                <a href="${notif.data.link || '#'}" class="block">
                                    <div class="flex justify-between items-baseline gap-2">
                                        <span class="text-xs ${titleColor} font-bold">${notif.data.title}</span>
                                        <span class="text-[10px] text-gray-400 whitespace-nowrap">${timeText}</span>
                                    </div>
                                    <p class="text-[11px] text-gray-600 mt-1 line-clamp-2 leading-relaxed">
                                        ${notif.data.message}
                                    </p>
                                </a>
                            </div>
                        `;
                        listContainer.innerHTML += rowHtml;
                    });
                }
            })
            .catch(error => console.error('Poller error:', error));
    }

    checkNotifications();
    setInterval(checkNotifications, 5000);
</script>
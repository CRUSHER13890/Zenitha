@props(['title'])
<meta name="csrf-token" content="{{ csrf_token() }}">
<div class="border-b w-full h-16 shadow-sm fixed z-30 bg-white">
    <div class="flex h-full items-center justify-between px-6">
        <!-- Left side: Title -->
        <div class="flex-1 flex items-center">
            <h1 class="text-2xl font-bold text-yellow-400">{{ $title }}</h1>
        </div>

        <!-- Right side: Navigation buttons, notifications, and user dropdown -->
        <div class="flex items-center space-x-4">
            <!-- Conditional Navigation buttons (only visible when user is logged in) -->
            @if (Auth::check())
                <a href="{{ route('home.cart') }}"
                    class="text-sm px-6 py-2 border bg-gray-700 text-white rounded-md hover:bg-gray-800">My Cart</a>
                <a href="{{ route('home.history-reservation') }}"
                    class="text-sm px-6 py-2 border bg-gray-700 text-white rounded-md hover:bg-gray-800">Riwayat
                    Reservasi</a>
                <a href="{{ route('home.history') }}"
                    class="text-sm px-6 py-2 border bg-gray-700 text-white rounded-md hover:bg-gray-800">Riwayat
                    Pembelian</a>

                <!-- Notification Bell -->
                <div class="relative">
                    <button class="relative p-2 text-gray-600 hover:text-gray-800 focus:outline-none"
                        id="notification-btn">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9">
                            </path>
                        </svg>
                        <!-- Notification Badge -->
                        <span
                            class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center hidden"
                            id="notification-badge">0</span>
                    </button>

                    <!-- Notification Dropdown -->
                    <div class="absolute right-0 mt-2 w-80 bg-white shadow-lg rounded-md border border-gray-200 hidden z-50"
                        id="notification-dropdown">
                        <div class="p-4 border-b border-gray-200">
                            <div class="flex justify-between items-center">
                                <h3 class="text-lg font-semibold text-gray-800">Notifikasi</h3>
                                <button class="text-sm text-blue-600 hover:text-blue-800" id="mark-all-read">
                                    Tandai Semua Dibaca
                                </button>
                            </div>
                        </div>
                        <div class="max-h-96 overflow-y-auto" id="notification-list">
                            <!-- Notifications will be loaded here -->
                            <div class="p-4 text-center text-gray-500">Memuat notifikasi...</div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- User dropdown (only visible when user is logged in) -->
            @if (Auth::check())
                <div class="relative">
                    <button
                        class="flex items-center space-x-2 px-4 py-1 border bg-gray-700 text-white rounded-md hover:bg-gray-800 focus:outline-none"
                        id="user-dropdown-btn">
                        <img src="https://www.w3schools.com/howto/img_avatar.png" alt="User Avatar"
                            class="h-8 w-8 rounded-full">
                        <span class="text-sm font-semibold">{{ Auth::user()->name }}</span>
                        <svg class="w-4 h-4 ml-2 text-white" xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7">
                            </path>
                        </svg>
                    </button>
                    <!-- Dropdown content -->
                    <div class="absolute right-0 mt-2 w-48 bg-white shadow-lg rounded-md border border-gray-200 hidden"
                        id="user-dropdown">
                        <div class="p-2">
                            <p class="px-4 py-2 text-sm text-gray-700">Welcome, {{ Auth::user()->name }}!</p>
                            @if (Auth::user()->role === 'admin')
                                <a href="{{ route('dashboard') }}"
                                    class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-200">
                                    Dashboard
                                </a>
                            @endif
                            <form action="{{ route('logout') }}" method="post" class="mt-2">
                                @csrf
                                <button type="submit"
                                    class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-200">
                                    Logout
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @else
                <!-- Login/Register Buttons (for non-authenticated users) -->
                <a href="{{ route('login') }}"
                    class="text-sm px-7 py-2 border bg-gray-300 text-center rounded-md hover:bg-gray-400">Login</a>
                <a href="{{ route('register') }}"
                    class="text-sm px-5 py-2 border bg-gray-700 text-center text-white rounded-md ms-2 hover:bg-gray-800">Register</a>
            @endif
        </div>
    </div>
</div>

<script>
    // Toggle user dropdown
    const dropdownBtn = document.getElementById('user-dropdown-btn');
    const dropdownMenu = document.getElementById('user-dropdown');

    if (dropdownBtn && dropdownMenu) {
        dropdownBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            dropdownMenu.classList.toggle('hidden');
        });

        document.addEventListener('click', (e) => {
            if (!dropdownBtn.contains(e.target) && !dropdownMenu.contains(e.target)) {
                dropdownMenu.classList.add('hidden');
            }
        });
    }

    // Notification system with real-time badge updates
    const notificationBtn = document.getElementById('notification-btn');
    const notificationDropdown = document.getElementById('notification-dropdown');
    const notificationBadge = document.getElementById('notification-badge');
    const notificationList = document.getElementById('notification-list');
    const markAllReadBtn = document.getElementById('mark-all-read');

    if (notificationBtn && notificationDropdown) {
        // State management
        let isDropdownOpen = false;
        let pollingInterval;
        const NORMAL_POLLING_INTERVAL = 5000; // 5 seconds for real-time updates
        const ACTIVE_POLLING_INTERVAL = 5000;  // 5 seconds when dropdown is open
        const INITIAL_LOAD_DELAY = 1000;       // 1 second after page load

        // Notification types with icons
        const NOTIFICATION_ICONS = {
            'reservasi_approved': {
                icon: '<svg class="w-5 h-5 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>',
                bgColor: 'bg-green-50'
            },
            'reservasi_cancelled': {
                icon: '<svg class="w-5 h-5 text-red-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>',
                bgColor: 'bg-red-50'
            },
            'default': {
                icon: '<svg class="w-5 h-5 text-blue-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>',
                bgColor: 'bg-blue-50'
            }
        };

        // Toggle notification dropdown
        notificationBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            isDropdownOpen = !notificationDropdown.classList.contains('hidden');
            
            if (!isDropdownOpen) {
                // When opening dropdown
                loadNotifications();
            }
            
            notificationDropdown.classList.toggle('hidden');
            isDropdownOpen = !isDropdownOpen;
        });

        // Close dropdown when clicking outside
        document.addEventListener('click', (e) => {
            if (!notificationBtn.contains(e.target) && !notificationDropdown.contains(e.target)) {
                notificationDropdown.classList.add('hidden');
                isDropdownOpen = false;
            }
        });

        // Mark all as read
        if (markAllReadBtn) {
            markAllReadBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                markAllAsRead();
            });
        }

        // Set polling interval
        function setPollingInterval(interval) {
            clearInterval(pollingInterval);
            pollingInterval = setInterval(loadNotifications, interval);
        }

        // Fungsi untuk update badge saja
        function updateBadgeOnly(count) {
            count = parseInt(count) || 0;
            if (count > 0) {
                notificationBadge.textContent = count > 9 ? '9+' : count;
                notificationBadge.classList.remove('hidden');
                
                // Tambahkan animasi untuk menarik perhatian
                notificationBadge.classList.add('animate-pulse');
                setTimeout(() => {
                    notificationBadge.classList.remove('animate-pulse');
                }, 2000);
            } else {
                notificationBadge.classList.add('hidden');
            }
        }

        // Modifikasi loadNotifications untuk real-time update
        function loadNotifications() {
            fetch('/notifications?_=' + Date.now(), {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                credentials: 'same-origin'
            })
            .then(response => {
                if (!response.ok) throw new Error('Network response was not ok');
                return response.json();
            })
            .then(data => {
                if (data) {
                    // Update badge terlepas dari dropdown terbuka/tidak
                    updateBadgeOnly(data.unread_count);
                    
                    // Update list hanya jika dropdown terbuka
                    if (isDropdownOpen && data.notifications) {
                        displayNotifications(data.notifications);
                    }
                }
            })
            .catch(error => {
                console.error('Error loading notifications:', error);
                if (isDropdownOpen) {
                    notificationList.innerHTML = '<div class="p-4 text-center text-red-500">Gagal memuat notifikasi</div>';
                }
            });
        }

        // Display notifications
        function displayNotifications(notifications) {
            if (!Array.isArray(notifications) || notifications.length === 0) {
                notificationList.innerHTML = '<div class="p-4 text-center text-gray-500">Tidak ada notifikasi</div>';
                return;
            }

            let html = '';
            notifications.forEach(notification => {
                if (!notification) return;
                
                const notificationType = NOTIFICATION_ICONS[notification.jenis] || NOTIFICATION_ICONS.default;
                const isRead = notification.is_read;
                const bgClass = isRead ? 'bg-white' : notificationType.bgColor;
                const date = formatDate(notification.created_at);

                html += `
                    <div class="p-3 border-b border-gray-100 notification-item ${bgClass}" 
                         data-id="${notification.id}">
                        <div class="flex items-start">
                            ${notificationType.icon}
                            <div class="flex-1">
                                <h4 class="font-semibold ${isRead ? 'text-gray-700' : 'text-gray-900'}">${escapeHtml(notification.judul)}</h4>
                                <p class="text-sm text-gray-600 mt-1">${escapeHtml(notification.pesan)}</p>
                                <span class="text-xs text-gray-400 mt-2 block">${date}</span>
                            </div>
                            ${!isRead ? '<div class="w-2 h-2 bg-blue-500 rounded-full ml-2 mt-1"></div>' : ''}
                        </div>
                    </div>
                `;
            });

            notificationList.innerHTML = html;

            // Add click event to mark individual notifications as read
            document.querySelectorAll('.notification-item').forEach(item => {
                item.addEventListener('click', function() {
                    const notificationId = this.dataset.id;
                    if (notificationId) {
                        markAsRead(notificationId);
                    }
                });
            });
        }

        // Mark as read
        function markAsRead(notificationId) {
            fetch('/notifications/mark-as-read', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({ id: notificationId }),
                credentials: 'same-origin'
            })
            .then(response => {
                if (!response.ok) throw new Error('Network response was not ok');
                return response.json();
            })
            .then(data => {
                if (data && data.success) {
                    loadNotifications();
                }
            })
            .catch(error => {
                console.error('Error marking notification as read:', error);
            });
        }

        // Mark all as read
        function markAllAsRead() {
            fetch('/notifications/mark-all-read', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'X-Requested-With': 'XMLHttpRequest'
                },
                credentials: 'same-origin'
            })
            .then(response => {
                if (!response.ok) throw new Error('Network response was not ok');
                return response.json();
            })
            .then(data => {
                if (data && data.success) {
                    loadNotifications();
                    updateBadgeOnly(0);
                }
            })
            .catch(error => {
                console.error('Error marking all notifications as read:', error);
            });
        }

        // Format date
        function formatDate(dateString) {
            try {
                const date = new Date(dateString);
                return date.toLocaleDateString('id-ID', {
                    year: 'numeric',
                    month: 'short',
                    day: 'numeric',
                    hour: '2-digit',
                    minute: '2-digit'
                });
            } catch (e) {
                console.error('Error formatting date:', e);
                return '';
            }
        }

        // Escape HTML
        function escapeHtml(unsafe) {
            if (!unsafe) return '';
            return unsafe
                .toString()
                .replace(/&/g, "&amp;")
                .replace(/</g, "&lt;")
                .replace(/>/g, "&gt;")
                .replace(/"/g, "&quot;")
                .replace(/'/g, "&#039;");
        }

        // Request notification permission
        function requestNotificationPermission() {
            if (Notification.permission === 'default') {
                Notification.requestPermission();
            }
        }

        // Initialize
        document.addEventListener('DOMContentLoaded', function() {
            @if(Auth::check())
                // Request notification permission
                requestNotificationPermission();
                
                // Load pertama kali segera
                loadNotifications();
                
                // Polling setiap 5 detik untuk update real-time
                setPollingInterval(5000);
                
                // Initialize sound notification (optional)
                const notificationSound = new Audio('data:audio/wav;base64,UklGRvIAAABXQVZFZm10IBAAAAABAAEAQB8AAEAfAAABAAgAZGF0YQoAAAC');
                
                // Real-time dengan WebSocket jika tersedia
                @if(env('PUSHER_APP_KEY'))
                    if (typeof Echo !== 'undefined') {
                        Echo.private(`App.Models.User.{{ Auth::id() }}`)
                            .notification((notification) => {
                                console.log('New real-time notification:', notification);
                                
                                // Play sound
                                notificationSound.play().catch(e => console.log('Audio play failed:', e));
                                
                                // Immediately update badge
                                loadNotifications();
                                
                                // Show desktop notification jika halaman tidak aktif
                                if (document.hidden && Notification.permission === 'granted') {
                                    new Notification(notification.judul || 'Notifikasi Baru', {
                                        body: notification.pesan || 'Anda memiliki notifikasi baru',
                                        icon: '/favicon.ico'
                                    });
                                }
                            });
                    }
                @endif
            @endif
        });

        // Clean up on page unload
        window.addEventListener('beforeunload', function() {
            clearInterval(pollingInterval);
        });
    }
</script>

@if(Auth::check())
    <!-- Optional: Add Laravel Echo for real-time updates -->
    <script src="https://cdn.jsdelivr.net/npm/laravel-echo@1.15.3/dist/echo.iife.min.js"></script>
    <script>
        window.Echo = new Echo({
            broadcaster: 'pusher',
            key: '{{ env('PUSHER_APP_KEY') }}',
            cluster: '{{ env('PUSHER_APP_CLUSTER') }}',
            encrypted: true,
            forceTLS: true
        });

        Echo.private(`App.Models.User.{{ Auth::id() }}`)
            .notification((notification) => {
                console.log('Real-time notification received:', notification);
                // Force refresh notifications
                if (typeof loadNotifications === 'function') {
                    loadNotifications();
                }
            });
    </script>
@endif
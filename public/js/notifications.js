// Notifications JavaScript
$(document).ready(function() {
    // Setup CSRF token
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    let notificationDropdown = $('#notification-dropdown');
    let isDropdownOpen = false;
    let toggleInProgress = false;

    // Initialize notifications
    loadNotifications();
    updateUnreadCount();
    
    // Auto refresh every 30 seconds
    setInterval(() => {
        if (!isDropdownOpen) { // Only refresh when dropdown is closed
        loadNotifications();
        updateUnreadCount();
        }
    }, 30000);

    // Toggle notification dropdown - FIXED
    $('#notification-toggle').on('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        // Prevent multiple rapid clicks
        if (toggleInProgress) return;
        toggleInProgress = true;
        
        setTimeout(() => {
            toggleInProgress = false;
        }, 300);
        
        if (isDropdownOpen) {
            hideNotificationDropdown();
        } else {
            showNotificationDropdown();
        }
    });

    // Close dropdown when clicking outside - IMPROVED
    $(document).on('click', function(e) {
        if (!isDropdownOpen) return;
        
        const dropdown = $('#notification-dropdown');
        const toggle = $('#notification-toggle');
        
        // Check if click is outside both dropdown and toggle button
        if (!dropdown.is(e.target) && !dropdown.has(e.target).length && 
            !toggle.is(e.target) && !toggle.has(e.target).length) {
                hideNotificationDropdown();
        }
    });

    // Mark all as read
    $('#mark-all-read').on('click', function(e) {
        e.stopPropagation();
        e.preventDefault();
        markAllAsRead();
    });

    // Click on notification content to mark as read - IMPROVED
    $(document).on('click', '.notification-content', function(e) {
        e.stopPropagation();
        
        const notificationItem = $(this).closest('.notification-item');
        const notificationId = notificationItem.data('id');
        
        if (notificationId && !notificationItem.hasClass('read')) {
            markAsRead(notificationId);
        }
    });

    // Delete notification button - COMPLETELY FIXED
    $(document).on('click', '.delete-notification', function(e) {
        console.log('=== DELETE BUTTON CLICKED ===');
        e.stopPropagation();
        e.preventDefault();
        
        const $button = $(this);
        const notificationId = $button.data('id') || $button.attr('data-id');
        
        console.log('Notification ID:', notificationId);
        console.log('Button data:', $button.data());
        console.log('Button attributes:', {
            'data-id': $button.attr('data-id'),
            'id': $button.attr('id'),
            'class': $button.attr('class')
        });
        
        if (notificationId) {
            console.log('Calling deleteNotification with ID:', notificationId);
            deleteNotification(notificationId);
        } else {
            console.error('ERROR: No notification ID found!');
            console.log('Button HTML:', $button.prop('outerHTML'));
            
            // Try to find ID from parent element
            const parentId = $button.closest('.notification-item').data('id');
            if (parentId) {
                console.log('Found ID from parent:', parentId);
                deleteNotification(parentId);
            } else {
                console.error('Could not find notification ID anywhere');
            }
        }
    });

    // Prevent dropdown close when clicking inside dropdown content - IMPROVED
    $('#notification-dropdown').on('click', function(e) {
        // Don't stop propagation for delete buttons - let them handle their own events
        if ($(e.target).hasClass('delete-notification') || $(e.target).closest('.delete-notification').length) {
            return;
        }
        
        e.stopPropagation();
        console.log('Clicked inside dropdown - preventing close');
    });

    // View all notifications
    $('#view-all-notifications').on('click', function(e) {
        e.stopPropagation();
        e.preventDefault();
        console.log('View all notifications clicked');
        hideNotificationDropdown();
    });

    function showNotificationDropdown() {
        console.log('Showing notification dropdown');
        const dropdown = $('#notification-dropdown');
        
        isDropdownOpen = true;
        
        dropdown.removeClass('hidden')
                .addClass('animate-in slide-in-from-top-2 fade-in-20')
                .css({
                    'animation-duration': '200ms',
                    'animation-timing-function': 'cubic-bezier(0.4, 0, 0.2, 1)'
                });
        
        // Load fresh notifications when opening
        loadNotifications();
    }

    function hideNotificationDropdown() {
        console.log('Hiding notification dropdown');
        const dropdown = $('#notification-dropdown');
        
        isDropdownOpen = false;
        
        dropdown.removeClass('animate-in slide-in-from-top-2 fade-in-20')
                .addClass('animate-out slide-out-to-top-2 fade-out-20');
        
        setTimeout(() => {
            dropdown.addClass('hidden').removeClass('animate-out slide-out-to-top-2 fade-out-20');
        }, 150);
    }

    function loadNotifications() {
        $.ajax({
            url: '/notifications',
            method: 'GET',
            data: { limit: 10 },
            success: function(response) {
                if (response.success) {
                    renderNotifications(response.notifications);
                    updateNotificationBadge(response.unread_count);
                }
            },
            error: function(xhr) {
                console.error('Failed to load notifications:', xhr);
                $('#notification-list').html(`
                    <div class="p-6 text-center text-red-500 dark:text-red-400">
                        <div class="mb-3">
                            <svg class="w-12 h-12 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <p class="text-sm font-medium">Failed to load notifications</p>
                    </div>
                `);
            }
        });
    }

    function updateNotificationBadge(count) {
        const badge = $('#notification-badge');
        const countElement = $('#notification-count');
        const button = $('#notification-toggle');
        
        if (count > 0) {
            badge.removeClass('hidden').addClass('animate-pulse');
            button.addClass('notification-badge');
            countElement.text(count > 99 ? '99+' : count);
        } else {
            badge.addClass('hidden').removeClass('animate-pulse');
            button.removeClass('notification-badge');
        }
    }

    function renderNotifications(notifications) {
        const container = $('#notification-list');
        
        if (!notifications || notifications.length === 0) {
            container.html(`
                <div class="p-8 text-center text-gray-500 dark:text-gray-400">
                    <div class="mb-4">
                        <svg class="w-16 h-16 mx-auto text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M15 17h5l-5 5v-5zM4 19v-2.5A7.5 7.5 0 0111.5 9H14"></path>
                        </svg>
                    </div>
                    <p class="text-lg font-medium mb-2">No notifications yet</p>
                    <p class="text-sm">You're all caught up! 🎉</p>
                </div>
            `);
            return;
        }

        let html = '';
        notifications.forEach(notification => {
            const isRead = notification.is_read || notification.read_at !== null;
            const icon = getNotificationIcon(notification.type.toUpperCase());
            
            html += `
                <div class="notification-item ${isRead ? 'read' : 'unread'} relative border-b border-gray-100 dark:border-gray-600" data-id="${notification.id}">
                    <!-- Delete Button - Absolute positioned in top right -->
                    <button class="delete-notification absolute top-3 right-3 w-8 h-8 flex items-center justify-center text-gray-400 hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-full transition-all duration-200 opacity-80 hover:opacity-100 z-10" 
                            data-id="${notification.id}" 
                            title="Delete notification">
                        <svg class="w-4 h-4 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                    </button>
                    
                    <!-- Notification Content - with right padding for delete button -->
                    <div class="notification-content p-4 pr-14 hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer transition-all duration-200">
                        <div class="flex items-start space-x-3">
                            <div class="flex-shrink-0">
                                <div class="w-10 h-10 rounded-full ${isRead ? 'bg-gray-100 dark:bg-gray-600' : 'bg-blue-100 dark:bg-blue-900'} flex items-center justify-center">
                                    ${icon}
                                </div>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-semibold text-gray-900 dark:text-white ${isRead ? 'opacity-75' : ''} line-clamp-1">
                                    ${notification.title}
                                </p>
                                <p class="text-sm text-gray-600 dark:text-gray-300 ${isRead ? 'opacity-60' : ''} line-clamp-2 mt-1">
                                    ${notification.message}
                                </p>
                                <div class="flex items-center justify-between mt-2">
                                    <p class="text-xs text-gray-500 dark:text-gray-400">
                                        ${notification.time_ago}
                                    </p>
                                    ${!isRead ? `
                                        <div class="w-2 h-2 bg-blue-500 rounded-full animate-pulse"></div>
                                    ` : ''}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        });
        
        container.html(html);
    }

    function getNotificationIcon(type) {
        const iconClass = "w-5 h-5";
        const icons = {
            'CREATE': `<svg class="${iconClass} text-green-600 dark:text-green-400" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm5 11h-4v4h-2v-4H7v-2h4V7h2v4h4v2z"/></svg>`,
            'UPDATE': `<svg class="${iconClass} text-blue-600 dark:text-blue-400" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>`,
            'DELETE': `<svg class="${iconClass} text-red-600 dark:text-red-400" fill="currentColor" viewBox="0 0 24 24"><path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12 19 6.41z"/></svg>`,
            'APPROVE': `<svg class="${iconClass} text-green-600 dark:text-green-400" fill="currentColor" viewBox="0 0 24 24"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41L9 16.17z"/></svg>`,
            'REJECT': `<svg class="${iconClass} text-red-600 dark:text-red-400" fill="currentColor" viewBox="0 0 24 24"><path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12 19 6.41z"/></svg>`,
            'MARK_PAID': `<svg class="${iconClass} text-green-600 dark:text-green-400" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/></svg>`,
            'SCHEDULE': `<svg class="${iconClass} text-purple-600 dark:text-purple-400" fill="currentColor" viewBox="0 0 24 24"><path d="M19 3h-1V1h-2v2H8V1H6v2H5c-1.11 0-1.99.9-1.99 2L3 19c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 16H5V8h14v11zM7 10h5v5H7z"/></svg>`,
            'LOGIN': `<svg class="${iconClass} text-blue-600 dark:text-blue-400" fill="currentColor" viewBox="0 0 24 24"><path d="M11,7L9.6,8.4l2.6,2.6H2v2h10.2l-2.6,2.6L11,17l5-5L11,7z M20,19h-8v2h8c1.1,0,2-0.9,2-2V5c0-1.1-0.9-2-2-2h-8v2h8V19z"/></svg>`
        };
        
        return icons[type] || icons['CREATE'];
    }

    function markAsRead(notificationId) {
        $.ajax({
            url: `/notifications/${notificationId}/read`,
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    // Update UI immediately
                    const notificationElement = $(`.notification-item[data-id="${notificationId}"]`);
                    notificationElement.removeClass('unread').addClass('read');
                    
                    // Update notification appearance
                    notificationElement.find('.text-gray-900, .text-white').addClass('opacity-75');
                    notificationElement.find('.text-gray-600, .text-gray-300').addClass('opacity-60');
                    notificationElement.find('.bg-blue-100, .bg-blue-900').removeClass('bg-blue-100 bg-blue-900').addClass('bg-gray-100 dark:bg-gray-600');
                    notificationElement.find('.animate-pulse').removeClass('animate-pulse');
                    
                    // Update counts
                    updateUnreadCount();
                }
            },
            error: function(xhr) {
                console.error('Failed to mark notification as read:', xhr);
            }
        });
    }

    function markAllAsRead() {
        $.ajax({
            url: '/notifications/mark-all-read',
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    // Show success message
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: 'All notifications marked as read',
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 3000,
                        timerProgressBar: true
                    });
                    
                    // Refresh notifications
                    loadNotifications();
                    updateUnreadCount();
                }
            },
            error: function(xhr) {
                console.error('Failed to mark all notifications as read:', xhr);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Failed to mark notifications as read',
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 3000
                });
            }
        });
    }

    function deleteNotification(notificationId) {
        console.log('deleteNotification called with ID:', notificationId);
        
        if (!notificationId) {
            console.error('Invalid notification ID provided');
            return;
        }
        
        // Show confirmation
        Swal.fire({
            title: 'Delete Notification?',
            text: "This notification will be permanently deleted.",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Yes, delete it!',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            console.log('Confirmation result:', result);
            
            if (result.isConfirmed) {
                console.log('User confirmed deletion, making AJAX request...');
                
                $.ajax({
                    url: `/notifications/${notificationId}`,
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    beforeSend: function() {
                        console.log('Sending DELETE request for notification:', notificationId);
                    },
                    success: function(response) {
                        console.log('Delete response:', response);
                        
                        if (response.success) {
                            // Remove from UI with animation
                            const notificationElement = $(`.notification-item[data-id="${notificationId}"]`);
                            
                            if (notificationElement.length) {
                                notificationElement.addClass('opacity-0 transform scale-95 transition-all duration-200');
                            
                            setTimeout(() => {
                                notificationElement.remove();
                                    
                                    // Update counts and check if dropdown is empty
                                updateUnreadCount();
                                    
                                    // If no more notifications, show empty state
                                    if ($('.notification-item').length === 0) {
                                        loadNotifications();
                                    }
                            }, 200);
                            }
                            
                            // Show success toast
                            Swal.fire({
                                icon: 'success',
                                title: 'Deleted!',
                                text: 'Notification has been deleted.',
                                toast: true,
                                position: 'top-end',
                                showConfirmButton: false,
                                timer: 2000,
                                timerProgressBar: true
                            });
                        } else {
                            console.error('Server returned error:', response);
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: response.message || 'Failed to delete notification',
                                toast: true,
                                position: 'top-end',
                                showConfirmButton: false,
                                timer: 3000
                            });
                        }
                    },
                    error: function(xhr) {
                        console.error('AJAX error while deleting notification:', xhr);
                        console.error('Status:', xhr.status);
                        console.error('Response text:', xhr.responseText);
                        
                        let errorMessage = 'Failed to delete notification';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        } else if (xhr.status === 403) {
                            errorMessage = 'You are not authorized to delete this notification';
                        } else if (xhr.status === 404) {
                            errorMessage = 'Notification not found';
                        }
                        
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: errorMessage,
                            toast: true,
                            position: 'top-end',
                            showConfirmButton: false,
                            timer: 3000
                        });
                    }
                });
            }
        });
    }

    function updateUnreadCount() {
        $.ajax({
            url: '/notifications/unread-count',
            method: 'GET',
            success: function(response) {
                if (response.success) {
                    updateNotificationBadge(response.unread_count);
                }
            },
            error: function(xhr) {
                console.error('Failed to get unread count:', xhr);
            }
        });
    }

    // Function to create new notification (for testing)
    window.createTestNotification = function() {
        const testNotifications = [
            {
                type: 'create',
                title: 'Test Notification',
                message: 'This is a test notification for development purposes',
                data: { test: true }
            },
            {
                type: 'approve',
                title: 'Request Approved',
                message: 'Your request has been approved by admin',
                data: { approved: true }
            }
        ];
        
        const randomNotification = testNotifications[Math.floor(Math.random() * testNotifications.length)];
        
        // This would typically be called from the backend when an activity occurs
        console.log('Test notification would be created:', randomNotification);
        
        // Refresh notifications to show any new ones
        loadNotifications();
    };
}); 
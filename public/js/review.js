$(function() {
    // Search and filter functionality
    let searchTimeout;
    
    function loadReviews() {
        const category = $('#category-filter').val();
        const search = $('#search-input').val();
        
        $.get('/review', {
            category: category,
            search: search
        }, function(response) {
            $('#review-list').html(response.html);
            bindEventHandlers();
        }).fail(function(error) {
            console.error('Error loading reviews:', error);
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Failed to load reviews. Please try again.'
            });
        });
    }
    
    // Category filter change
    $('#category-filter').on('change', function() {
        loadReviews();
    });
    
    // Search input with debounce
    $('#search-input').on('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(function() {
            loadReviews();
        }, 500);
    });
    
    // Clear filters
    $('#clear-filters').on('click', function() {
        $('#category-filter').val('all');
        $('#search-input').val('');
        loadReviews();
    });
    
    function bindEventHandlers() {
        // View details
        $('.view-details').off('click').on('click', function() {
            const requestId = $(this).data('request-id');
            $.get(`/review/${requestId}`, function(response) {
                const request = response.request;
                let detailsHtml = generateDetailedView(request);
                $('#request-details-content').html(detailsHtml);
                $('#details-modal').removeClass('hidden').addClass('flex');
            }).fail(function(error) {
                console.error('Error fetching request details:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Failed to load request details. Please try again.'
                });
            });
        });

        // Approve request
        $('.approve-request').off('click').on('click', function() {
            const requestId = $(this).data('request-id');
            
            Swal.fire({
                title: 'Konfirmasi',
                text: 'Apakah Anda yakin ingin menyetujui permintaan ini?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Ya, Setuju',
                cancelButtonText: 'Batal',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    $.post(`/review/${requestId}/approve`, {
                        _token: $('meta[name="csrf-token"]').attr('content')
                    }).done(function(response) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: 'Permintaan telah disetujui',
                            timer: 1500,
                            showConfirmButton: false
                        }).then(() => {
                            window.location.reload();
                        });
                    }).fail(function(error) {
                        console.error('Error approving request:', error);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Gagal menyetujui permintaan. Silakan coba lagi.'
                        });
                    });
                }
            });
        });

        // Reject request
        $('.reject-request').off('click').on('click', function() {
            const requestId = $(this).data('request-id');
            
            Swal.fire({
                title: 'Konfirmasi',
                text: 'Apakah Anda yakin ingin menolak permintaan ini?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, Tolak',
                cancelButtonText: 'Batal',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    $.post(`/review/${requestId}/reject`, {
                        _token: $('meta[name="csrf-token"]').attr('content')
                    }).done(function(response) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: 'Permintaan telah ditolak',
                            timer: 1500,
                            showConfirmButton: false
                        }).then(() => {
                            window.location.reload();
                        });
                    }).fail(function(error) {
                        console.error('Error rejecting request:', error);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Gagal menolak permintaan. Silakan coba lagi.'
                        });
                    });
                }
            });
        });
    }
    
    function generateDetailedView(request) {
        const statusColor = request.status === 'pending' ? 'yellow' : 
                           request.status === 'approved' ? 'green' : 'red';
        const actionColor = request.action_type === 'create' ? 'green' : 
                           request.action_type === 'update' ? 'blue' : 'red';
        
        let html = `
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Request Overview -->
                <div class="lg:col-span-1">
                    <div class="bg-gradient-to-br from-${actionColor}-50 to-${actionColor}-100 rounded-xl p-6 border border-${actionColor}-200">
                        <div class="flex items-center space-x-3 mb-4">
                            <div class="p-3 bg-${actionColor}-500 rounded-lg">
                                ${getActionIcon(request.action_type)}
                            </div>
                            <div>
                                <h4 class="text-lg font-semibold text-${actionColor}-900">${request.action_type.charAt(0).toUpperCase() + request.action_type.slice(1)} ${request.model_type}</h4>
                                <p class="text-sm text-${actionColor}-700">Request #${request.id}</p>
                            </div>
                        </div>
                        
                        <div class="space-y-3">
                            <div class="flex items-center justify-between">
                                <span class="text-sm font-medium text-${actionColor}-700">Status:</span>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-${statusColor}-100 text-${statusColor}-800">
                                    ${request.status.charAt(0).toUpperCase() + request.status.slice(1)}
                                </span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-sm font-medium text-${actionColor}-700">Created:</span>
                                <span class="text-sm text-${actionColor}-600">${new Date(request.created_at).toLocaleDateString('id-ID', {
                                    year: 'numeric',
                                    month: 'long',
                                    day: 'numeric',
                                    hour: '2-digit',
                                    minute: '2-digit'
                                })}</span>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- User Information -->
                <div class="lg:col-span-2">
                    <div class="bg-white rounded-xl border border-gray-200 p-6">
                        <div class="flex items-center space-x-3 mb-4">
                            <div class="p-2 bg-blue-100 rounded-lg">
                                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                            </div>
                            <h4 class="text-lg font-semibold text-gray-900">Requester Information</h4>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Name</label>
                                <p class="text-sm text-gray-900 bg-gray-50 rounded-lg px-3 py-2">${request.user.name}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                                <p class="text-sm text-gray-900 bg-gray-50 rounded-lg px-3 py-2">${request.user.email}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Data Details -->
            <div class="bg-white rounded-xl border border-gray-200 p-6">
                <div class="flex items-center space-x-3 mb-4">
                    <div class="p-2 bg-purple-100 rounded-lg">
                        <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <h4 class="text-lg font-semibold text-gray-900">${request.model_type} Data</h4>
                </div>
                
                ${generateDataSection(request.data, request.model_type)}
            </div>
        `;
        
        // Add attachment section if exists
        if (request.attachment) {
            html += `
                <!-- Attachment Section -->
                <div class="bg-white rounded-xl border border-gray-200 p-6">
                    <div class="flex items-center space-x-3 mb-4">
                        <div class="p-2 bg-orange-100 rounded-lg">
                            <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/>
                            </svg>
                        </div>
                        <h4 class="text-lg font-semibold text-gray-900">Bukti Transaksi</h4>
                    </div>
                    
                    <div class="bg-gray-50 rounded-lg p-4">
                        <div class="flex items-center space-x-3">
                            <div class="flex-shrink-0">
                                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-900 truncate">
                                    ${request.attachment.split('/').pop()}
                                </p>
                                <p class="text-sm text-gray-500">
                                    File attachment dari user
                                </p>
                            </div>
                            <div class="flex-shrink-0">
                                <a href="/storage/${request.attachment}" target="_blank" 
                                   class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                    Lihat
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        }
        
        return html;
    }
    
    function getActionIcon(actionType) {
        const iconClass = "w-5 h-5 text-white";
        switch(actionType) {
            case 'create':
                return `<svg class="${iconClass}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                </svg>`;
            case 'update':
                return `<svg class="${iconClass}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>`;
            case 'delete':
                return `<svg class="${iconClass}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                </svg>`;
        }
    }
    
    function generateDataSection(data, modelType) {
        let html = '<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">';
        
        // Common fields for all models
        const commonFields = ['coa', 'customer', 'activity', 'prodi', 'grade'];
        const financialFields = ['debit', 'credit', 'rate_1', 'rate_2', 'quantity_1', 'quantity_2'];
        const statusFields = ['ar_status', 'ap_status', 'status'];
        
        // Display common fields
        commonFields.forEach(field => {
            if (data[field] !== undefined) {
                html += `
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">${field.replace('_', ' ').toUpperCase()}</label>
                        <p class="text-sm text-gray-900 bg-gray-50 rounded-lg px-3 py-2">${data[field]}</p>
                    </div>
                `;
            }
        });
        
        html += '</div>';
        
        // Financial information section
        const hasFinancialData = financialFields.some(field => data[field] !== undefined);
        if (hasFinancialData) {
            html += `
                <div class="mt-6">
                    <h5 class="text-md font-semibold text-gray-900 mb-3">Financial Information</h5>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            `;
            
            financialFields.forEach(field => {
                if (data[field] !== undefined) {
                    const isDebit = field === 'debit';
                    const isCredit = field === 'credit';
                    const colorClass = isDebit ? 'text-green-600' : isCredit ? 'text-red-600' : 'text-blue-600';
                    const value = typeof data[field] === 'number' ? 
                        `Rp ${new Intl.NumberFormat('id-ID').format(data[field])}` : 
                        data[field];
                    
                    html += `
                        <div class="bg-gray-50 rounded-lg p-3">
                            <label class="block text-xs font-medium text-gray-600 mb-1">${field.replace('_', ' ').toUpperCase()}</label>
                            <p class="text-sm font-semibold ${colorClass}">${value}</p>
                        </div>
                    `;
                }
            });
            
            html += '</div></div>';
        }
        
        // Status information
        const hasStatusData = statusFields.some(field => data[field] !== undefined);
        if (hasStatusData) {
            html += `
                <div class="mt-6">
                    <h5 class="text-md font-semibold text-gray-900 mb-3">Status Information</h5>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            `;
            
            statusFields.forEach(field => {
                if (data[field] !== undefined) {
                    const statusColor = data[field] === 'Paid' ? 'green' : 
                                      data[field] === 'Pending' ? 'yellow' : 'blue';
                    
                    html += `
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">${field.replace('_', ' ').toUpperCase()}</label>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-${statusColor}-100 text-${statusColor}-800">
                                ${data[field]}
                            </span>
                        </div>
                    `;
                }
            });
            
            html += '</div></div>';
        }
        
        // Additional fields (any remaining fields not covered above)
        const displayedFields = [...commonFields, ...financialFields, ...statusFields];
        const remainingFields = Object.keys(data).filter(key => !displayedFields.includes(key));
        
        if (remainingFields.length > 0) {
            html += `
                <div class="mt-6">
                    <h5 class="text-md font-semibold text-gray-900 mb-3">Additional Information</h5>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            `;
            
            remainingFields.forEach(field => {
                if (data[field] !== undefined && data[field] !== null) {
                    html += `
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">${field.replace('_', ' ').toUpperCase()}</label>
                            <p class="text-sm text-gray-900 bg-gray-50 rounded-lg px-3 py-2">${data[field]}</p>
                        </div>
                    `;
                }
            });
            
            html += '</div></div>';
        }
        
        return html;
    }

    // Close modal
    $('[data-modal-hide="details-modal"]').on('click', function() {
        $('#details-modal').removeClass('flex').addClass('hidden');
    });
    
    // Initial binding
    bindEventHandlers();
}); 
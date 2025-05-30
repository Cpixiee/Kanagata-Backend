// Invoice Management JavaScript
$(document).ready(function() {
    // Set CSRF token untuk semua request AJAX
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    // Set filter awal
    $('#year-filter').val(currentFilters.year);
    updatePeriodInfo();
    
    // Load data awal
    console.log('Loading initial invoice data...');
    loadInvoiceData();

    // Set auto refresh setiap 5 menit
    setInterval(loadInvoiceData, 300000);

    // Event handler untuk refresh button
    $('#refresh-data').on('click', function() {
        console.log('Manual refresh triggered');
        loadInvoiceData();
    });

    // Event handler untuk filter tahun
    $('#year-filter').on('change', function() {
        currentFilters.year = $(this).val();
        console.log('Year filter changed to:', currentFilters.year);
        updatePeriodInfo();
        loadInvoiceData();
    });
    
    // Event handler untuk filter bulan
    $('#month-filter').on('change', function() {
        currentFilters.month = $(this).val() || null;
        console.log('Month filter changed to:', currentFilters.month);
        updatePeriodInfo();
        loadInvoiceData();
    });
});

// Variabel global untuk menyimpan filter
let currentFilters = {
    year: new Date().getFullYear(),
    month: null
};

function loadInvoiceData() {
    const $refreshBtn = $('#refresh-data');
    const $refreshIcon = $refreshBtn.find('svg');
    
    console.log('Loading invoice data with filters:', currentFilters);
    
    // Tampilkan loading state
    $refreshBtn.addClass('loading').prop('disabled', true);
    $refreshIcon.addClass('animate-spin');
    
    // Siapkan parameter untuk request
    const params = {
        year: currentFilters.year
    };
    
    if (currentFilters.month) {
        params.month = currentFilters.month;
    }
    
    console.log('Request parameters:', params);

    $.ajax({
        url: '/invoice/data',
        method: 'GET',
        data: params,
        timeout: 30000, // 30 second timeout
        success: function(response) {
            console.log('Invoice data response:', response);
            
            if (response.success) {
                updateFinancialCards(response.data);
                updateDetailTables(response.data);
                showSuccessMessage('Data berhasil dimuat');
            } else {
                console.error('Server returned error:', response.message);
                showErrorMessage('Gagal memuat data: ' + (response.message || 'Unknown error'));
            }
        },
        error: function(xhr, status, error) {
            console.error('AJAX Error:', {
                status: status,
                error: error,
                responseText: xhr.responseText,
                statusCode: xhr.status
            });
            
            let errorMessage = 'Terjadi kesalahan saat memuat data.';
            
            if (xhr.status === 500) {
                errorMessage = 'Server error. Silakan periksa log server.';
            } else if (xhr.status === 404) {
                errorMessage = 'Endpoint tidak ditemukan.';
            } else if (status === 'timeout') {
                errorMessage = 'Request timeout. Silakan coba lagi.';
            }
            
            showErrorMessage(errorMessage);
        },
        complete: function() {
            // Hilangkan loading state
            $refreshBtn.removeClass('loading').prop('disabled', false);
            $refreshIcon.removeClass('animate-spin');
        }
    });
}

function updateFinancialCards(data) {
    console.log('Updating financial cards with data:', data);
    
    // Update semua card dengan animasi
    updateCardValue('#ar-revenue', data.ar_revenue || 0);
    updateCardValue('#ar-paid', data.ar_paid || 0);
    updateCardValue('#os-ar', data.os_ar || 0);
    updateCardValue('#ap-cost', data.ap_cost || 0);
    updateCardValue('#ap-paid', data.ap_paid || 0);
    updateCardValue('#os-ap', data.os_ap || 0);
    updateCardValue('#margin', data.margin || 0);
    updateCardValue('#ar-ap-balance', data.ar_ap_balance || 0);
}

function updateCardValue(selector, value) {
    const $element = $(selector);
    
    if ($element.length === 0) {
        console.warn('Element not found:', selector);
        return;
    }
    
    console.log(`Updating ${selector} with value:`, value);
    
    // Tambahkan class updating untuk animasi
    $element.addClass('updating');
    
    setTimeout(() => {
        $element.text(formatCurrency(value));
        $element.removeClass('updating');
    }, 150);
}

function updateDetailTables(data) {
    console.log('Updating detail tables with data:', data);
    
    // Update AR Details Table
    if (data.ar_details && Array.isArray(data.ar_details)) {
        console.log('AR Details:', data.ar_details);
        updateARTable(data.ar_details);
    } else {
        console.warn('AR details not found or not array:', data.ar_details);
        updateARTable([]);
    }
    
    // Update AP Details Table  
    if (data.ap_details && Array.isArray(data.ap_details)) {
        console.log('AP Details:', data.ap_details);
        updateAPTable(data.ap_details);
    } else {
        console.warn('AP details not found or not array:', data.ap_details);
        updateAPTable([]);
    }
}

function updateARTable(arDetails) {
    const $tbody = $('#ar-details-table');
    
    if ($tbody.length === 0) {
        console.error('AR details table not found');
        return;
    }
    
    $tbody.empty();
    
    if (arDetails.length === 0) {
        $tbody.append(`
            <tr>
                <td colspan="4" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">
                    <i class="fas fa-inbox text-2xl mb-2 block"></i>
                    Tidak ada data AR untuk periode ini
                </td>
            </tr>
        `);
        return;
    }
    
    arDetails.forEach(function(item) {
        const row = `
            <tr class="border-b border-gray-200 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                <td class="px-4 py-3 font-medium text-gray-900 dark:text-white">
                    ${item.project_name || 'N/A'}
                </td>
                <td class="px-4 py-3 text-gray-700 dark:text-gray-300 currency">
                    ${formatCurrency(item.total_revenue || 0)}
                </td>
                <td class="px-4 py-3 text-gray-700 dark:text-gray-300 currency">
                    ${formatCurrency(item.paid_amount || 0)}
                </td>
                <td class="px-4 py-3 text-gray-700 dark:text-gray-300 currency">
                    ${formatCurrency(item.outstanding_amount || 0)}
                </td>
            </tr>
        `;
        $tbody.append(row);
    });
    
    console.log(`AR table updated with ${arDetails.length} rows`);
}

function updateAPTable(apDetails) {
    const $tbody = $('#ap-details-table');
    
    if ($tbody.length === 0) {
        console.error('AP details table not found');
        return;
    }
    
    $tbody.empty();
    
    if (apDetails.length === 0) {
        $tbody.append(`
            <tr>
                <td colspan="4" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">
                    <i class="fas fa-inbox text-2xl mb-2 block"></i>
                    Tidak ada data AP untuk periode ini
                </td>
            </tr>
        `);
        return;
    }
    
    apDetails.forEach(function(item) {
        const row = `
            <tr class="border-b border-gray-200 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                <td class="px-4 py-3 font-medium text-gray-900 dark:text-white">
                    ${item.project_name || 'N/A'}
                </td>
                <td class="px-4 py-3 text-gray-700 dark:text-gray-300 currency">
                    ${formatCurrency(item.total_cost || 0)}
                </td>
                <td class="px-4 py-3 text-gray-700 dark:text-gray-300 currency">
                    ${formatCurrency(item.paid_amount || 0)}
                </td>
                <td class="px-4 py-3 text-gray-700 dark:text-gray-300 currency">
                    ${formatCurrency(item.outstanding_amount || 0)}
                </td>
            </tr>
        `;
        $tbody.append(row);
    });
    
    console.log(`AP table updated with ${apDetails.length} rows`);
}

function formatCurrency(amount) {
    if (amount === null || amount === undefined || isNaN(amount)) {
        return 'Rp 0';
    }
    
    const number = parseFloat(amount);
    return 'Rp ' + number.toLocaleString('id-ID', {
        minimumFractionDigits: 0,
        maximumFractionDigits: 0
    });
}

function updatePeriodInfo() {
    const monthNames = [
        '', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
    ];
    
    let periodText = `Tahun ${currentFilters.year}`;
    
    if (currentFilters.month) {
        periodText += ` - ${monthNames[parseInt(currentFilters.month)]}`;
    } else {
        periodText += ' - Semua Bulan';
    }
    
    const $periodInfo = $('#period-info');
    if ($periodInfo.length > 0) {
        $periodInfo.text(`Menampilkan data untuk: ${periodText}`);
    }
    
    console.log('Period info updated:', periodText);
}

function showSuccessMessage(message) {
    // Tampilkan notifikasi sukses yang tidak mengganggu
    console.log('Success:', message);
}

function showErrorMessage(message) {
    console.error('Error message:', message);
    
    // Tampilkan toast error
    if (typeof Swal !== 'undefined') {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: message,
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 5000,
            timerProgressBar: true
        });
    } else {
        alert(message);
    }
}

function formatNumber(num) {
    if (num === null || num === undefined || isNaN(num)) {
        return '0';
    }
    return parseFloat(num).toLocaleString('id-ID');
}

// Export functions untuk testing
if (typeof module !== 'undefined' && module.exports) {
    module.exports = {
        formatCurrency,
        formatNumber,
        updateFinancialCards,
        updateDetailTables
    };
} 
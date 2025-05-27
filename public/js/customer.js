$(document).ready(function() {
    // Setup CSRF token for all AJAX requests
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    // Check URL parameters for auto-showing customer details
    const urlParams = new URLSearchParams(window.location.search);
    const showDetailsId = urlParams.get('show_details');
    if (showDetailsId) {
        // Find the customer card with matching ID and trigger click
        $(`.customer-card[data-customer*='"id":${showDetailsId}']`).trigger('click');
    }

    // Image preview functionality
    $('#customer-image').change(function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                $('#preview-image').attr('src', e.target.result).removeClass('hidden');
                $('#upload-placeholder').addClass('hidden');
            }
            reader.readAsDataURL(file);
        }
    });

    // Form submission
    $('#add-customer-form').submit(function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const selectedCustomer = $('#name').val();

        if (!selectedCustomer) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Silakan pilih customer yang valid',
                confirmButtonColor: '#3085d6'
            });
            return;
        }

        // Show loading state
        Swal.fire({
            title: 'Menambahkan Customer...',
            html: 'Mohon tunggu...',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });
        
        $.ajax({
            url: '/customer',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success) {
                    // Create new customer card
                    const customer = response.data;
                    const customerCard = createCustomerCard(customer);
                    
                    // Add the new card to the grid
                    const grid = $('.grid').first();
                    if (grid.find('.col-span-2').length) {
                        // If "No customers found" message exists, remove it
                        grid.empty();
                    }
                    grid.prepend(customerCard);
                    
                    // Reset form and close modal
                    $('#add-customer-form')[0].reset();
                    $('#preview-image').attr('src', '#').addClass('hidden');
                    $('#upload-placeholder').removeClass('hidden');
                    $('#add-customer-modal').hide();
                    
                    // Show success message
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: 'Customer added successfully',
                        showConfirmButton: false,
                        timer: 1500
                    }).then(() => {
                        // Refresh the page after the success message is shown
                        window.location.reload();
                    });
                }
            },
            error: function(xhr) {
                let errorMessage = 'Error adding customer.';
                
                if (xhr.responseJSON && xhr.responseJSON.errors) {
                    const errors = xhr.responseJSON.errors;
                    errorMessage = Object.values(errors).flat().join('\n');
                }

                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: errorMessage,
                    confirmButtonColor: '#3085d6'
                });
            }
        });
    });

    // Reset form when modal is hidden
    $('[data-modal-hide="add-customer-modal"]').click(function() {
        Swal.fire({
            title: 'Are you sure?',
            text: "You will lose all entered data!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, close it!'
        }).then((result) => {
            if (result.isConfirmed) {
                $('#add-customer-form')[0].reset();
                $('#preview-image').addClass('hidden').attr('src', '#');
                $('#upload-placeholder').removeClass('hidden');
                $('#add-customer-modal').hide();
            }
        });
    });

    // Handle customer card click to show details
    $(document).on('click', '.customer-card', function(e) {
        e.preventDefault();
        const customer = $(this).data('customer');
        showCustomerDetails(customer);
    });

    // Handle closing customer detail modal
    $('[data-modal-hide="customer-detail-modal"]').click(function() {
        hideCustomerDetails();
    });

    // Close modal when clicking outside
    $(document).on('click', '#customer-detail-modal', function(e) {
        if (e.target === this) {
            hideCustomerDetails();
        }
    });

    // Close modal with Escape key
    $(document).keydown(function(e) {
        if (e.key === "Escape" && !$('#customer-detail-modal').hasClass('hidden')) {
            hideCustomerDetails();
        }
    });

    // Theme toggle functionality
    initializeThemeToggle();
});

function createCustomerCard(customer) {
    const imageUrl = customer.image 
        ? `/storage/${customer.image}` 
        : '/img/default-customer.png';
    
    return `
        <div class="flex rounded-sm bg-gray-50 h-auto dark:bg-gray-800">
            <a href="#" class="customer-card flex flex-col items-center bg-white border border-gray-200 rounded-lg shadow-sm md:flex-row md:max-w-xl hover:bg-gray-100 dark:border-gray-700 dark:bg-gray-800 dark:hover:bg-gray-700" data-customer='${JSON.stringify(customer)}'>
                <img class="p-4 object-cover w-full rounded-t-lg h-96 md:h-auto md:w-48 md:rounded-none md:rounded-s-lg"
                    src="${imageUrl}" 
                    alt="${customer.name}">
                <div class="justify-between p-4 leading-normal">
                    <h5 class="mb-2 text-2xl font-bold tracking-tight text-gray-900 dark:text-white">${customer.name}</h5>
                    <p class="mb-3 font-normal text-gray-700 dark:text-gray-400">${customer.address}</p>
                    <p class="mb-3 font-normal text-gray-700 dark:text-gray-400">
                        <strong>Phone:</strong> ${customer.phone}<br>
                        <strong>Email:</strong> ${customer.email}
                    </p>
                    ${customer.description ? `
                    <p class="mb-3 font-normal text-gray-700 dark:text-gray-400">${customer.description}</p>
                    ` : ''}
                </div>
            </a>
        </div>
    `;
}

function showCustomerDetails(customer) {
    // Set customer details in modal
    $('#detail-customer-image').attr('src', customer.image ? `/storage/${customer.image}` : '/img/default-customer.png');
    $('#detail-customer-name').text(customer.name);
    $('#detail-customer-phone').text(customer.phone);
    $('#detail-customer-email').text(customer.email);
    $('#detail-customer-address').text(customer.address);
    
    if (customer.description) {
        $('#detail-customer-description').text(customer.description);
        $('#detail-description-container').show();
    } else {
        $('#detail-description-container').hide();
    }

    // Show modal with animation
    const modal = $('#customer-detail-modal');
    modal.removeClass('hidden')
         .addClass('flex animate-fadeIn');
    
    // Animate modal content
    const modalContent = modal.find('.relative.bg-white');
    modalContent.addClass('animate-slideIn');

    // Load project summary
    loadProjectSummary(customer.name);
}

function loadProjectSummary(customerName) {
    // Show loading state
    $('#project-loading').show();
    $('#project-summary-content').hide();
    $('#no-projects').hide();

    // Make AJAX request to get project summary
    $.ajax({
        url: `/customer/${encodeURIComponent(customerName)}/projects`,
        method: 'GET',
        success: function(response) {
            $('#project-loading').hide();
            
            if (response.success && response.data) {
                displayProjectSummary(response.data);
                $('#project-summary-content').show();
            } else {
                $('#no-projects').show();
            }
        },
        error: function(xhr) {
            $('#project-loading').hide();
            console.error('Error loading project summary:', xhr);
            
            if (xhr.status === 404 || (xhr.responseJSON && !xhr.responseJSON.success)) {
                $('#no-projects').show();
            } else {
                // Show error message
                $('#project-summary-section').html(`
                    <div class="text-center py-8">
                        <svg class="mx-auto h-12 w-12 text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">Error loading projects</h3>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Failed to load project data. Please try again.</p>
                    </div>
                `);
            }
        }
    });
}

function displayProjectSummary(data) {
    // Update summary cards
    $('#total-projects').text(data.total_projects);
    $('#total-revenue').text(formatCurrency(data.total_gt_rev));
    $('#total-cost').text(formatCurrency(data.total_gt_cost));
    $('#total-margin').text(formatCurrency(data.total_gt_margin));

    // Update financial details
    $('#total-sum-ar').text(formatCurrency(data.total_sum_ar));
    $('#total-ar-paid').text(formatCurrency(data.total_ar_paid));
    $('#total-ar-os').text(formatCurrency(data.total_ar_os));
    $('#total-sum-ap').text(formatCurrency(data.total_sum_ap));
    $('#total-ap-paid').text(formatCurrency(data.total_ap_paid));
    $('#total-ap-os').text(formatCurrency(data.total_ap_os));
    $('#total-todo').text(formatCurrency(data.total_todo));
    $('#total-ar-ap').text(formatCurrency(data.total_ar_ap));

    // Update projects list
    const projectsList = $('#projects-list');
    projectsList.empty();

    if (data.projects_detail && data.projects_detail.length > 0) {
        data.projects_detail.forEach(function(project) {
            const projectCard = `
                <div class="bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg p-4">
                    <div class="flex justify-between items-start mb-2">
                        <div>
                            <h6 class="font-semibold text-gray-900 dark:text-white">${project.coa}</h6>
                            <p class="text-sm text-gray-600 dark:text-gray-400">${project.activity} - ${project.prodi} (${project.grade})</p>
                        </div>
                        <span class="px-2 py-1 text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200 rounded-full">
                            ID: ${project.id}
                        </span>
                    </div>
                    <div class="grid grid-cols-3 gap-4 text-sm">
                        <div>
                            <span class="text-gray-600 dark:text-gray-400">Revenue:</span>
                            <p class="font-medium text-green-600 dark:text-green-400">${formatCurrency(project.gt_rev)}</p>
                        </div>
                        <div>
                            <span class="text-gray-600 dark:text-gray-400">Cost:</span>
                            <p class="font-medium text-red-600 dark:text-red-400">${formatCurrency(project.gt_cost)}</p>
                        </div>
                        <div>
                            <span class="text-gray-600 dark:text-gray-400">Margin:</span>
                            <p class="font-medium text-purple-600 dark:text-purple-400">${formatCurrency(project.gt_margin)}</p>
                        </div>
                    </div>
                </div>
            `;
            projectsList.append(projectCard);
        });
    }
}

function formatCurrency(amount) {
    if (amount === null || amount === undefined) {
        return 'Rp 0';
    }
    
    // Convert to number if it's a string
    const numAmount = typeof amount === 'string' ? parseFloat(amount) : amount;
    
    // Format with Indonesian locale
    return 'Rp ' + numAmount.toLocaleString('id-ID', {
        minimumFractionDigits: 0,
        maximumFractionDigits: 0
    });
}

function hideCustomerDetails() {
    const modal = $('#customer-detail-modal');
    const modalContent = modal.find('.relative.bg-white');

    // Add closing animations
    modalContent.removeClass('animate-slideIn')
               .addClass('animate-slideOut');
    modal.removeClass('animate-fadeIn')
         .addClass('animate-fadeOut');

    // Hide modal after animation completes
    setTimeout(() => {
        modal.addClass('hidden')
             .removeClass('flex animate-fadeOut');
        modalContent.removeClass('animate-slideOut');
    }, 300);
}

function initializeThemeToggle() {
    var themeToggleDarkIcon = document.getElementById('theme-toggle-dark-icon');
    var themeToggleLightIcon = document.getElementById('theme-toggle-light-icon');
    var themeToggleText = document.getElementById('theme-toggle-text');
    var themeToggleBtn = document.getElementById('theme-toggle');

    // Change the icons inside the button based on previous settings
    if (localStorage.getItem('color-theme') === 'dark' || (!('color-theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
        themeToggleLightIcon.classList.remove('hidden');
        themeToggleText.textContent = 'Toggle Light Mode';
    } else {
        themeToggleDarkIcon.classList.remove('hidden');
        themeToggleText.textContent = 'Toggle Dark Mode';
    }

    themeToggleBtn.addEventListener('click', function() {
        // Toggle icons
        themeToggleDarkIcon.classList.toggle('hidden');
        themeToggleLightIcon.classList.toggle('hidden');

        // If is set in localStorage
        if (localStorage.getItem('color-theme')) {
            if (localStorage.getItem('color-theme') === 'light') {
                document.documentElement.classList.add('dark');
                localStorage.setItem('color-theme', 'dark');
                themeToggleText.textContent = 'Toggle Light Mode';
            } else {
                document.documentElement.classList.remove('dark');
                localStorage.setItem('color-theme', 'light');
                themeToggleText.textContent = 'Toggle Dark Mode';
            }
        } else {
            if (document.documentElement.classList.contains('dark')) {
                document.documentElement.classList.remove('dark');
                localStorage.setItem('color-theme', 'light');
                themeToggleText.textContent = 'Toggle Dark Mode';
            } else {
                document.documentElement.classList.add('dark');
                localStorage.setItem('color-theme', 'dark');
                themeToggleText.textContent = 'Toggle Light Mode';
            }
        }
    });
}
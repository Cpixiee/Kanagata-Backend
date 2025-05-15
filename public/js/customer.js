$(document).ready(function() {
    // Setup CSRF token for all AJAX requests
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

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

        // Show loading state
        Swal.fire({
            title: 'Adding Customer...',
            html: 'Please wait...',
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
});

function createCustomerCard(customer) {
    const imageUrl = customer.image 
        ? `/storage/${customer.image}` 
        : '/img/default-customer.png';
    
    return `
        <div class="flex rounded-sm bg-gray-50 h-auto dark:bg-gray-800">
            <a href="#" class="flex flex-col items-center bg-white border border-gray-200 rounded-lg shadow-sm md:flex-row md:max-w-xl hover:bg-gray-100 dark:border-gray-700 dark:bg-gray-800 dark:hover:bg-gray-700">
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
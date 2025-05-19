// Logsheet functionality
const Logsheet = {
    viewModal: null,
    addModal: null,
    table: null,

    init: function() {
        // Initialize modals
        this.viewModal = new Modal(document.getElementById('view-logsheet-modal'));
        this.addModal = new Modal(document.getElementById('add-logsheet-modal'));
        
        this.setupAjax();
        this.initializeDataTable();
        this.bindEvents();
    },

    setupAjax: function() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
    },

    initializeDataTable: function() {
        // Initialize DataTable only if it hasn't been initialized yet
        if (!this.table) {
            this.table = $('table').DataTable({
                dom: 't<"bottom"ip>', // Only show table, info, and pagination
                pageLength: 10,
                lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
                processing: true,
                language: {
                    processing: "Sedang memproses...",
                    info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                    infoEmpty: "Menampilkan 0 sampai 0 dari 0 data",
                    infoFiltered: "(disaring dari _MAX_ total data)",
                    zeroRecords: "Tidak ditemukan data yang sesuai",
                    emptyTable: "Tidak ada data yang tersedia",
                    paginate: {
                        first: "Pertama",
                        previous: "Sebelumnya",
                        next: "Selanjutnya",
                        last: "Terakhir"
                    }
                },
                ordering: true,
                responsive: true,
                columnDefs: [
                    {
                        targets: [0, -1], // NO column and Action column
                        orderable: false
                    }
                ],
                drawCallback: function(settings) {
                    // Update display info
                    var info = this.api().page.info();
                    $('#display-start').text(info.start + 1);
                    $('#display-end').text(info.end);
                    $('#display-total').text(info.recordsTotal);
                    
                    // Update row numbers
                    this.api().column(0).nodes().each(function(cell, i) {
                        cell.innerHTML = i + 1 + info.start;
                    });
                }
            });

            // Connect custom search input
            $('#table-search').on('keyup', function() {
                Logsheet.table.search(this.value).draw();
            });

            // Connect custom length select
            $('#table-length').on('change', function() {
                Logsheet.table.page.len(this.value).draw();
            });
        }
    },

    bindEvents: function() {
        this.bindRowClick();
        this.bindAddForm();
        this.bindEditButton();
        this.bindDeleteButton();
    },

    bindRowClick: function() {
        $('.logsheet-row').on('click', function() {
            const logsheet = $(this).data('logsheet');
            Logsheet.showDetails(logsheet);
        });
    },

    showDetails: function(logsheet) {
        // Populate the view modal with logsheet data
        $('#view-coa').text(logsheet.coa);
        $('#view-customer').text(logsheet.customer);
        $('#view-activity').text(logsheet.activity);
        $('#view-prodi').text(logsheet.prodi);
        $('#view-grade').text(logsheet.grade);
        $('#view-seq').text(logsheet.seq);
        $('#view-quantity-1').text(logsheet.quantity_1);
        $('#view-rate-1').text(this.formatCurrency(logsheet.rate_1));
        $('#view-revenue').text(this.formatCurrency(logsheet.revenue));
        $('#view-ar-status').text(logsheet.ar_status);
        $('#view-tutor').text(logsheet.tutor);
        $('#view-quantity-2').text(logsheet.quantity_2);
        $('#view-rate-2').text(this.formatCurrency(logsheet.rate_2));
        $('#view-cost').text(this.formatCurrency(logsheet.cost));
        $('#view-ap-status').text(logsheet.ap_status);

        // Show the modal
        this.viewModal.show();
    },

    bindAddForm: function() {
        // Calculate values on input
        ['quantity_1', 'rate_1', 'quantity_2', 'rate_2'].forEach(id => {
            $(`#${id}`).on('input', function() {
                Logsheet.calculateValues();
            });
        });

        // Prevent form from submitting normally
        $('#logsheetForm').on('submit', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            console.log('Form submitted');
            
            const formData = new FormData(this);
            const jsonData = {};
            
            formData.forEach((value, key) => {
                jsonData[key] = value;
            });
            
            // Add calculated values
            jsonData.revenue = $('#revenue').val();
            jsonData.cost = $('#cost').val();

            console.log('Sending data:', jsonData);

            $.ajax({
                url: '/logsheet',
                type: 'POST',
                data: jsonData,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                    'Accept': 'application/json'
                },
                success: function(response) {
                    console.log('Success:', response);
                    Logsheet.addModal.hide();
                    showNotification('success', response.message);
                },
                error: function(xhr, status, error) {
                    console.error('Error:', { xhr, status, error });
                    const errors = xhr.responseJSON?.errors;
                    let errorMessage = '';
                    if (errors) {
                        for (const key in errors) {
                            errorMessage += `${errors[key]}\n`;
                        }
                    } else {
                        errorMessage = 'Something went wrong. Please try again.';
                    }
                    showNotification('error', errorMessage, 'Form Submission Error');
                }
            });

            return false;
        });
    },

    calculateValues: function() {
        const quantity1 = parseFloat($('#quantity_1').val()) || 0;
        const rate1 = parseFloat($('#rate_1').val()) || 0;
        const quantity2 = parseFloat($('#quantity_2').val()) || 0;
        const rate2 = parseFloat($('#rate_2').val()) || 0;

        const revenue = quantity1 * rate1;
        const cost = quantity2 * rate2;

        $('#revenue').val(revenue.toFixed(2));
        $('#cost').val(cost.toFixed(2));
    },

    bindEditButton: function() {
        $('.edit-logsheet').on('click', function(e) {
            e.stopPropagation();
            const logsheetId = $(this).closest('tr').data('logsheet-id');
            
            $.get(`/logsheet/${logsheetId}/edit`, function(logsheet) {
                // Populate the edit modal with logsheet data
                $('#edit-logsheet-modal').modal('show');
                $('#edit-logsheet-form input[name="coa"]').val(logsheet.coa);
                $('#edit-logsheet-form select[name="customer"]').val(logsheet.customer);
                // ... populate other fields
            });
        });
    },

    bindDeleteButton: function() {
        $('.delete-logsheet').on('click', function(e) {
            e.stopPropagation();
            const logsheetId = $(this).closest('tr').data('logsheet-id');
            
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: `/logsheet/${logsheetId}`,
                        type: 'DELETE',
                        success: function(response) {
                            showNotification('success', 'Logsheet entry has been deleted successfully!');
                            setTimeout(() => {
                                window.location.href = '/logsheet';
                            }, 1000);
                        },
                        error: function(xhr) {
                            let errorMessage = 'An error occurred while deleting the logsheet.';
                            
                            if (xhr.responseJSON && xhr.responseJSON.error) {
                                errorMessage = xhr.responseJSON.error;
                            }

                            showNotification('error', errorMessage, 'Delete Failed');
                        }
                    });
                }
            });
        });
    },

    formatCurrency: function(value) {
        return new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR',
            minimumFractionDigits: 0,
            maximumFractionDigits: 0
        }).format(value);
    }
};

// Initialize when document is ready
$(document).ready(function() {
    Logsheet.init();

    // Setup AJAX CSRF token
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    // Initialize edit modal
    const editModalElement = document.getElementById('edit-logsheet-modal');
    const editModal = new Modal(editModalElement, {
        placement: 'center',
        backdrop: 'static',
        closable: true
    });

    // Handle form submission for add logsheet
    $('#logsheetForm').on('submit', function(e) {
        e.preventDefault();
        handleFormSubmission($(this), false);
    });

    // Handle form submission for edit logsheet
    $('#editLogsheetForm').on('submit', function(e) {
        e.preventDefault();
        handleFormSubmission($(this), true);
    });

    // Common form submission handler
    function handleFormSubmission($form, isEdit) {
        Swal.fire({
            title: 'Menyimpan...',
            text: 'Mohon tunggu sebentar',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        const formData = new FormData($form[0]);
        const jsonData = {};
        
        formData.forEach((value, key) => {
            jsonData[key] = value;
        });
        
        // Add calculated values
        if (!isEdit) {
        jsonData.revenue = $('#revenue').val();
        jsonData.cost = $('#cost').val();
        } else {
            jsonData.revenue = $('#edit_revenue').val();
            jsonData.cost = $('#edit_cost').val();
        }

        $.ajax({
            url: $form.attr('action'),
            method: 'POST',
            data: jsonData,
            success: function(response) {
                Swal.fire({
                    title: 'Berhasil!',
                    text: isEdit ? 'Data logsheet berhasil diperbarui' : 'Data logsheet berhasil disimpan',
                    icon: 'success',
                    timer: 1500,
                    showConfirmButton: false
                }).then(() => {
                    window.location.reload();
                });
            },
            error: function(xhr) {
                let errorMessage = 'Terjadi kesalahan saat menyimpan data';
                if (xhr.responseJSON && xhr.responseJSON.errors) {
                    errorMessage = Object.values(xhr.responseJSON.errors).flat().join('\n');
                } else if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                }
                
                Swal.fire({
                    title: 'Error!',
                    text: errorMessage,
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
            }
        });
    }

    // Handle edit button click
    $(document).on('click', '.edit-logsheet', function(e) {
        e.preventDefault();
        const logsheetId = $(this).data('logsheet-id');
        
        Swal.fire({
            title: 'Loading...',
            text: 'Mengambil data logsheet',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        $.ajax({
            url: `/logsheet/${logsheetId}/edit`,
            method: 'GET',
            success: function(data) {
                Swal.close();
                
                // Populate edit form
                $('#edit_id').val(data.id);
                $('#edit_coa').val(data.coa);
                $('#edit_customer').val(data.customer);
                $('#edit_activity').val(data.activity);
                $('#edit_prodi').val(data.prodi);
                $('#edit_grade').val(data.grade);
                $('#edit_seq').val(data.seq);
                $('#edit_quantity_1').val(data.quantity_1);
                $('#edit_rate_1').val(data.rate_1);
                $('#edit_revenue').val(data.revenue);
                $('#edit_ar_status').val(data.ar_status);
                $('#edit_tutor').val(data.tutor);
                $('#edit_quantity_2').val(data.quantity_2);
                $('#edit_rate_2').val(data.rate_2);
                $('#edit_cost').val(data.cost);
                $('#edit_ap_status').val(data.ap_status);
                
                // Show edit modal
                if (editModalElement) {
                    editModal.show();
                }
            },
            error: function(xhr) {
                Swal.fire({
                    title: 'Error!',
                    text: 'Gagal mengambil data logsheet',
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
            }
        });
    });

    // Handle delete button
    $(document).on('click', '.delete-logsheet', function(e) {
        e.preventDefault();
        const form = $(this).closest('form');
        
        Swal.fire({
            title: 'Apakah Anda yakin?',
            text: "Data yang dihapus tidak dapat dikembalikan!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, hapus!',
            cancelButtonText: 'Batal',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Menghapus...',
                    text: 'Mohon tunggu sebentar',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                $.ajax({
                    url: form.attr('action'),
                    type: 'POST',
                    data: form.serialize(),
                    success: function(response) {
                        Swal.fire({
                            title: 'Terhapus!',
                            text: 'Data berhasil dihapus.',
                            icon: 'success',
                            timer: 1500,
                            showConfirmButton: false
                        }).then(() => {
                            window.location.reload();
                        });
                    },
                    error: function(xhr) {
                        Swal.fire({
                            title: 'Error!',
                            text: 'Gagal menghapus data.',
                            icon: 'error',
                            confirmButtonText: 'OK'
                        });
                    }
                });
            }
        });
    });

    // Auto calculate for add form
    $('#quantity_1, #rate_1, #quantity_2, #rate_2').on('input', function() {
        calculateValues(false);
    });

    // Auto calculate for edit form
    $('#edit_quantity_1, #edit_rate_1, #edit_quantity_2, #edit_rate_2').on('input', function() {
        calculateValues(true);
    });

    function calculateValues(isEdit) {
        const prefix = isEdit ? 'edit_' : '';
        const quantity1 = parseFloat($(`#${prefix}quantity_1`).val()) || 0;
        const rate1 = parseFloat($(`#${prefix}rate_1`).val()) || 0;
        const quantity2 = parseFloat($(`#${prefix}quantity_2`).val()) || 0;
        const rate2 = parseFloat($(`#${prefix}rate_2`).val()) || 0;

        const revenue = quantity1 * rate1;
        const cost = quantity2 * rate2;

        $(`#${prefix}revenue`).val(revenue.toFixed(2));
        $(`#${prefix}cost`).val(cost.toFixed(2));
    }

    // Reset forms when modals are hidden
    $('#add-logsheet-modal, #edit-logsheet-modal').on('hidden.bs.modal', function () {
        const $form = $(this).find('form');
        $form[0].reset();
        if ($(this).attr('id') === 'edit-logsheet-modal') {
            $form.find('input[name="_method"]').val('PUT');
        }
    });

    // Toast notification setup
    const Toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true,
        didOpen: (toast) => {
            toast.addEventListener('mouseenter', Swal.stopTimer);
            toast.addEventListener('mouseleave', Swal.resumeTimer);
        }
    });

    // Function to show success message
    function showSuccessMessage(message) {
        Toast.fire({
            icon: 'success',
            title: message
        });
    }

    // Function to show error message
    function showErrorMessage(message) {
        Toast.fire({
            icon: 'error',
            title: message
        });
    }

    // Initialize all modals
    document.addEventListener('DOMContentLoaded', function() {
        const modals = document.querySelectorAll('[data-modal-target]');
        modals.forEach(modal => {
            new Modal(modal);
        });
    });

    // Handle project selection for add form
    $('#project_id').on('change', function() {
        const selectedOption = $(this).find('option:selected');
        if (selectedOption.val()) {
            $('#coa').val(selectedOption.data('coa'));
            $('#customer').val(selectedOption.data('customer'));
            $('#activity').val(selectedOption.data('activity'));
            $('#prodi').val(selectedOption.data('prodi'));
            $('#grade').val(selectedOption.data('grade'));
            $('#rate_1').val(selectedOption.data('rate1'));
            $('#rate_2').val(selectedOption.data('rate2'));
        } else {
            // Clear form if no project selected
            $('#coa, #customer, #activity, #prodi, #grade, #rate_1, #rate_2').val('');
        }
    });

    // Handle project selection for edit form
    $('#edit_project_id').on('change', function() {
        const selectedOption = $(this).find('option:selected');
        if (selectedOption.val()) {
            $('#edit_coa').val(selectedOption.data('coa'));
            $('#edit_customer').val(selectedOption.data('customer'));
            $('#edit_activity').val(selectedOption.data('activity'));
            $('#edit_prodi').val(selectedOption.data('prodi'));
            $('#edit_grade').val(selectedOption.data('grade'));
            $('#edit_rate_1').val(selectedOption.data('rate1'));
            $('#edit_rate_2').val(selectedOption.data('rate2'));
        } else {
            // Clear form if no project selected
            $('#edit_coa, #edit_customer, #edit_activity, #edit_prodi, #edit_grade, #edit_rate_1, #edit_rate_2').val('');
        }
    });
});
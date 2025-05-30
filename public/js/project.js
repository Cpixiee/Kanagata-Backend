$(document).ready(function() {
    // Setup CSRF token untuk semua request AJAX
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    // Hapus DataTable yang ada jika sudah ada
    if ($.fn.DataTable.isDataTable('table')) {
        $('table').DataTable().destroy();
    }

    // Inisialisasi DataTable
    const table = $('table').DataTable({
        processing: true,
        serverSide: false,
        destroy: true,
        scrollX: true,
        scrollCollapse: true,
        "order": [],
        dom: "<'dt-container'<'dataTables_length'l><'dataTables_filter'f>>" +
             "<'dataTables_scroll't>" +
             "<'dt-footer'<'dataTables_info'i><'dataTables_paginate'p>>",
        language: {
            processing: "Sedang memproses...",
            search: "Pencarian:",
            lengthMenu: "Tampilkan _MENU_ data",
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
        columnDefs: [
            {
                targets: [0, -1],
                orderable: false
            }
        ]
    });

    // Hubungkan input pencarian dan panjang tabel
    $('#table-search').on('keyup', function() {
        table.search(this.value).draw();
    });

    $('#table-length').on('change', function() {
        table.page.len(this.value).draw();
    });

    // Menyembunyikan scrollbar header
    $('.dataTables_scrollHead').css('overflow', 'hidden');

    // Event handler untuk tombol edit
    $(document).on('click', '.edit-project', function(e) {
        e.preventDefault();
        const projectId = $(this).data('project-id');
        
        Swal.fire({
            title: 'Memuat...',
            text: 'Mengambil data proyek',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        $.ajax({
            url: `/projects/${projectId}/edit`,
            method: 'GET',
            success: function(data) {
                Swal.close();
                
                // Isi form dengan data
                $('#edit-project-form').attr('action', `/projects/${projectId}`);
                $('#edit-coa').val(data.coa);
                $('#edit-customer').val(data.customer);
                $('#edit-activity').val(data.activity);
                $('#edit-prodi').val(data.prodi);
                $('#edit-grade').val(data.grade);
                $('#edit-quantity_1').val(data.quantity_1);
                $('#edit-rate_1').val(data.rate_1);
                $('#edit-quantity_2').val(data.quantity_2);
                $('#edit-rate_2').val(data.rate_2);
                
                // Tampilkan modal
                const editModal = document.getElementById('edit-project-modal');
                const modal = new Modal(editModal);
                modal.show();
            },
            error: function(xhr) {
                Swal.fire({
                    title: 'Error!',
                    text: 'Gagal mengambil data proyek',
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
            }
        });
    });

    // Event handler untuk tombol Details
    $(document).on('click', '.view-project-details', function(e) {
        e.preventDefault();
        const projectId = $(this).data('project-id');
        
        Swal.fire({
            title: 'Memuat...',
            text: 'Mengambil detail proyek',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        $.ajax({
            url: `/projects/${projectId}/details`,
            method: 'GET',
            success: function(response) {
                Swal.close();
                
                if (response.success && response.data) {
                    populateProjectDetails(response.data);
                    
                    // Tampilkan modal
                    const detailsModal = document.getElementById('project-details-modal');
                    const modal = new Modal(detailsModal);
                    modal.show();
                } else {
                    Swal.fire({
                        title: 'Error!',
                        text: 'Gagal mengambil detail proyek',
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                }
            },
            error: function(xhr) {
                Swal.fire({
                    title: 'Error!',
                    text: 'Gagal mengambil detail proyek',
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
            }
        });
    });

    // Fungsi untuk mengisi data ke modal details
    function populateProjectDetails(data) {
        // Basic Information
        $('#detail-coa').text(data.coa || '-');
        $('#detail-customer').text(data.customer || '-');
        $('#detail-activity').text(data.activity || '-');
        $('#detail-prodi').text(data.prodi || '-');
        $('#detail-grade').text(data.grade || '-');

        // Revenue Details
        $('#detail-quantity-1').text(data.quantity_1 || '-');
        $('#detail-rate-1').text(formatCurrency(data.rate_1 || 0));
        $('#detail-gt-rev').text(formatCurrency(data.gt_rev || 0));

        // Cost Details
        $('#detail-quantity-2').text(data.quantity_2 || '-');
        $('#detail-rate-2').text(formatCurrency(data.rate_2 || 0));
        $('#detail-gt-cost').text(formatCurrency(data.gt_cost || 0));

        // Financial Summary
        $('#detail-gt-margin').text(formatCurrency(data.gt_margin || 0));
        $('#detail-sum-ar').text(formatCurrency(data.sum_ar || 0));
        $('#detail-sum-ap').text(formatCurrency(data.sum_ap || 0));
        $('#detail-ar-ap').text(formatCurrency(data.ar_ap || 0));

        // AR/AP Breakdown
        $('#detail-ar-paid').text(formatCurrency(data.ar_paid || 0));
        $('#detail-ar-os').text(formatCurrency(data.ar_os || 0));
        $('#detail-todo').text(formatCurrency(data.todo || 0));
        $('#detail-ap-paid').text(formatCurrency(data.ap_paid || 0));
        $('#detail-ap-os').text(formatCurrency(data.ap_os || 0));

        // Logsheet Summary
        $('#detail-total-revenue').text(formatCurrency(data.logsheet_total_revenue || 0));
        $('#detail-total-cost').text(formatCurrency(data.logsheet_total_cost || 0));
        $('#detail-total-margin').text(formatCurrency(data.logsheet_total_margin || 0));
    }

    // Fungsi untuk format currency
    function formatCurrency(amount) {
        if (amount === null || amount === undefined) {
            return 'Rp 0';
        }
        
        const numAmount = typeof amount === 'string' ? parseFloat(amount) : amount;
        
        return 'Rp ' + numAmount.toLocaleString('id-ID', {
            minimumFractionDigits: 0,
            maximumFractionDigits: 0
        });
    }

    // Handler untuk konfirmasi penghapusan
    $('button.delete-project').on('click', function(e) {
        e.preventDefault();
        const form = $(this).closest('form');
        
        Swal.fire({
            title: 'Apakah Anda yakin?',
            text: "Data proyek akan dihapus secara permanen!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                form.submit();
            }
        });
    });

    // Tangani pengiriman form
    $('#add-project-form').on('submit', function(e) {
        e.preventDefault();
        const formData = $(this).serialize();

        $.ajax({
            url: $(this).attr('action'),
            type: 'POST',
            data: formData,
            success: function(response) {
                if (response.request) {
                    // This is a review request
                    Swal.fire({
                        icon: 'info',
                        title: 'Request Submitted',
                        text: 'Your request has been submitted for review and is pending approval.',
                        confirmButtonText: 'OK'
                    }).then((result) => {
                        $('#add-project-modal').hide();
                        location.reload();
                    });
                } else {
                    // Direct success (admin)
                    Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: response.message,
                        confirmButtonText: 'OK'
                    }).then((result) => {
                        $('#add-project-modal').hide();
                        location.reload();
                    });
                }
            },
            error: function(xhr) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'An error occurred while processing your request.',
                    confirmButtonText: 'OK'
                });
            }
        });
    });

    $('#edit-project-form').on('submit', function(e) {
        e.preventDefault();
        const formData = $(this).serialize() + '&_method=PUT';
        const projectId = $(this).attr('action').split('/').pop();

        // Show loading state
        Swal.fire({
            title: 'Menyimpan...',
            text: 'Mohon tunggu sebentar',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        $.ajax({
            url: `/projects/${projectId}`,
            type: 'POST',
            data: formData,
            success: function(response) {
                if (response.request) {
                    // This is a review request
                    Swal.fire({
                        icon: 'info',
                        title: 'Request Submitted',
                        text: 'Your update request has been submitted for review and is pending approval.',
                        confirmButtonText: 'OK'
                    }).then((result) => {
                        $('#edit-project-modal').hide();
                        location.reload();
                    });
                } else {
                    // Direct success (admin)
                    Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: response.message || 'Project updated successfully',
                        confirmButtonText: 'OK'
                    }).then((result) => {
                        $('#edit-project-modal').hide();
                        location.reload();
                    });
                }
            },
            error: function(xhr, status, error) {
                let errorMessage = 'An error occurred while processing your request.';
                
                if (xhr.status === 422) {
                    const errors = xhr.responseJSON.errors;
                    const errorMessages = [];
                    Object.keys(errors).forEach(field => {
                        errorMessages.push(errors[field][0]);
                        const input = $(`#edit-${field}`);
                        input.addClass('is-invalid');
                        input.after(`<div class="invalid-feedback">${errors[field][0]}</div>`);
                    });
                    errorMessage = errorMessages.join('\n');
                }
                
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: errorMessage,
                    confirmButtonText: 'OK'
                });
            }
        });
    });

    $('.delete-project').click(function(e) {
        e.preventDefault();
        const form = $(this).closest('form');

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
                    url: form.attr('action'),
                    type: 'DELETE',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response.request) {
                            // This is a review request
                            Swal.fire({
                                icon: 'info',
                                title: 'Request Submitted',
                                text: 'Your deletion request has been submitted for review and is pending approval.',
                                confirmButtonText: 'OK'
                            }).then((result) => {
                                location.reload();
                            });
                        } else {
                            // Direct success (admin)
                            Swal.fire({
                                icon: 'success',
                                title: 'Deleted!',
                                text: response.message,
                                confirmButtonText: 'OK'
                            }).then((result) => {
                                location.reload();
                            });
                        }
                    },
                    error: function(xhr) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'An error occurred while processing your request.',
                            confirmButtonText: 'OK'
                        });
                    }
                });
            }
        });
    });

    // Hitung nilai otomatis
    function calculateValues(prefix = '') {
        const quantity1 = parseFloat($(`#${prefix}quantity_1`).val()) || 0;
        const rate1 = parseFloat($(`#${prefix}rate_1`).val()) || 0;
        const quantity2 = parseFloat($(`#${prefix}quantity_2`).val()) || 0;
        const rate2 = parseFloat($(`#${prefix}rate_2`).val()) || 0;

        const gtRev = quantity1 * rate1;
        const gtCost = quantity2 * rate2;
        const gtMargin = gtRev - gtCost;

        // Hanya update nilai yang ada di form
        if ($(`#${prefix}gt_rev`).length) $(`#${prefix}gt_rev`).val(gtRev.toFixed(2));
        if ($(`#${prefix}gt_cost`).length) $(`#${prefix}gt_cost`).val(gtCost.toFixed(2));
        if ($(`#${prefix}gt_margin`).length) $(`#${prefix}gt_margin`).val(gtMargin.toFixed(2));
    }

    // Ikat event perhitungan
    ['quantity_1', 'rate_1', 'quantity_2', 'rate_2'].forEach(field => {
        $(`#${field}, #edit-${field}`).on('input', function() {
            calculateValues($(this).attr('id').startsWith('edit-') ? 'edit-' : '');
        });
    });
});

function showSuccessMessage(message) {
    Swal.fire({
        title: 'Berhasil!',
        text: message,
        icon: 'success',
        timer: 1500,
        showConfirmButton: false
    });
}

function showErrorMessage(message) {
    Swal.fire({
        title: 'Error!',
        text: message,
        icon: 'error',
        confirmButtonText: 'OK'
    });
}

// Form submission handlers
$('#projectForm, #editProjectForm').on('submit', function(e) {
    e.preventDefault();
    const form = $(this);
    const isEdit = form.attr('id') === 'editProjectForm';
    const formData = new FormData(this);
    const jsonData = {};
    formData.forEach((value, key) => {
        jsonData[key] = value;
    });

    if (isEdit) {
        jsonData._method = 'PUT';
    }

    Swal.fire({
        title: 'Konfirmasi',
        text: 'Permintaan akan dikirim untuk ditinjau. Lanjutkan?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Ya, Kirim',
        cancelButtonText: 'Batal',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: form.attr('action'),
                type: 'POST',
                data: jsonData,
                success: function(response) {
                    Swal.fire({
                        icon: 'info',
                        title: 'Permintaan Terkirim',
                        text: 'Permintaan telah dikirim untuk ditinjau',
                        timer: 2000,
                        showConfirmButton: false
                    }).then(() => {
                        // Close modal if it exists
                        const modalId = isEdit ? 'edit-project-modal' : 'add-project-modal';
                        const modal = document.getElementById(modalId);
                        if (modal) {
                            const modalInstance = flowbite.Modal.getInstance(modal);
                            modalInstance.hide();
                        }
                    });
                },
                error: function(xhr) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Gagal mengirim permintaan. Silakan coba lagi.'
                    });
                }
            });
        }
    });
});

// Delete project handler
$('.delete-project').on('click', function(e) {
    e.preventDefault();
    const form = $(this).closest('form');
    
    Swal.fire({
        title: 'Konfirmasi',
        text: 'Permintaan penghapusan akan dikirim untuk ditinjau. Lanjutkan?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Ya, Kirim',
        cancelButtonText: 'Batal',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: form.attr('action'),
                type: 'POST',
                data: form.serialize(),
                success: function(response) {
                    Swal.fire({
                        icon: 'info',
                        title: 'Permintaan Terkirim',
                        text: 'Permintaan penghapusan telah dikirim untuk ditinjau',
                        timer: 2000,
                        showConfirmButton: false
                    });
                },
                error: function(xhr) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Gagal mengirim permintaan. Silakan coba lagi.'
                    });
                }
            });
        }
    });
});

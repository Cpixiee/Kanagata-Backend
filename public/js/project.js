// Initialize DataTable
$(document).ready(function() {
    // Setup AJAX CSRF token
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    // Initialize edit modal
    const editModalElement = document.getElementById('edit-project-modal');
    const editModal = new Modal(editModalElement, {
        placement: 'center',
        backdrop: 'static',
        closable: true
    });

    // Initialize DataTable with custom controls
    var table = $('#project-details table').DataTable({
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
                targets: -1,
                orderable: false
            }
        ]
    });

    // Connect custom search input
    $('#table-search').on('keyup', function() {
        table.search(this.value).draw();
    });

    // Connect custom length select
    $('#table-length').on('change', function() {
        table.page.len(this.value).draw();
    });

    // Update custom length select when DataTable length changes
    table.on('length.dt', function(e, settings, len) {
        $('#table-length').val(len);
    });

    // Menambahkan event listener untuk memastikan tabel diperbarui saat ada perubahan
    table.on('draw', function() {
        // Memperbarui nomor urut
        table.column(0, {search:'applied', order:'applied'}).nodes().each(function (cell, i) {
            cell.innerHTML = i + 1;
        });
    });

    // Handle form submission for add project
    $('#add-project-form').on('submit', function(e) {
        e.preventDefault();
        handleFormSubmission($(this), false);
    });

    // Handle form submission for edit project
    $('#edit-project-form').on('submit', function(e) {
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

        $.ajax({
            url: $form.attr('action'),
            method: 'POST',
            data: $form.serialize(),
            success: function(response) {
                Swal.fire({
                    title: 'Berhasil!',
                    text: isEdit ? 'Data project berhasil diperbarui' : 'Data project berhasil disimpan',
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

    // Handle add project button click
    $('[data-modal-target="add-project-modal"]').click(function(e) {
        e.preventDefault();
        $('#add-project-form')[0].reset();
        $('#add-project-modal').modal('show');
    });

    // Handle edit project button click using event delegation
    $(document).on('click', '.edit-project', function(e) {
        e.preventDefault();
        const projectId = $(this).data('project-id');
        
        // Show loading state
        Swal.fire({
            title: 'Loading...',
            text: 'Mengambil data project',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        // Fetch project data for edit
        $.ajax({
            url: `/projects/${projectId}/edit`,
            method: 'GET',
            success: function(data) {
                Swal.close();
                
                // Populate edit form with project data
                const $form = $('#edit-project-form');
                for (const key in data) {
                    const element = $form.find(`#edit-${key}`);
                    if (element.length) {
                        element.val(data[key]);
                    }
                }
                
                // Update form action and method
                $form.attr('action', `/projects/${projectId}`);
                $form.find('input[name="_method"]').val('PUT');
                
                // Show edit modal using Flowbite
                if (editModalElement) {
                    editModal.show();
                }
            },
            error: function(xhr) {
                Swal.fire({
                    title: 'Error!',
                    text: 'Gagal mengambil data project',
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
            }
        });
    });

    // Reset forms when modals are hidden
    $('#add-project-modal, #edit-project-modal').on('hidden.bs.modal', function () {
        const $form = $(this).find('form');
        $form[0].reset();
        if ($(this).attr('id') === 'edit-project-modal') {
            $form.find('input[name="_method"]').val('PUT');
        }
    });

    // Handle delete button
    $(document).on('click', 'button[type="submit"]', function(e) {
        const form = $(this).closest('form');
        
        // Jika bukan form delete, biarkan form disubmit normal
        if (!form.find('input[name="_method"][value="DELETE"]').length) {
            return true;
        }

        e.preventDefault();
        
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
                // Show loading state
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
});

// Function to show success message
function showSuccessMessage(message) {
    Swal.fire({
        title: 'Berhasil!',
        text: message,
        icon: 'success',
        timer: 1500,
        showConfirmButton: false
    });
}

// Function to show error message
function showErrorMessage(message) {
    Swal.fire({
        title: 'Error!',
        text: message,
        icon: 'error',
        confirmButtonText: 'OK'
    });
}


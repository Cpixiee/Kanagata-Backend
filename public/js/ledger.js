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
    $(document).on('click', '.edit-ledger', function(e) {
        e.preventDefault();
        const ledgerId = $(this).data('ledger-id');
        
        Swal.fire({
            title: 'Memuat...',
            text: 'Mengambil data ledger',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        $.ajax({
            url: `/ledger/${ledgerId}/edit`,
            method: 'GET',
            success: function(data) {
                Swal.close();
                
                // Isi form dengan data
                $('#edit-ledger-form').attr('action', `/ledger/${ledgerId}`);
                $('#edit-category').val(data.category);
                $('#edit-budget_id').val(data.budget_id);
                $('#edit-sub_budget').val(data.sub_budget);
                $('#edit-recipient').val(data.recipient);
                $('#edit-date').val(data.date);
                $('#edit-month').val(data.month);
                $('#edit-status').val(data.status);
                $('#edit-debit').val(data.debit);
                $('#edit-credit').val(data.credit);
                
                // Tampilkan modal
                const editModal = document.getElementById('edit-ledger-modal');
                const modal = new Modal(editModal);
                modal.show();
            },
            error: function(xhr) {
                Swal.fire({
                    title: 'Error!',
                    text: 'Gagal mengambil data ledger',
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
            }
        });
    });

    // Handler untuk konfirmasi penghapusan
    $('button.delete-ledger').on('click', function(e) {
        e.preventDefault();
        const form = $(this).closest('form');
        
        Swal.fire({
            title: 'Apakah Anda yakin?',
            text: "Data ledger akan dihapus secara permanen!",
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
    $('#add-ledger-form, #edit-ledger-form').on('submit', function(e) {
        e.preventDefault();
        const $form = $(this);
        const isEdit = $form.attr('id') === 'edit-ledger-form';
        
        Swal.fire({
            title: 'Menyimpan...',
            text: 'Mohon tunggu',
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
                    text: isEdit ? 'Data ledger berhasil diperbarui' : 'Data ledger berhasil dibuat',
                    icon: 'success',
                    timer: 1500,
                    showConfirmButton: false
                }).then(() => {
                    window.location.reload();
                });
            },
            error: function(xhr) {
                let errorMessage = 'Terjadi kesalahan saat menyimpan data ledger.';
                if (xhr.responseJSON?.errors) {
                    errorMessage = Object.values(xhr.responseJSON.errors).flat().join('\n');
                } else if (xhr.responseJSON?.message) {
                    errorMessage = xhr.responseJSON.message;
                }
                
                // Tambahkan informasi status error
                if (xhr.status === 419) {
                    errorMessage = 'Sesi telah kedaluwarsa. Silakan muat ulang halaman.';
                }
                
                console.error('Error response:', xhr.responseJSON);
                
                Swal.fire({
                    title: 'Error!',
                    text: errorMessage,
                    icon: 'error',
                    confirmButtonText: 'OK'
                }).then((result) => {
                    if (xhr.status === 419) {
                        window.location.reload();
                    }
                });
            }
        });
    });

    // Function to update budget options based on category
    function updateBudgetOptions(categorySelect, budgetSelect) {
        const category = $(categorySelect).val();
        
        if (!category) return;

        $.ajax({
            url: '/ledger/budget-options',
            method: 'GET',
            data: { category: category },
            success: function(options) {
                const $budgetSelect = $(budgetSelect);
                $budgetSelect.empty();
                $budgetSelect.append('<option value="" selected disabled>Pilih Budget</option>');
                
                options.forEach(function(option) {
                    $budgetSelect.append(`<option value="${option.id}">${option.coa}</option>`);
                });
            },
            error: function(xhr) {
                showErrorMessage('Gagal memuat opsi budget');
            }
        });
    }

    // Handle category change in add form
    $('#category').on('change', function() {
        updateBudgetOptions(this, '#budget_id');
    });

    // Handle category change in edit form
    $('#edit-category').on('change', function() {
        updateBudgetOptions(this, '#edit-budget_id');
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
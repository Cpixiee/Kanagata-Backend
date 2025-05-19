$(document).ready(function() {
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
                $('#edit-todo').val(data.todo);
                $('#edit-ar_ap').val(data.ar_ap);
                
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
    $('#add-project-form, #edit-project-form').on('submit', function(e) {
        e.preventDefault();
        const $form = $(this);
        const isEdit = $form.attr('id') === 'edit-project-form';
        
        Swal.fire({
            title: 'Menyimpan...',
            text: 'Mohon tunggu',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        const formData = new FormData(this);
        const jsonData = {};
        formData.forEach((value, key) => {
            jsonData[key] = value;
        });

        // Hitung nilai turunan
        const prefix = isEdit ? 'edit-' : '';
        const quantity1 = parseFloat($(`#${prefix}quantity_1`).val()) || 0;
        const rate1 = parseFloat($(`#${prefix}rate_1`).val()) || 0;
        const quantity2 = parseFloat($(`#${prefix}quantity_2`).val()) || 0;
        const rate2 = parseFloat($(`#${prefix}rate_2`).val()) || 0;

        jsonData.gt_rev = quantity1 * rate1;
        jsonData.gt_cost = quantity2 * rate2;
        jsonData.gt_margin = jsonData.gt_rev - jsonData.gt_cost;

        $.ajax({
            url: isEdit ? $form.attr('action') : '/projects',
            method: 'POST',
            data: jsonData,
            success: function(response) {
                Swal.fire({
                    title: 'Berhasil!',
                    text: isEdit ? 'Proyek berhasil diperbarui' : 'Proyek berhasil dibuat',
                    icon: 'success',
                    timer: 1500,
                    showConfirmButton: false
                }).then(() => {
                    window.location.reload();
                });
            },
            error: function(xhr) {
                let errorMessage = 'Terjadi kesalahan saat menyimpan proyek.';
                if (xhr.responseJSON?.errors) {
                    errorMessage = Object.values(xhr.responseJSON.errors).flat().join('\n');
                }
                
                Swal.fire({
                    title: 'Error!',
                    text: errorMessage,
                    icon: 'error',
                    confirmButtonText: 'OK'
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

        $(`#${prefix}gt_rev`).val(gtRev.toFixed(2));
        $(`#${prefix}gt_cost`).val(gtCost.toFixed(2));
        $(`#${prefix}gt_margin`).val(gtMargin.toFixed(2));
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

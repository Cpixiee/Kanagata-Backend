$(document).ready(function() {
    // Setup CSRF token untuk semua request AJAX
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    // Handle filter type change
    $('#filter_type').on('change', function() {
        const filterType = $(this).val();
        
        // Hide all filter containers
        $('#filter_date_container, #filter_month_container, #filter_year_container').hide();
        
        // Show relevant filter containers based on selection
        switch(filterType) {
            case 'day':
                $('#filter_date_container').show();
                $('#filter_year_container').show(); // Year is needed for day filter
                break;
            case 'month':
                $('#filter_month_container').show();
                $('#filter_year_container').show();
                break;
            case 'year':
                $('#filter_year_container').show();
                break;
            case 'all':
            default:
                // No additional filters needed
                break;
        }
    });

    // Initialize filter visibility on page load
    $('#filter_type').trigger('change');

    // Function to update summary based on current filters
    function updateSummary() {
        const formData = $('#filter-form').serialize();
        
        $.ajax({
            url: '/ledger/summary',
            method: 'GET',
            data: formData,
            success: function(data) {
                // Update summary values with animation
                $('#sum-debit').fadeOut(200, function() {
                    $(this).text(formatNumber(data.sum_debit)).fadeIn(200);
                });
                $('#sum-credit').fadeOut(200, function() {
                    $(this).text(formatNumber(data.sum_credit)).fadeIn(200);
                });
                $('#saldo').fadeOut(200, function() {
                    $(this).text(formatNumber(data.saldo)).fadeIn(200);
                });
            },
            error: function(xhr) {
                console.error('Error updating summary:', xhr);
            }
        });
    }

    // Function to format numbers with thousand separators
    function formatNumber(num) {
        return new Intl.NumberFormat('id-ID').format(num);
    }

    // Update summary when filter changes
    $('#filter_type, #filter_date, #filter_month, #filter_year').on('change', function() {
        updateSummary();
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
                $('#edit-budget').val(data.budget);
                $('#edit-sub_budget').val(data.sub_budget);
                $('#edit-recipient').val(data.recipient);
                
                // Format date properly for input[type="date"]
                let formattedDate = data.date;
                if (data.date && data.date.includes('T')) {
                    formattedDate = data.date.split('T')[0];
                }
                $('#edit-date').val(formattedDate);
                
                $('#edit-month').val(data.month);
                $('#edit-status').val(data.status);
                $('#edit-debit').val(data.debit);
                $('#edit-credit').val(data.credit);

                // Handle different behavior for project vs operation categories
                const isProjectCategory = ['COST PROJECT', 'REVENUE PROJECT'].includes(data.category);
                
                if (isProjectCategory) {
                    // For project categories: show readonly inputs, hide dropdowns
                    $('#edit-category').hide().prop('required', false);
                    $('#edit-category-readonly').show().val(data.category);
                    $('#edit-budget').hide().prop('required', false);
                    $('#edit-budget-readonly').show().val(data.budget);
                    $('#edit-sub_budget').hide().prop('required', false);
                    $('#edit-sub_budget-readonly').show().val(data.sub_budget);
                    $('#edit-recipient').hide().prop('required', false);
                    $('#edit-recipient-readonly').show().val(data.recipient);
                    $('#edit-status').hide().prop('required', false);
                    $('#edit-status-readonly').show().val(data.status);
                    
                    // Handle amount fields based on category
                    if (data.category === 'COST PROJECT') {
                        // COST PROJECT: credit editable, debit readonly
                        $('#edit-credit').prop('readonly', false).removeClass('bg-gray-100 cursor-not-allowed');
                        $('#edit-debit').prop('readonly', true).addClass('bg-gray-100 cursor-not-allowed');
                    } else if (data.category === 'REVENUE PROJECT') {
                        // REVENUE PROJECT: debit editable, credit readonly
                        $('#edit-debit').prop('readonly', false).removeClass('bg-gray-100 cursor-not-allowed');
                        $('#edit-credit').prop('readonly', true).addClass('bg-gray-100 cursor-not-allowed');
                    }
                    
                    // Add hidden inputs for readonly values
                    $('#edit-ledger-form').find('input[name="category_hidden"]').remove();
                    $('#edit-ledger-form').find('input[name="budget_hidden"]').remove();
                    $('#edit-ledger-form').find('input[name="sub_budget_hidden"]').remove();
                    $('#edit-ledger-form').find('input[name="recipient_hidden"]').remove();
                    $('#edit-ledger-form').find('input[name="status_hidden"]').remove();
                    
                    $('#edit-ledger-form').append(`<input type="hidden" name="category" value="${data.category}">`);
                    $('#edit-ledger-form').append(`<input type="hidden" name="budget" value="${data.budget}">`);
                    $('#edit-ledger-form').append(`<input type="hidden" name="sub_budget" value="${data.sub_budget}">`);
                    $('#edit-ledger-form').append(`<input type="hidden" name="recipient" value="${data.recipient}">`);
                    $('#edit-ledger-form').append(`<input type="hidden" name="status" value="${data.status}">`);
                } else {
                    // For operation categories: show dropdowns, hide readonly inputs
                    $('#edit-category').show().prop('required', true).val(data.category);
                    $('#edit-category-readonly').hide();
                    $('#edit-budget').show().prop('required', true);
                    $('#edit-budget-readonly').hide();
                    $('#edit-sub_budget').show().prop('required', true).val(data.sub_budget);
                    $('#edit-sub_budget-readonly').hide();
                    $('#edit-recipient').show().prop('required', true).val(data.recipient);
                    $('#edit-recipient-readonly').hide();
                    $('#edit-status').show().prop('required', true).val(data.status);
                    $('#edit-status-readonly').hide();
                    
                    // For operation categories: credit editable, debit readonly
                    $('#edit-credit').prop('readonly', false).removeClass('bg-gray-100 cursor-not-allowed');
                    $('#edit-debit').prop('readonly', true).addClass('bg-gray-100 cursor-not-allowed');
                    
                    // Remove hidden inputs for operation categories
                    $('#edit-ledger-form').find('input[type="hidden"][name="category"]').remove();
                    $('#edit-ledger-form').find('input[type="hidden"][name="budget"]').remove();
                    $('#edit-ledger-form').find('input[type="hidden"][name="sub_budget"]').remove();
                    $('#edit-ledger-form').find('input[type="hidden"][name="recipient"]').remove();
                    $('#edit-ledger-form').find('input[type="hidden"][name="status"]').remove();
                    
                    // Update budget options for operation categories
                    updateBudgetOptions($('#edit-category')[0], '#edit-budget');
                    setTimeout(() => {
                        $('#edit-budget').val(data.budget);
                    }, 500);
                }
                
                // Tampilkan modal
                const editModal = document.getElementById('edit-ledger-modal');
                editModal.classList.remove('hidden');
                editModal.setAttribute('aria-hidden', 'false');
                editModal.style.display = 'flex';
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
        
        // Cek role user dari data yang disimpan di body
        const isAdmin = $('body').data('role') === 'admin';

        if (isAdmin) {
            // Konfirmasi hapus untuk admin
            Swal.fire({
                title: 'Konfirmasi Hapus',
                text: 'Apakah Anda yakin ingin menghapus data ini?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#EF4444',
                cancelButtonColor: '#6B7280',
                confirmButtonText: 'Ya, Hapus',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: form.attr('action'),
                        type: 'POST',
                        data: form.serialize(),
                        success: function(response) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil',
                                text: 'Data ledger berhasil dihapus',
                                confirmButtonText: 'OK',
                                confirmButtonColor: '#3B82F6'
                            }).then(() => {
                                window.location.reload();
                            });
                        },
                        error: function(xhr) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'Gagal menghapus data. Silakan coba lagi.'
                            });
                        }
                    });
                }
            });
        } else {
            // Konfirmasi hapus untuk user biasa
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
                                html: `
                                    <div class="text-center">
                                        <div class="mb-4">
                                            <i class="fas fa-info-circle text-info" style="font-size: 48px; color: #60A5FA;"></i>
                                        </div>
                                        <h2 class="text-xl font-semibold mb-4" style="color: #374151;">Permintaan Terkirim</h2>
                                        <p style="color: #6B7280;">Permintaan penghapusan telah dikirim untuk ditinjau</p>
                                    </div>
                                `,
                                showConfirmButton: true,
                                confirmButtonText: 'OK',
                                confirmButtonColor: '#3B82F6',
                                background: '#FFFFFF'
                            }).then(() => {
                                window.location.reload();
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
        }
    });

    // Form submission handlers
    $('#add-ledger-form, #edit-ledger-form').on('submit', function(e) {
        e.preventDefault();
        const form = $(this);
        const isEdit = form.attr('id') === 'edit-ledger-form';
        
        // Validasi form sebelum submit
        let isValid = true;
        const requiredFields = ['category', 'budget', 'sub_budget', 'recipient', 'date', 'month', 'status'];
        
        requiredFields.forEach(field => {
            const fieldElement = isEdit ? $(`#edit-${field}`) : $(`#${field}`);
            const readonlyElement = isEdit ? $(`#edit-${field}-readonly`) : null;
            
            // Untuk edit, cek apakah field readonly atau tidak
            if (isEdit && readonlyElement && readonlyElement.is(':visible')) {
                // Field readonly, cek nilai dari readonly element
                if (!readonlyElement.val() || readonlyElement.val() === '') {
                    isValid = false;
                    readonlyElement.addClass('border-red-500');
                } else {
                    readonlyElement.removeClass('border-red-500');
                }
            } else if (isEdit) {
                // Field normal untuk edit, cek apakah ada hidden input atau field biasa
                const hiddenInput = form.find(`input[type="hidden"][name="${field}"]`);
                
                if (hiddenInput.length > 0) {
                    // Ada hidden input, cek nilainya
                    if (!hiddenInput.val() || hiddenInput.val() === '') {
                        isValid = false;
                        // Highlight field yang terlihat jika ada
                        if (fieldElement.length && fieldElement.is(':visible')) {
                            fieldElement.addClass('border-red-500');
                        }
                    } else {
                        if (fieldElement.length && fieldElement.is(':visible')) {
                            fieldElement.removeClass('border-red-500');
                        }
                    }
                } else {
                    // Tidak ada hidden input, cek field biasa
                    if (!fieldElement.val() || fieldElement.val() === '') {
                        isValid = false;
                        fieldElement.addClass('border-red-500');
                    } else {
                        fieldElement.removeClass('border-red-500');
                    }
                }
            } else {
                // Form add, cek field normal
                if (!fieldElement.val() || fieldElement.val() === '') {
                    isValid = false;
                    fieldElement.addClass('border-red-500');
                } else {
                    fieldElement.removeClass('border-red-500');
                }
            }
        });

        // Validasi amount berdasarkan kategori
        const creditField = isEdit ? $('#edit-credit') : $('#credit');
        const debitField = isEdit ? $('#edit-debit') : $('input[name="debit"]');
        
        if (isEdit) {
            // Untuk edit, cek kategori dari hidden input atau field yang terlihat
            const categoryValue = form.find('input[type="hidden"][name="category"]').val() || 
                                 form.find('#edit-category').val();
            
            if (categoryValue === 'REVENUE PROJECT') {
                // REVENUE PROJECT: debit harus ada nilai, credit boleh 0
                if (!debitField.val() || debitField.val() <= 0) {
                    isValid = false;
                    debitField.addClass('border-red-500');
                } else {
                    debitField.removeClass('border-red-500');
                }
            } else {
                // COST PROJECT, COST OPERATION, KAS MARGIN: credit harus ada nilai, debit boleh 0
                if (!creditField.val() || creditField.val() <= 0) {
                    isValid = false;
                    creditField.addClass('border-red-500');
                } else {
                    creditField.removeClass('border-red-500');
                }
            }
        } else {
            // Form add: validasi berdasarkan kategori
            const categoryValue = $('#category').val();
            
            if (categoryValue === 'REVENUE PROJECT') {
                // REVENUE PROJECT: amount akan masuk ke debit
                if (!creditField.val() || creditField.val() <= 0) {
                    isValid = false;
                    creditField.addClass('border-red-500');
                } else {
                    creditField.removeClass('border-red-500');
                }
            } else {
                // COST PROJECT, COST OPERATION, KAS MARGIN: amount akan masuk ke credit
                if (!creditField.val() || creditField.val() <= 0) {
                    isValid = false;
                    creditField.addClass('border-red-500');
                } else {
                    creditField.removeClass('border-red-500');
                }
            }
        }

        if (!isValid) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Mohon lengkapi semua field yang diperlukan'
            });
            return;
        }

        const formData = new FormData(this);

        if (isEdit) {
            formData.append('_method', 'PUT');
        }

        // Cek role user dari data yang disimpan di body
        const isAdmin = $('body').data('role') === 'admin';

        $.ajax({
            url: form.attr('action'),
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.message || response.success) {
                    if (isAdmin) {
                        // Notifikasi untuk admin
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: isEdit ? 'Data ledger berhasil diperbarui' : 'Data ledger berhasil ditambahkan',
                            confirmButtonText: 'OK',
                            confirmButtonColor: '#3B82F6'
                        }).then(() => {
                            const modalId = isEdit ? 'edit-ledger-modal' : 'add-ledger-modal';
                            const modal = document.getElementById(modalId);
                            if (modal) {
                                modal.classList.add('hidden');
                                modal.setAttribute('aria-hidden', 'true');
                                modal.style.display = 'none';
                            }
                            window.location.reload();
                        });
                    } else {
                        // Notifikasi untuk user biasa
                        Swal.fire({
                            html: `
                                <div class="text-center">
                                    <div class="mb-4">
                                        <i class="fas fa-info-circle" style="font-size: 48px; color: #60A5FA;"></i>
                                    </div>
                                    <h2 class="text-xl font-semibold mb-4" style="color: #374151;">Permintaan Terkirim</h2>
                                    <p style="color: #6B7280;">Permintaan ${isEdit ? 'perubahan' : 'penambahan'} data telah dikirim untuk ditinjau.</p>
                                </div>
                            `,
                            showConfirmButton: true,
                            confirmButtonText: 'OK',
                            confirmButtonColor: '#3B82F6',
                            background: '#FFFFFF'
                        }).then(() => {
                            const modalId = isEdit ? 'edit-ledger-modal' : 'add-ledger-modal';
                            const modal = document.getElementById(modalId);
                            if (modal) {
                                modal.classList.add('hidden');
                                modal.setAttribute('aria-hidden', 'true');
                                modal.style.display = 'none';
                            }
                            window.location.reload();
                        });
                    }
                }
            },
            error: function(xhr) {
                let errorMessage = 'Terjadi kesalahan. Silakan coba lagi.';
                if (xhr.responseJSON && xhr.responseJSON.errors) {
                    const errors = Object.values(xhr.responseJSON.errors).flat();
                    errorMessage = errors.join('\n');
                } else if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                }
                
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: errorMessage
                });
            }
        });
    });

    // Handle category change in add form
    $('#category').on('change', function() {
        updateBudgetOptions(this, '#budget');
        
        const category = $(this).val();
        const $amountLabel = $('#amount-label');
        const $creditInput = $('#credit');
        const $debitInput = $('input[name="debit"]');
        
        // Update label and values based on category
        if (category === 'REVENUE PROJECT') {
            $amountLabel.text('Amount (Debit)');
            $debitInput.val($creditInput.val() || 0);
            $creditInput.val(0);
        } else {
            $amountLabel.text('Amount (Credit)');
            $creditInput.val($debitInput.val() || 0);
            $debitInput.val(0);
        }
    });

    // Handle amount change
    $('#credit').on('change', function() {
        const category = $('#category').val();
        const amount = $(this).val() || 0;
        const $debitInput = $('input[name="debit"]');
        
        if (category === 'REVENUE PROJECT') {
            $debitInput.val(amount);
            $(this).val(0);
        } else {
            $debitInput.val(0);
        }
    });

    // Handle category change in edit form
    $('#edit-category').on('change', function() {
        updateBudgetOptions(this, '#edit-budget');
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
                    if (category === 'COST OPERATION' || category === 'KAS MARGIN') {
                        // For COST OPERATION and KAS MARGIN, use COA as both value and text
                        $budgetSelect.append(`<option value="${option.coa}">${option.coa}</option>`);
                    } else {
                        // For project categories, use project COA as value
                        $budgetSelect.append(`<option value="${option.coa}">${option.coa}</option>`);
                    }
                });
            },
            error: function(xhr) {
                showErrorMessage('Gagal memuat opsi budget');
            }
        });
    }

    // Handle modal close buttons
    $(document).on('click', '[data-modal-hide="edit-ledger-modal"]', function(e) {
        e.preventDefault();
        closeEditModal();
    });

    $(document).on('click', '[data-modal-hide="add-ledger-modal"]', function(e) {
        e.preventDefault();
        closeAddModal();
    });

    // Function to close edit modal properly
    function closeEditModal() {
        const editModal = document.getElementById('edit-ledger-modal');
        if (editModal) {
            // Hide modal
            editModal.classList.add('hidden');
            editModal.setAttribute('aria-hidden', 'true');
            editModal.style.display = 'none';
            
            // Remove all possible backdrop elements
            document.querySelectorAll('[modal-backdrop], .modal-backdrop, .fixed.inset-0.bg-gray-900').forEach(backdrop => {
                backdrop.remove();
            });
            
            // Remove overflow hidden from body and restore scrolling
            document.body.classList.remove('overflow-hidden');
            document.body.style.overflow = '';
            document.documentElement.style.overflow = '';
            
            // Remove any modal-related classes from html/body
            document.documentElement.classList.remove('overflow-hidden');
            
            // Force remove any remaining modal backdrop styles
            const style = document.createElement('style');
            style.textContent = `
                body { overflow: auto !important; }
                html { overflow: auto !important; }
            `;
            document.head.appendChild(style);
            setTimeout(() => {
                document.head.removeChild(style);
            }, 100);
            
            // Reset form
            $('#edit-ledger-form')[0].reset();
            
            // Remove any hidden inputs that were added
            $('#edit-ledger-form').find('input[type="hidden"][name="category"]').remove();
            $('#edit-ledger-form').find('input[type="hidden"][name="budget"]').remove();
            $('#edit-ledger-form').find('input[type="hidden"][name="sub_budget"]').remove();
            $('#edit-ledger-form').find('input[type="hidden"][name="recipient"]').remove();
            $('#edit-ledger-form').find('input[type="hidden"][name="status"]').remove();
            
            // Reset field visibility and states
            $('#edit-category').show().prop('required', true).removeClass('bg-gray-100 cursor-not-allowed');
            $('#edit-category-readonly').hide();
            $('#edit-budget').show().prop('required', true).removeClass('bg-gray-100 cursor-not-allowed');
            $('#edit-budget-readonly').hide();
            $('#edit-sub_budget').show().prop('required', true).removeClass('bg-gray-100 cursor-not-allowed');
            $('#edit-sub_budget-readonly').hide();
            $('#edit-recipient').show().prop('required', true).removeClass('bg-gray-100 cursor-not-allowed');
            $('#edit-recipient-readonly').hide();
            $('#edit-status').show().prop('required', true).removeClass('bg-gray-100 cursor-not-allowed');
            $('#edit-status-readonly').hide();
            $('#edit-credit').prop('readonly', false).removeClass('bg-gray-100 cursor-not-allowed');
        }
    }

    // Function to close add modal properly
    function closeAddModal() {
        const addModal = document.getElementById('add-ledger-modal');
        if (addModal) {
            // Hide modal
            addModal.classList.add('hidden');
            addModal.setAttribute('aria-hidden', 'true');
            addModal.style.display = 'none';
            
            // Remove all possible backdrop elements
            document.querySelectorAll('[modal-backdrop], .modal-backdrop, .fixed.inset-0.bg-gray-900').forEach(backdrop => {
                backdrop.remove();
            });
            
            // Remove overflow hidden from body and restore scrolling
            document.body.classList.remove('overflow-hidden');
            document.body.style.overflow = '';
            document.documentElement.style.overflow = '';
            
            // Remove any modal-related classes from html/body
            document.documentElement.classList.remove('overflow-hidden');
            
            // Force remove any remaining modal backdrop styles
            const style = document.createElement('style');
            style.textContent = `
                body { overflow: auto !important; }
                html { overflow: auto !important; }
            `;
            document.head.appendChild(style);
            setTimeout(() => {
                document.head.removeChild(style);
            }, 100);
            
            // Reset form
            $('#add-ledger-form')[0].reset();
        }
    }

    // Handle ESC key to close modals
    $(document).on('keydown', function(e) {
        if (e.key === 'Escape') {
            // Close edit modal
            const editModal = document.getElementById('edit-ledger-modal');
            if (editModal && !editModal.classList.contains('hidden')) {
                closeEditModal();
            }
            // Close add modal
            const addModal = document.getElementById('add-ledger-modal');
            if (addModal && !addModal.classList.contains('hidden')) {
                closeAddModal();
            }
        }
    });

    // Handle backdrop click to close modals
    $(document).on('click', '#edit-ledger-modal, #add-ledger-modal', function(e) {
        if (e.target === this) {
            if (this.id === 'edit-ledger-modal') {
                closeEditModal();
            } else if (this.id === 'add-ledger-modal') {
                closeAddModal();
            }
        }
    });

    // Handle mark as paid button
    $(document).on('click', '.mark-as-paid', function() {
        const ledgerId = $(this).data('ledger-id');
        const button = $(this);

        // Konfirmasi sebelum mengubah status
        Swal.fire({
            title: 'Konfirmasi',
            text: 'Apakah Anda yakin ingin mengubah status menjadi PAID?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, ubah!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                // Disable button sementara proses
                button.prop('disabled', true);
                
                // Kirim request ke server
                $.ajax({
                    url: `/ledger/${ledgerId}/mark-as-paid`,
                    type: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: 'Status berhasil diubah menjadi PAID',
                                confirmButtonText: 'OK',
                                confirmButtonColor: '#3B82F6'
                            }).then(() => {
                                window.location.reload();
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal!',
                                text: response.message || 'Terjadi kesalahan saat mengubah status.',
                                confirmButtonText: 'OK'
                            });
                            button.prop('disabled', false);
                        }
                    },
                    error: function(xhr) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: 'Terjadi kesalahan saat mengubah status.',
                            confirmButtonText: 'OK'
                        });
                        button.prop('disabled', false);
                    }
                });
            }
        });
    });
});

function showSuccessMessage(message) {
    Swal.fire({
        icon: 'success',
        title: 'Berhasil!',
        text: message,
        confirmButtonText: 'OK',
        confirmButtonColor: '#3B82F6'
    });
}

function showErrorMessage(message) {
    Swal.fire({
        icon: 'error',
        title: 'Error!',
        text: message,
        confirmButtonText: 'OK'
    });
}
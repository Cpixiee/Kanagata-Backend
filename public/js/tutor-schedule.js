$(document).ready(function() {
    // Setup CSRF token untuk semua AJAX requests
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    let calendar = null;
    let selectedTutorId = null;
    let selectedLogsheetId = null;
    let selectedDate = null;
    let remainingSessions = 0;

    // Inisialisasi modal-modal menggunakan Flowbite
    const modals = {
        scheduleModal: new Modal(document.getElementById('schedule-modal'), {
            placement: 'center',
            backdrop: 'dynamic',
            closable: true,
        }),
        addScheduleModal: new Modal(document.getElementById('add-schedule-modal'), {
            placement: 'center',
            backdrop: 'dynamic',
            closable: true,
        }),
        editScheduleModal: new Modal(document.getElementById('edit-schedule-modal'), {
            placement: 'center',
            backdrop: 'dynamic',
            closable: true,
        })
    };

    // Event handler untuk tombol close pada modal Schedule Calendar
    const closeButtons = document.querySelectorAll('[data-modal-hide="schedule-modal"]');
    closeButtons.forEach(button => {
        button.addEventListener('click', () => {
            modals.scheduleModal.hide();
            // Reset state saat modal ditutup
            selectedLogsheetId = null;
            if (calendar) {
                calendar.destroy();
                calendar = null;
            }
            // Reset dropdown logsheet
            $('#logsheet_id').val('');
            // Hapus konten kalender
            $('#schedule-calendar').empty();
            // Hapus info sesi tersisa dan legend jika ada
            $('.remaining-sessions-info').remove();
            $('.calendar-legend').remove();
        });
    });

    // Reset form saat modal ditutup
    document.getElementById('schedule-modal').addEventListener('hidden.bs.modal', function () {
        selectedLogsheetId = null;
        if (calendar) {
            calendar.destroy();
            calendar = null;
        }
        $('#logsheet_id').val('');
        $('#schedule-calendar').empty();
        $('.remaining-sessions-info').remove();
        $('.calendar-legend').remove();
    });

    document.getElementById('add-schedule-modal').addEventListener('hidden.bs.modal', function () {
        $('#add-schedule-form').trigger('reset');
    });

    document.getElementById('edit-schedule-modal').addEventListener('hidden.bs.modal', function () {
        $('#edit-schedule-form').trigger('reset');
    });

    function initializeCalendar() {
        if (calendar) {
            calendar.destroy();
        }

        const calendarEl = document.getElementById('schedule-calendar');
        calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            selectable: true,
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth'
            },
            events: function(info, successCallback, failureCallback) {
                if (!selectedTutorId || !selectedLogsheetId) {
                    successCallback([]);
                    return;
                }
                
                $.ajax({
                    url: `/tutor/${selectedTutorId}/schedules`,
                    method: 'GET',
                    data: { logsheet_id: selectedLogsheetId },
                    success: function(response) {
                        const events = response.schedules.map(schedule => ({
                            id: schedule.id,
                            title: `Sesi ${schedule.session_number}${schedule.notes ? '\n' + schedule.notes : ''}`,
                            start: schedule.schedule_date,
                            className: `status-${schedule.status}`,
                            extendedProps: {
                                status: schedule.status,
                                notes: schedule.notes,
                                session_number: schedule.session_number
                            }
                        }));
                        successCallback(events);
                    },
                    error: function() {
                        failureCallback();
                    }
                });
            },
            eventDidMount: function(info) {
                // Tambahkan tooltip untuk menampilkan notes
                if (info.event.extendedProps.notes) {
                    $(info.el).tooltip({
                        title: info.event.extendedProps.notes,
                        placement: 'top',
                        trigger: 'hover',
                        container: 'body'
                    });
                }
            },
            dateClick: function(info) {
                if (!selectedLogsheetId) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Pilih Logsheet',
                        text: 'Silakan pilih logsheet terlebih dahulu'
                    });
                    return;
                }

                if (remainingSessions <= 0) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Tidak Ada Sesi Tersedia',
                        text: 'Semua sesi untuk logsheet ini sudah dijadwalkan'
                    });
                    return;
                }

                // Check if date is in the past
                const clickedDate = new Date(info.dateStr);
                const today = new Date();
                today.setHours(0, 0, 0, 0);

                if (clickedDate < today) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Tanggal Tidak Valid',
                        text: 'Tidak dapat membuat jadwal untuk tanggal yang sudah lewat'
                    });
                    return;
                }

                selectedDate = info.dateStr;
                showAddScheduleModal();
            },
            eventClick: function(info) {
                showEditScheduleModal(info.event);
            },
            dayCellDidMount: function(arg) {
                updateCellColor(arg.el, arg.date);
            }
        });

        calendar.render();
        updateDateColors();
    }

    function updateDateColors() {
        if (!selectedTutorId || !selectedLogsheetId) return;

        // Get all schedules for the tutor first
        $.ajax({
            url: `/tutor/${selectedTutorId}/schedules`,
            method: 'GET',
            success: function(scheduleResponse) {
                // Get available dates for the selected logsheet
                $.ajax({
                    url: `/tutor/${selectedTutorId}/available-dates`,
                    method: 'GET',
                    data: {
                        logsheet_id: selectedLogsheetId,
                        month: calendar.view.currentStart.toISOString().slice(0, 7)
                    },
                    success: function(response) {
                        remainingSessions = response.remaining_sessions;
                        
                        // Get all scheduled dates for this tutor (across all logsheets)
                        const allScheduledDates = scheduleResponse.schedules.map(schedule => 
                            new Date(schedule.start).toISOString().split('T')[0]
                        );
                        
                        // Update semua sel kalender
                        const cells = document.querySelectorAll('.fc-daygrid-day');
                        cells.forEach(cell => {
                            const date = cell.getAttribute('data-date');
                            if (!date) return;

                            const cellDate = new Date(date);
                            const today = new Date();
                            today.setHours(0, 0, 0, 0);

                            // Reset classes
                            cell.classList.remove('booked-date', 'available-date', 'past-date');

                            // Cek apakah tanggal ini memiliki jadwal (di logsheet manapun)
                            const hasAnySchedule = allScheduledDates.includes(date);

                            // Tanggal yang sudah lewat
                            if (cellDate < today) {
                                cell.classList.add('past-date');
                            } 
                            // Tanggal yang sudah ada jadwal (di logsheet manapun)
                            else if (hasAnySchedule) {
                                cell.classList.add('booked-date');
                            }
                            // Tanggal yang masih available (future date) dan masih ada sesi tersisa
                            else if (cellDate >= today && remainingSessions > 0) {
                                cell.classList.add('available-date');
                            }
                        });

                        // Update info sesi tersisa
                        const remainingSessionsInfo = document.createElement('div');
                        remainingSessionsInfo.className = 'text-sm text-gray-600 mt-2';
                        remainingSessionsInfo.textContent = `Sesi tersisa: ${remainingSessions} dari ${response.total_sessions}`;
                        
                        const calendarHeader = document.querySelector('.fc-header-toolbar');
                        const existingInfo = document.querySelector('.remaining-sessions-info');
                        if (existingInfo) {
                            existingInfo.remove();
                        }
                        remainingSessionsInfo.classList.add('remaining-sessions-info');
                        calendarHeader.appendChild(remainingSessionsInfo);

                        // Tambahkan keterangan warna
                        const legendContainer = document.createElement('div');
                        legendContainer.className = 'calendar-legend flex gap-4 justify-center mt-4';
                        legendContainer.innerHTML = `
                            <div class="flex items-center">
                                <span class="w-3 h-3 inline-block mr-1 bg-red-200"></span>
                                <span class="text-sm">Sudah ada jadwal</span>
                            </div>
                            <div class="flex items-center">
                                <span class="w-3 h-3 inline-block mr-1 bg-green-100"></span>
                                <span class="text-sm">Tersedia</span>
                            </div>
                            <div class="flex items-center">
                                <span class="w-3 h-3 inline-block mr-1 bg-gray-100"></span>
                                <span class="text-sm">Lewat</span>
                            </div>
                        `;

                        const existingLegend = document.querySelector('.calendar-legend');
                        if (existingLegend) {
                            existingLegend.remove();
                        }
                        document.getElementById('schedule-calendar').appendChild(legendContainer);
                    }
                });
            }
        });
    }

    function updateCellColor(cell, date) {
        const today = new Date();
        today.setHours(0, 0, 0, 0);
        
        if (date < today) {
            cell.classList.add('past-date');
        }
    }

    function showAddScheduleModal() {
        if (!selectedTutorId || !selectedLogsheetId) return;

        $.ajax({
            url: `/tutor/${selectedTutorId}/available-sessions`,
            method: 'GET',
            data: { logsheet_id: selectedLogsheetId },
            success: function(response) {
                const sessionSelect = $('#session_number');
                sessionSelect.empty();
                sessionSelect.append('<option value="">Pilih nomor sesi</option>');

                response.available_sessions.forEach(session => {
                    sessionSelect.append(`<option value="${session}">Sesi ${session}</option>`);
                });

                $('#schedule_date').val(selectedDate);
                modals.addScheduleModal.show();
            }
        });
    }

    function showEditScheduleModal(event) {
        const schedule = event.extendedProps;
        $('#edit_schedule_date').val(event.startStr);
        $('#edit_status').val(schedule.status);
        $('#edit_notes').val(schedule.notes);
        modals.editScheduleModal.show();
        $('#edit-schedule-form').data('scheduleId', event.id);
    }

    // Handler untuk pemilihan logsheet
    $('#logsheet_id').change(function() {
        selectedLogsheetId = $(this).val();
        if (selectedLogsheetId && calendar) {
            calendar.refetchEvents();
            updateDateColors();
        } else if (selectedLogsheetId) {
            initializeCalendar();
        }
    });

    // Handler untuk form tambah jadwal
    $('#add-schedule-form').submit(function(e) {
        e.preventDefault();

        // Validasi form sebelum submit
        const sessionNumber = $('#session_number').val();
        const scheduleDate = $('#schedule_date').val();

        if (!sessionNumber) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Silakan pilih nomor sesi'
            });
            return;
        }

        if (!scheduleDate) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Silakan pilih tanggal'
            });
            return;
        }

        // Tampilkan loading
        Swal.fire({
            title: 'Menyimpan...',
            text: 'Mohon tunggu sebentar',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        $.ajax({
            url: `/tutor/${selectedTutorId}/schedules`,
            method: 'POST',
            data: {
                logsheet_id: selectedLogsheetId,
                session_number: sessionNumber,
                schedule_date: scheduleDate,
                notes: $('#notes').val()
            },
            success: function(response) {
                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: response.message
                    });
                    modals.addScheduleModal.hide();
                    calendar.refetchEvents();
                    updateDateColors();
                }
            },
            error: function(xhr) {
                let errorMessage = 'Gagal menambah jadwal';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                } else if (xhr.responseJSON && xhr.responseJSON.errors) {
                    errorMessage = Object.values(xhr.responseJSON.errors).flat().join('\n');
                }
                
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: errorMessage
                });
            }
        });
    });

    // Handler untuk form edit jadwal
    $('#edit-schedule-form').submit(function(e) {
        e.preventDefault();
        const scheduleId = $(this).data('scheduleId');
        const formData = $(this).serialize();

        $.ajax({
            url: `/tutor/${selectedTutorId}/schedules/${scheduleId}`,
            method: 'PUT',
            data: formData,
            success: function(response) {
                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: response.message
                    });
                    modals.editScheduleModal.hide();
                    calendar.refetchEvents();
                    updateDateColors();
                }
            },
            error: function(xhr) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: xhr.responseJSON?.message || 'Gagal mengupdate jadwal'
                });
            }
        });
    });

    // Handler untuk hapus jadwal
    $('#delete-schedule').click(function() {
        const scheduleId = $('#edit-schedule-form').data('scheduleId');

        Swal.fire({
            title: 'Apakah Anda yakin?',
            text: "Jadwal yang dihapus tidak dapat dikembalikan!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `/tutor/${selectedTutorId}/schedules/${scheduleId}`,
                    method: 'DELETE',
                    success: function(response) {
                        if (response.success) {
                            Swal.fire(
                                'Terhapus!',
                                response.message,
                                'success'
                            );
                            modals.editScheduleModal.hide();
                            calendar.refetchEvents();
                            updateDateColors();
                        }
                    },
                    error: function() {
                        Swal.fire(
                            'Error!',
                            'Gagal menghapus jadwal',
                            'error'
                        );
                    }
                });
            }
        });
    });

    // Inisialisasi saat tombol schedule diklik
    $('.schedule-btn').click(function() {
        selectedTutorId = $(this).data('tutor-id');
        if (!selectedTutorId) {
            console.error('Tutor ID tidak ditemukan');
            return;
        }

        // Muat data logsheet untuk tutor yang dipilih
        $.ajax({
            url: `/tutor/${selectedTutorId}/logsheets`,
            method: 'GET',
            success: function(response) {
                const logsheetSelect = $('#logsheet_id');
                logsheetSelect.empty();
                logsheetSelect.append('<option value="">Pilih logsheet</option>');

                response.forEach(logsheet => {
                    logsheetSelect.append(`<option value="${logsheet.id}">${logsheet.activity} - ${logsheet.customer} (${logsheet.available_sessions.length} sesi tersisa)</option>`);
                });

                modals.scheduleModal.show();
                
                // Calendar akan diinisialisasi setelah logsheet dipilih
                selectedLogsheetId = null;
                if (calendar) {
                    calendar.destroy();
                    calendar = null;
                }
            },
            error: function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Gagal memuat data logsheet'
                });
            }
        });
    });
}); 
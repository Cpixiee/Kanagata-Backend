$(document).ready(function() {
    // Check URL parameters for auto-showing schedule
    const urlParams = new URLSearchParams(window.location.search);
    const showScheduleId = urlParams.get('show_schedule');
    if (showScheduleId) {
        // Find and click the schedule button for the specified tutor
        $(`[data-tutor-id="${showScheduleId}"]`).trigger('click');
    }

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

    // Initialize Flowbite modals
    const scheduleModal = new Modal(document.getElementById('schedule-modal'), {
        backdrop: 'static',
        onShow: () => {
            console.log('Schedule modal shown');
            initializeCalendar(); // Initialize calendar when modal is shown
        },
        onHide: () => {
            selectedLogsheetId = null;
            if (calendar) {
                calendar.destroy();
                calendar = null;
            }
            $('#logsheet_id').val('');
            $('#schedule-calendar').empty();
            $('.remaining-sessions-info').remove();
            $('.calendar-legend').remove();
        }
    });

    const addScheduleModal = new Modal(document.getElementById('add-schedule-modal'), {
        backdrop: 'static',
        onHide: () => {
            $('#add-schedule-form').trigger('reset');
        }
    });

    const editScheduleModal = new Modal(document.getElementById('edit-schedule-modal'), {
        backdrop: 'static',
        onHide: () => {
            $('#edit-schedule-form').trigger('reset');
        }
    });

    const cropModal = new Modal(document.getElementById('crop-modal'), {
        backdrop: 'static'
    });

    // Mapping modal IDs to modal instances
    const modalInstances = {
        'schedule-modal': scheduleModal,
        'add-schedule-modal': addScheduleModal,
        'edit-schedule-modal': editScheduleModal,
        'crop-modal': cropModal
    };

    // Event handler untuk tombol close pada semua modal
    document.querySelectorAll('[data-modal-hide]').forEach(button => {
        button.addEventListener('click', () => {
            const modalId = button.getAttribute('data-modal-hide');
            const modal = modalInstances[modalId];
            if (modal) {
                modal.hide();
            }
        });
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
                if (!selectedTutorId) {
                    successCallback([]);
                    return;
                }
                
                $.ajax({
                    url: `/tutor/${selectedTutorId}/schedules`,
                    method: 'GET',
                    data: selectedLogsheetId ? { logsheet_id: selectedLogsheetId } : {},
                    success: function(response) {
                        const events = response.schedules.map(schedule => ({
                            id: schedule.id,
                            title: `${schedule.logsheet_activity || ''} - Sesi ${schedule.session_number}${schedule.notes ? '\n' + schedule.notes : ''}`,
                            start: schedule.schedule_date,
                            className: `status-${schedule.status}`,
                            extendedProps: {
                                status: schedule.status,
                                notes: schedule.notes,
                                session_number: schedule.session_number,
                                logsheet_id: schedule.logsheet_id
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
                // Jika tidak ada logsheet yang dipilih, cek apakah ada logsheet yang masih punya sesi tersisa
                if (!selectedLogsheetId) {
                    $.ajax({
                        url: `/tutor/${selectedTutorId}/logsheets`,
                        method: 'GET',
                        success: function(response) {
                            if (response.data && response.data.length > 0) {
                                Swal.fire({
                                    icon: 'info',
                                    title: 'Pilih Logsheet',
                                    text: 'Untuk menambah jadwal baru, silakan pilih logsheet terlebih dahulu'
                                });
                            } else {
                                Swal.fire({
                                    icon: 'info',
                                    title: 'Tidak Ada Sesi Tersisa',
                                    text: 'Semua sesi pada semua logsheet sudah dijadwalkan'
                                });
                            }
                            return;
                        }
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
        if (!selectedTutorId) return;

        // Get all schedules for the tutor first
        $.ajax({
            url: `/tutor/${selectedTutorId}/schedules`,
            method: 'GET',
            success: function(scheduleResponse) {
                const updateColors = (response = null) => {
                    let allScheduledDates = scheduleResponse.schedules.map(s => s.schedule_date);
                    let remainingSessionsText = '';
                    
                    if (selectedLogsheetId && response) {
                        remainingSessions = response.remaining_sessions;
                        remainingSessionsText = `Pertemuan ke-${response.next_sequence} dari ${response.total_sequences}`;
                    }
                    
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

                        // Tanggal yang sudah lewat
                        if (cellDate < today) {
                            cell.classList.add('past-date');
                        } 
                        // Tanggal yang sudah ada jadwal
                        else if (allScheduledDates.includes(date)) {
                            cell.classList.add('booked-date');
                        }
                        // Tanggal yang masih available (jika ada logsheet yang dipilih)
                        else if (selectedLogsheetId && cellDate >= today && remainingSessions > 0) {
                            cell.classList.add('available-date');
                        }
                    });

                    // Update info sesi tersisa jika ada
                    const existingInfo = document.querySelector('.remaining-sessions-info');
                    if (existingInfo) {
                        existingInfo.remove();
                    }
                    
                    if (remainingSessionsText) {
                        const remainingSessionsInfo = document.createElement('div');
                        remainingSessionsInfo.className = 'text-sm text-gray-600 mt-2 remaining-sessions-info';
                        remainingSessionsInfo.textContent = remainingSessionsText;
                        const calendarHeader = document.querySelector('.fc-header-toolbar');
                        if (calendarHeader) {
                            calendarHeader.appendChild(remainingSessionsInfo);
                        }
                    }

                    // Tambahkan keterangan warna
                    const legendContainer = document.createElement('div');
                    legendContainer.className = 'calendar-legend flex gap-4 justify-center mt-4';
                    legendContainer.innerHTML = `
                        <div class="flex items-center">
                            <span class="w-3 h-3 inline-block mr-1 bg-red-200"></span>
                            <span class="text-sm">Sudah ada jadwal</span>
                        </div>
                        ${selectedLogsheetId && remainingSessions > 0 ? `
                        <div class="flex items-center">
                            <span class="w-3 h-3 inline-block mr-1 bg-green-200"></span>
                            <span class="text-sm">Tersedia</span>
                        </div>
                        ` : ''}
                        <div class="flex items-center">
                            <span class="w-3 h-3 inline-block mr-1 bg-gray-200"></span>
                            <span class="text-sm">Tanggal lewat</span>
                        </div>
                    `;

                    const existingLegend = document.querySelector('.calendar-legend');
                    if (existingLegend) {
                        existingLegend.remove();
                    }
                    const calendarEl = document.getElementById('schedule-calendar');
                    if (calendarEl) {
                        calendarEl.appendChild(legendContainer);
                    }
                };

                // If logsheet is selected, get its available dates
                if (selectedLogsheetId) {
                    $.ajax({
                        url: `/tutor/${selectedTutorId}/available-dates`,
                        method: 'GET',
                        data: {
                            logsheet_id: selectedLogsheetId,
                            month: calendar?.view?.currentStart?.toISOString().slice(0, 7) || new Date().toISOString().slice(0, 7)
                        },
                        success: updateColors,
                        error: () => updateColors()
                    });
                } else {
                    updateColors();
                }
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
                if (!response.success) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: response.message || 'Gagal memuat sesi yang tersedia'
                    });
                    return;
                }

                const sessionSelect = $('#session_number');
                sessionSelect.empty();
                sessionSelect.append('<option value="">Pilih nomor sesi</option>');

                if (response.available_sessions && response.available_sessions.length > 0) {
                    response.available_sessions.forEach(session => {
                        sessionSelect.append(`<option value="${session}">Sesi ${session}</option>`);
                    });
                    $('#schedule_date').val(selectedDate);
                    addScheduleModal.show();
                } else {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Tidak Ada Sesi Tersedia',
                        text: 'Semua sesi untuk logsheet ini sudah dijadwalkan'
                    });
                }
            },
            error: function(xhr) {
                let errorMessage = 'Gagal memuat sesi yang tersedia';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                }
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: errorMessage
                });
            }
        });
    }

    function showEditScheduleModal(event) {
        const schedule = event.extendedProps;
        $('#edit_schedule_date').val(event.startStr);
        $('#edit_status').val(schedule.status);
        $('#edit_notes').val(schedule.notes);
        editScheduleModal.show();
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
                    addScheduleModal.hide();
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
                    editScheduleModal.hide();
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
                            editScheduleModal.hide();
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
                console.log('Logsheets response:', response); // Debug log
                
                const logsheetSelect = $('#logsheet_id');
                logsheetSelect.empty();
                logsheetSelect.append('<option value="">Pilih logsheet</option>');

                if (response.success && response.data && response.data.length > 0) {
                    response.data.forEach(logsheet => {
                        const nextSeq = logsheet.available_sessions[0] || 'Selesai';
                        const totalSeq = logsheet.total_sequences;
                        const scheduledSessions = logsheet.scheduled_sessions;
                        
                        let statusText = nextSeq === 'Selesai' 
                            ? '(Selesai)' 
                            : `(Pertemuan ke-${nextSeq} dari ${totalSeq})`;
                            
                        logsheetSelect.append(
                            `<option value="${logsheet.id}">` +
                            `${logsheet.activity} - ${logsheet.customer} ${statusText}` +
                            `</option>`
                        );
                    });
                } else {
                    logsheetSelect.append('<option value="" disabled>Tidak ada logsheet tersedia</option>');
                }

                scheduleModal.show();
            },
            error: function(xhr) {
                console.error('Error loading logsheets:', xhr); // Debug log
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Gagal memuat data logsheet'
                });
            }
        });
    });
}); 

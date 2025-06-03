$(document).ready(function() {
    // Inisialisasi semua modal Flowbite
    const modals = {
        editTutorModal: new Modal(document.getElementById('edit-tutor-modal'), {
            placement: 'center',
            backdrop: 'dynamic',
            closable: true,
        }),
        cropModal: new Modal(document.getElementById('crop-modal'), {
            placement: 'center',
            backdrop: 'dynamic',
            closable: true,
        })
    };
    
    let cropper = null;
    let croppedImageData = null;

    // Handler untuk tombol edit
    $('.edit-tutor').on('click', function() {
        const tutorId = $(this).data('tutor-id');
        if (!tutorId) {
            console.error('Tutor ID tidak ditemukan');
            return;
        }

        // Tampilkan loading
        Swal.fire({
            title: 'Loading...',
            text: 'Mengambil data tutor',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });
        
        // Ambil data tutor
        fetch(`/tutor/${tutorId}/edit`)
            .then(response => response.json())
            .then(data => {
                $('#edit_id').val(data.id);
                $('#edit_name').val(data.name);
                $('#edit_email').val(data.email);
                $('#edit_phone').val(data.phone);
                $('#edit_address').val(data.address);
                $('#edit_birth_year').val(data.birth_year);
                $('#edit_description').val(data.description);
                
                // Set foto preview
                const previewPhoto = $('#preview_photo');
                previewPhoto.attr('src', data.photo_url || "https://via.placeholder.com/150");
                
                // Tutup loading dan tampilkan modal
                Swal.close();
                modals.editTutorModal.show();
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Gagal mengambil data tutor'
                });
            });
    });

    // Handler untuk tombol close dan cancel
    $('[data-modal-hide="edit-tutor-modal"]').on('click', function() {
        modals.editTutorModal.hide();
    });

    $('[data-modal-hide="crop-modal"]').on('click', function() {
        modals.cropModal.hide();
        if (cropper) {
            cropper.destroy();
            cropper = null;
        }
    });

    // Handler untuk form submit
    $('#editTutorForm').on('submit', function(e) {
        e.preventDefault();
        
        // Tampilkan loading saat submit
        Swal.fire({
            title: 'Menyimpan...',
            text: 'Sedang memproses data',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        const formData = new FormData(this);
        
        // Jika ada cropped image, tambahkan ke formData
        if (croppedImageData) {
            // Convert base64 to blob
            fetch(croppedImageData)
                .then(res => res.blob())
                .then(blob => {
                    formData.set('photo', blob, 'cropped-image.jpg');
                    submitForm(formData);
                });
        } else {
            submitForm(formData);
        }
    });

    function submitForm(formData) {
        const tutorId = $('#edit_id').val();
        
        fetch(`/tutor/${tutorId}`, {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil',
                    text: data.message
                }).then(() => {
                    window.location.reload();
                });
            } else {
                throw new Error(data.message || 'Terjadi kesalahan');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: error.message || 'Gagal mengupdate data tutor'
            });
        });
    }

    // Preview dan crop foto yang diupload
    $('#photo').on('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            // Validasi tipe file
            if (!file.type.startsWith('image/')) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'File harus berupa gambar'
                });
                return;
            }

            // Validasi ukuran file (max 2MB)
            if (file.size > 2 * 1024 * 1024) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Ukuran file maksimal 2MB'
                });
                return;
            }

            // Baca file sebagai URL
            const reader = new FileReader();
            reader.onload = function(e) {
                // Set image source untuk cropping
                const cropImage = document.getElementById('crop-image');
                cropImage.src = e.target.result;
                
                // Tampilkan modal cropping
                modals.cropModal.show();
                
                // Inisialisasi cropper
                if (cropper) {
                    cropper.destroy();
                }
                
                cropper = new Cropper(cropImage, {
                    aspectRatio: 1, // 1:1 ratio untuk foto profile
                    viewMode: 2,
                    dragMode: 'move',
                    autoCropArea: 1,
                    restore: false,
                    guides: true,
                    center: true,
                    highlight: false,
                    cropBoxMovable: true,
                    cropBoxResizable: true,
                    toggleDragModeOnDblclick: false,
                });
            }
            reader.readAsDataURL(file);
        }
    });

    // Handler untuk tombol crop
    $('#crop-button').on('click', function() {
        if (!cropper) return;

        // Dapatkan hasil crop dalam format base64
        croppedImageData = cropper.getCroppedCanvas({
            width: 400, // Set ukuran output
            height: 400,
            fillColor: '#fff',
            imageSmoothingEnabled: true,
            imageSmoothingQuality: 'high',
        }).toDataURL('image/jpeg', 0.8);

        // Update preview
        $('#preview_photo').attr('src', croppedImageData);
        
        // Tutup modal crop
        modals.cropModal.hide();
        cropper.destroy();
        cropper = null;
    });
}); 

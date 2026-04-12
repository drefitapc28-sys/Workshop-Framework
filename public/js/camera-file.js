/**
 * Camera JavaScript untuk Studi Kasus 3 - Tambah Customer 2 (FILE Storage)
 * Menggunakan HTML5 getUserMedia API
 */

const videoElement2 = document.getElementById('webcam2');
const canvasElement2 = document.getElementById('canvas2');
const photoPreview2 = document.getElementById('photoPreview2');
const startCameraBtn2 = document.getElementById('startCamera2');
const capturePhotoBtn2 = document.getElementById('capturePhoto2');
const stopCameraBtn2 = document.getElementById('stopCamera2');
const previewSection = document.getElementById('previewSection');
const fotoFileInput2 = document.getElementById('fotoFileInput2');

let stream2 = null;

/**
 * Mulai akses kamera
 */
function startCamera2() {
    // Check browser support
    if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
        alert('Browser Anda tidak mendukung akses kamera!');
        return;
    }

    // Request camera access
    navigator.mediaDevices.getUserMedia({ 
        video: { 
            facingMode: 'user',
            width: { ideal: 1280 },
            height: { ideal: 720 }
        } 
    })
    .then(function(mediaStream) {
        stream2 = mediaStream;
        videoElement2.srcObject = mediaStream;
        videoElement2.play();
        
        // Update button state
        startCameraBtn2.disabled = true;
        startCameraBtn2.classList.add('disabled');
        stopCameraBtn2.disabled = false;
        stopCameraBtn2.classList.remove('disabled');
        
        console.log('✅ Kamera berhasil diakses');
    })
    .catch(function(error) {
        console.error('❌ Error mengakses kamera:', error);
        
        if (error.name === 'NotAllowedError') {
            alert('Anda perlu mengizinkan akses kamera untuk fitur ini.');
        } else if (error.name === 'NotFoundError') {
            alert('Kamera tidak ditemukan di device ini.');
        } else {
            alert('Error mengakses kamera: ' + error.message);
        }
    });
}

/**
 * Ambil foto dari video stream ke canvas
 */
function capturePhoto2() {
    if (!stream2) {
        alert('Mulai kamera terlebih dahulu!');
        return;
    }

    const context = canvasElement2.getContext('2d');
    
    // Draw video frame ke canvas
    // Note: canvas perlu width/height yang sesuai dengan video aspect ratio
    context.drawImage(videoElement2, 0, 0, canvasElement2.width, canvasElement2.height);
    
    // Convert canvas ke base64 PNG
    const imageData = canvasElement2.toDataURL('image/png');
    
    // Tampilkan preview
    photoPreview2.src = imageData;
    photoPreview2.style.display = 'block';
    previewSection.style.display = 'block';
    
    // Simpan ke hidden input
    fotoFileInput2.value = imageData;
    
    console.log('✅ Foto berhasil ditangkap');
}

/**
 * Stop akses kamera
 */
function stopCamera2() {
    if (stream2) {
        // Stop semua tracks
        stream2.getTracks().forEach(function(track) {
            track.stop();
        });
        
        stream2 = null;
        videoElement2.srcObject = null;
        
        // Update button state
        startCameraBtn2.disabled = false;
        startCameraBtn2.classList.remove('disabled');
        stopCameraBtn2.disabled = true;
        stopCameraBtn2.classList.add('disabled');
        
        console.log('✅ Kamera dihentikan');
    }
}

/**
 * Event Listeners
 */
document.addEventListener('DOMContentLoaded', function() {
    startCameraBtn2.addEventListener('click', startCamera2);
    capturePhotoBtn2.addEventListener('click', capturePhoto2);
    stopCameraBtn2.addEventListener('click', stopCamera2);
    
    // Stop kamera saat halaman menutup
    window.addEventListener('beforeunload', function() {
        if (stream2) {
            stream2.getTracks().forEach(function(track) {
                track.stop();
            });
        }
    });
});

/**
 * Handle resize window untuk adjust canvas
 */
window.addEventListener('resize', function() {
    if (videoElement2.videoWidth > 0) {
        canvasElement2.width = videoElement2.videoWidth;
        canvasElement2.height = videoElement2.videoHeight;
    }
});

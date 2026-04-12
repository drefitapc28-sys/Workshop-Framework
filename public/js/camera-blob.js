/**
 * Camera JavaScript untuk Studi Kasus 3 - Tambah Customer 1 (BLOB Storage)
 * Menggunakan HTML5 getUserMedia API
 */

const videoElement1 = document.getElementById('webcam1');
const canvasElement1 = document.getElementById('canvas1');
const photoPreview1 = document.getElementById('photoPreview1');
const startCameraBtn1 = document.getElementById('startCamera1');
const capturePhotoBtn1 = document.getElementById('capturePhoto1');
const stopCameraBtn1 = document.getElementById('stopCamera1');
const previewSection = document.getElementById('previewSection');
const fotoBlobInput1 = document.getElementById('fotoBlobInput1');

let stream1 = null;

/**
 * Mulai akses kamera
 */
function startCamera1() {
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
        stream1 = mediaStream;
        videoElement1.srcObject = mediaStream;
        videoElement1.play();
        
        // Update button state
        startCameraBtn1.disabled = true;
        startCameraBtn1.classList.add('disabled');
        stopCameraBtn1.disabled = false;
        stopCameraBtn1.classList.remove('disabled');
        
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
function capturePhoto1() {
    if (!stream1) {
        alert('Mulai kamera terlebih dahulu!');
        return;
    }

    const context = canvasElement1.getContext('2d');
    
    // Draw video frame ke canvas
    // Note: canvas perlu width/height yang sesuai dengan video aspect ratio
    context.drawImage(videoElement1, 0, 0, canvasElement1.width, canvasElement1.height);
    
    // Convert canvas ke base64 PNG
    const imageData = canvasElement1.toDataURL('image/png');
    
    // Tampilkan preview
    photoPreview1.src = imageData;
    photoPreview1.style.display = 'block';
    previewSection.style.display = 'block';
    
    // Simpan ke hidden input
    fotoBlobInput1.value = imageData;
    
    console.log('✅ Foto berhasil ditangkap');
}

/**
 * Stop akses kamera
 */
function stopCamera1() {
    if (stream1) {
        // Stop semua tracks
        stream1.getTracks().forEach(function(track) {
            track.stop();
        });
        
        stream1 = null;
        videoElement1.srcObject = null;
        
        // Update button state
        startCameraBtn1.disabled = false;
        startCameraBtn1.classList.remove('disabled');
        stopCameraBtn1.disabled = true;
        stopCameraBtn1.classList.add('disabled');
        
        console.log('✅ Kamera dihentikan');
    }
}

/**
 * Event Listeners
 */
document.addEventListener('DOMContentLoaded', function() {
    startCameraBtn1.addEventListener('click', startCamera1);
    capturePhotoBtn1.addEventListener('click', capturePhoto1);
    stopCameraBtn1.addEventListener('click', stopCamera1);
    
    // Stop kamera saat halaman menutup
    window.addEventListener('beforeunload', function() {
        if (stream1) {
            stream1.getTracks().forEach(function(track) {
                track.stop();
            });
        }
    });
});

/**
 * Handle resize window untuk adjust canvas
 */
window.addEventListener('resize', function() {
    if (videoElement1.videoWidth > 0) {
        canvasElement1.width = videoElement1.videoWidth;
        canvasElement1.height = videoElement1.videoHeight;
    }
});

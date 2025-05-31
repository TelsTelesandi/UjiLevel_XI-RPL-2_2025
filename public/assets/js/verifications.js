// DOM Elements
const verificationModal = document.getElementById('verificationModal');
const verificationForm = document.getElementById('verificationForm');
const eventTableBody = document.getElementById('eventTableBody');
const statusFilter = document.getElementById('statusFilter');
const dateFilter = document.getElementById('dateFilter');
const mainContent = document.querySelector('.flex-1');
const sidebar = document.querySelector('.sidebar');
const searchInput = document.getElementById('searchVerification');
let selectedEventId = null;

// Toggle sidebar
document.getElementById('sidebar-toggle').addEventListener('click', function() {
    sidebar.classList.toggle('hidden');
    mainContent.classList.toggle('ml-0');
    
    // Animate content width change
    if (sidebar.classList.contains('hidden')) {
        mainContent.style.marginLeft = '0';
    } else {
        mainContent.style.marginLeft = '16rem';
    }
});

// Close modal when clicking (x) or outside
document.querySelector('.close').addEventListener('click', closeModal);
window.addEventListener('click', (e) => {
    if (e.target === verificationModal) closeModal();
});

// Load events on page load
document.addEventListener('DOMContentLoaded', loadEvents);

// Add filter event listeners
statusFilter.addEventListener('change', filterEvents);
dateFilter.addEventListener('change', filterEvents);

// Form submit handler
verificationForm.addEventListener('submit', handleVerificationSubmit);

// Search and filter functionality
searchInput.addEventListener('input', debounce(() => {
    loadEvents();
}, 300));

// Functions
function showVerificationModal(eventId) {
    fetch(`../../../index.php?action=get_event&id=${eventId}`)
        .then(response => response.json())
        .then(event => {
            // Populate event details
            const detailsHtml = `
                <div class="space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div class="p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-all duration-200">
                            <label class="block text-sm font-medium text-gray-700">Judul Event:</label>
                            <span class="mt-1 block text-sm text-gray-900">${event.judul_event}</span>
                        </div>
                        <div class="p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-all duration-200">
                            <label class="block text-sm font-medium text-gray-700">Pengaju:</label>
                            <span class="mt-1 block text-sm text-gray-900">${event.nama_lengkap}</span>
                        </div>
                        <div class="p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-all duration-200">
                            <label class="block text-sm font-medium text-gray-700">Jenis Kegiatan:</label>
                            <span class="mt-1 block text-sm text-gray-900">${event.jenis_kegiatan}</span>
                        </div>
                        <div class="p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-all duration-200">
                            <label class="block text-sm font-medium text-gray-700">Total Biaya:</label>
                            <span class="mt-1 block text-sm text-gray-900">${formatCurrency(event.Total_pembiayaan)}</span>
                        </div>
                    </div>
                    <div class="p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-all duration-200">
                        <label class="block text-sm font-medium text-gray-700">Deskripsi:</label>
                        <p class="mt-1 text-sm text-gray-900">${event.deskripsi}</p>
                    </div>
                    <div class="p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-all duration-200">
                        <label class="block text-sm font-medium text-gray-700">Status Saat Ini:</label>
                        <span class="mt-1 inline-flex px-2 py-1 text-sm rounded-full ${getStatusColorClass(event.status)}">
                            ${getStatusLabel(event.status)}
                        </span>
                    </div>
                    <div class="p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-all duration-200">
                        <label class="block text-sm font-medium text-gray-700">Proposal:</label>
                        <a href="../../../uploads/proposals/${event.Proposal}" target="_blank" 
                           class="mt-1 inline-flex items-center text-sm text-blue-600 hover:text-blue-700 group">
                            <i class="fas fa-file-pdf mr-2 group-hover:animate-bounce"></i>
                            <span class="group-hover:underline">Lihat Proposal</span>
                        </a>
                    </div>
                </div>
            `;
            
            document.getElementById('eventDetails').innerHTML = detailsHtml;
            
            // Set form data
            verificationForm.dataset.eventId = event.event_id;
            document.getElementById('verificationStatus').value = event.status || 'pending';
            document.getElementById('verificationNotes').value = event.catatan_admin || '';
            
            verificationModal.classList.remove('hidden');
            setTimeout(() => {
                verificationModal.querySelector('.relative').classList.add('scale-100', 'opacity-100');
            }, 100);
        })
        .catch(error => showAlert('Error loading event data', 'error'));
}

function closeModal() {
    const modalContent = verificationModal.querySelector('.relative');
    modalContent.classList.remove('scale-100', 'opacity-100');
    modalContent.classList.add('scale-95', 'opacity-0');
    
    setTimeout(() => {
        verificationModal.classList.add('hidden');
        verificationForm.reset();
        modalContent.classList.remove('scale-95', 'opacity-0');
    }, 200);
}

async function loadEvents() {
    try {
        const status = statusFilter.value;
        const date = dateFilter.value;
        
        // Show loading state
        eventTableBody.innerHTML = `
            <tr>
                <td colspan="8" class="px-6 py-4 text-center">
                    <div class="animate-pulse flex justify-center items-center space-x-2">
                        <div class="w-4 h-4 bg-blue-400 rounded-full"></div>
                        <div class="w-4 h-4 bg-blue-400 rounded-full"></div>
                        <div class="w-4 h-4 bg-blue-400 rounded-full"></div>
                    </div>
                </td>
            </tr>
        `;
        
        const queryParams = new URLSearchParams({
            status: status !== 'all' ? status : '',
            date: date
        });
        
        const response = await fetch(`../../../index.php?action=get_events&${queryParams}`);
        const events = await response.json();
        
        // Add animation delay to each row
        eventTableBody.innerHTML = events.map((event, index) => `
            <tr class="hover:bg-gray-50 transition-all duration-200 opacity-0 transform translate-y-4" 
                style="animation: fadeInUp 0.3s ease-out ${index * 0.1}s forwards;">
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${event.event_id}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${event.judul_event}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${event.nama_lengkap}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${event.jenis_kegiatan}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${formatCurrency(event.Total_pembiayaan)}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${formatDate(event.tanggal_pengajuan)}</td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <span class="inline-flex px-2 py-1 text-sm rounded-full ${getStatusColorClass(event.status)}">
                        ${getStatusLabel(event.status)}
                    </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                    <button onclick="showVerificationModal(${event.event_id})" 
                            class="text-blue-600 hover:text-blue-900 mr-3 transform hover:scale-110 transition-all duration-200">
                        <i class="fas fa-check-circle"></i>
                    </button>
                    <button onclick="downloadProposal('${event.Proposal}')"
                            class="text-green-600 hover:text-green-900 transform hover:scale-110 transition-all duration-200">
                        <i class="fas fa-download"></i>
                    </button>
                </td>
            </tr>
        `).join('');

        // Add fadeInUp animation
        const style = document.createElement('style');
        style.textContent = `
            @keyframes fadeInUp {
                from {
                    opacity: 0;
                    transform: translateY(1rem);
                }
                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }
        `;
        document.head.appendChild(style);
    } catch (error) {
        showAlert('Error loading events', 'error');
    }
}

async function handleVerificationSubmit(e) {
    e.preventDefault();
    
    const submitButton = e.target.querySelector('button[type="submit"]');
    const originalContent = submitButton.innerHTML;
    
    // Show loading state
    submitButton.disabled = true;
    submitButton.innerHTML = `
        <div class="flex items-center space-x-2">
            <i class="fas fa-circle-notch fa-spin"></i>
            <span>Memproses...</span>
        </div>
    `;
    
    const eventId = verificationForm.dataset.eventId;
    const status = document.getElementById('verificationStatus').value;
    const catatan = document.getElementById('verificationNotes').value;
    
    const formData = new FormData();
    formData.append('event_id', eventId);
    formData.append('status', status);
    formData.append('catatan_admin', catatan);
    
    try {
        const response = await fetch('../../../index.php?action=verify_event', {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            credentials: 'same-origin'
        });
        
        if (!response.ok) {
            if (response.status === 403) {
                window.location.href = '../../../index.php?action=login';
                return;
            }
            throw new Error('Network response was not ok');
        }
        
        const result = await response.json();
        
        if (result.success) {
            showAlert('Event berhasil diverifikasi', 'success');
            closeModal();
            await loadEvents(); // Reload data setelah verifikasi
        } else {
            showAlert(result.message || 'Verifikasi gagal', 'error');
        }
    } catch (error) {
        console.error('Error:', error);
        showAlert('Error memproses verifikasi: ' + error.message, 'error');
    } finally {
        // Restore button state
        submitButton.disabled = false;
        submitButton.innerHTML = originalContent;
    }
}

function downloadProposal(filename) {
    window.open(`../../../uploads/proposals/${filename}`, '_blank');
}

function filterEvents() {
    loadEvents();
}

// Utility Functions
function formatCurrency(amount) {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR'
    }).format(amount);
}

function formatDate(dateString) {
    return new Date(dateString).toLocaleDateString('id-ID', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    });
}

function getStatusLabel(status) {
    const labels = {
        'pending': 'Menunggu',
        'approved': 'Disetujui',
        'rejected': 'Ditolak'
    };
    return labels[status] || status;
}

function getStatusColorClass(status) {
    const colors = {
        'pending': 'bg-yellow-100 text-yellow-800',
        'approved': 'bg-green-100 text-green-800',
        'rejected': 'bg-red-100 text-red-800'
    };
    return colors[status] || 'bg-gray-100 text-gray-800';
}

function showAlert(message, type) {
    const alertDiv = document.createElement('div');
    alertDiv.className = `fixed top-4 right-4 px-4 py-2 rounded-lg text-white transform translate-x-full transition-transform duration-300 ${
        type === 'success' ? 'bg-green-500' : 'bg-red-500'
    }`;
    alertDiv.innerHTML = `
        <div class="flex items-center space-x-2">
            <i class="fas ${type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'}"></i>
            <span>${message}</span>
        </div>
    `;
    
    document.body.appendChild(alertDiv);
    
    // Slide in
    setTimeout(() => {
        alertDiv.classList.remove('translate-x-full');
    }, 100);
    
    // Slide out and remove
    setTimeout(() => {
        alertDiv.classList.add('translate-x-full');
        setTimeout(() => {
            alertDiv.remove();
        }, 300);
    }, 3000);
}

function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func.apply(this, args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
} 
// DOM Elements
const eventModal = document.getElementById('eventModal');
const verificationForm = document.getElementById('verificationForm');
const eventTableBody = document.getElementById('eventTableBody');
const statusFilter = document.getElementById('statusFilter');
const dateFilter = document.getElementById('dateFilter');

// Toggle sidebar
document.getElementById('sidebar-toggle').addEventListener('click', function() {
    document.querySelector('.sidebar').classList.toggle('collapsed');
    document.querySelector('.main-content').classList.toggle('expanded');
});

// Close modal when clicking (x) or outside
document.querySelector('.close').addEventListener('click', closeModal);
window.addEventListener('click', (e) => {
    if (e.target === eventModal) closeModal();
});

// Load events on page load
document.addEventListener('DOMContentLoaded', loadEvents);

// Form submit handler
verificationForm.addEventListener('submit', handleVerificationSubmit);

// Functions
function showEventModal(eventId) {
    fetch(`./index.php?action=get_event&id=${eventId}`)
        .then(response => response.json())
        .then(event => {
            document.getElementById('eventTitle').textContent = event.judul_event;
            document.getElementById('eventUser').textContent = event.nama_lengkap;
            document.getElementById('eventType').textContent = event.jenis_kegiatan;
            document.getElementById('eventBudget').textContent = formatCurrency(event.Total_pembiayaan);
            document.getElementById('eventDescription').textContent = event.deskripsi;
            document.getElementById('proposalLink').href = `./uploads/proposals/${event.Proposal}`;
            document.getElementById('eventStatus').textContent = getStatusLabel(event.status);
            
            // Set form data
            verificationForm.dataset.eventId = event.event_id;
            document.getElementById('verificationStatus').value = event.status || 'pending';
            document.getElementById('verificationNotes').value = event.catatan_admin || '';
            
            eventModal.style.display = 'block';
        })
        .catch(error => showAlert('Error loading event data', 'error'));
}

function closeModal() {
    eventModal.style.display = 'none';
    verificationForm.reset();
}

async function loadEvents() {
    try {
        const status = statusFilter.value;
        const date = dateFilter.value;
        
        const queryParams = new URLSearchParams({
            status: status !== 'all' ? status : '',
            date: date
        });
        
        const response = await fetch(`./index.php?action=get_events&${queryParams}`);
        const events = await response.json();
        
        eventTableBody.innerHTML = events.map(event => `
            <tr>
                <td>${event.event_id}</td>
                <td>${event.judul_event}</td>
                <td>${event.nama_lengkap}</td>
                <td>${event.jenis_kegiatan}</td>
                <td>${formatCurrency(event.Total_pembiayaan)}</td>
                <td>${formatDate(event.tanggal_pengajuan)}</td>
                <td>
                    <span class="status-badge ${event.status}">
                        ${getStatusLabel(event.status)}
                    </span>
                </td>
                <td class="actions">
                    <button onclick="showEventModal(${event.event_id})" class="btn-view">
                        <i class="fas fa-eye"></i>
                    </button>
                    <button onclick="downloadProposal('${event.Proposal}')" class="btn-download">
                        <i class="fas fa-download"></i>
                    </button>
                </td>
            </tr>
        `).join('');
    } catch (error) {
        showAlert('Error loading events', 'error');
    }
}

async function handleVerificationSubmit(e) {
    e.preventDefault();
    
    const eventId = verificationForm.dataset.eventId;
    const formData = new FormData(verificationForm);
    formData.append('event_id', eventId);
    
    try {
        const response = await fetch('./index.php?action=verify_event', {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            credentials: 'same-origin'
        });
        
        if (!response.ok) {
            if (response.status === 403) {
                window.location.href = './index.php?action=login';
                return;
            }
            throw new Error('Network response was not ok');
        }
        
        const result = await response.json();
        
        if (result.success) {
            showAlert(result.message, 'success');
            closeModal();
            loadEvents();
        } else {
            showAlert(result.message || 'Verifikasi gagal', 'error');
        }
    } catch (error) {
        console.error('Error:', error);
        showAlert('Error memproses verifikasi', 'error');
    }
}

function downloadProposal(filename) {
    window.open(`./uploads/proposals/${filename}`, '_blank');
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

function showAlert(message, type) {
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type}`;
    alertDiv.textContent = message;
    
    document.querySelector('.content-wrapper').insertBefore(
        alertDiv,
        document.querySelector('.content-header')
    );
    
    setTimeout(() => alertDiv.remove(), 3000);
} 
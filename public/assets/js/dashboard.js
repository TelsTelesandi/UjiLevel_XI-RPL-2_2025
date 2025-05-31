// DOM Elements
const totalUsers = document.getElementById('totalUsers');
const totalEvents = document.getElementById('totalEvents');
const pendingEvents = document.getElementById('pendingEvents');
const approvedEvents = document.getElementById('approvedEvents');
const activitiesList = document.getElementById('activitiesList');

// Charts
let eventStatusChart;
let monthlyEventsChart;

// Toggle sidebar
document.getElementById('sidebar-toggle').addEventListener('click', function() {
    document.querySelector('.sidebar').classList.toggle('collapsed');
    document.querySelector('.main-content').classList.toggle('expanded');
});

// Load all dashboard data on page load
document.addEventListener('DOMContentLoaded', () => {
    loadStatistics();
    loadCharts();
    loadRecentActivities();
});

// Functions
async function loadStatistics() {
    try {
        const response = await fetch('./index.php?action=get_dashboard_stats');
        const stats = await response.json();
        
        totalUsers.textContent = stats.total_users;
        totalEvents.textContent = stats.total_events;
        pendingEvents.textContent = stats.pending_events;
        approvedEvents.textContent = stats.approved_events;
    } catch (error) {
        showAlert('Error loading statistics', 'error');
    }
}

async function loadCharts() {
    try {
        // Load event status distribution
        const statusResponse = await fetch('./index.php?action=get_event_status_stats');
        const statusStats = await statusResponse.json();
        
        createEventStatusChart(statusStats);
        
        // Load monthly submissions
        const monthlyResponse = await fetch('./index.php?action=get_monthly_event_stats');
        const monthlyStats = await monthlyResponse.json();
        
        createMonthlyEventsChart(monthlyStats);
    } catch (error) {
        showAlert('Error loading charts', 'error');
    }
}

function createEventStatusChart(data) {
    const ctx = document.getElementById('eventStatusChart').getContext('2d');
    
    if (eventStatusChart) {
        eventStatusChart.destroy();
    }
    
    eventStatusChart = new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: ['Pending', 'Approved', 'Rejected'],
            datasets: [{
                data: [data.pending, data.approved, data.rejected],
                backgroundColor: [
                    '#ffc107',  // Pending - Yellow
                    '#28a745',  // Approved - Green
                    '#dc3545'   // Rejected - Red
                ]
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });
}

function createMonthlyEventsChart(data) {
    const ctx = document.getElementById('monthlyEventsChart').getContext('2d');
    
    if (monthlyEventsChart) {
        monthlyEventsChart.destroy();
    }
    
    monthlyEventsChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: data.labels,
            datasets: [{
                label: 'Event Submissions',
                data: data.values,
                borderColor: '#4e73df',
                backgroundColor: 'rgba(78, 115, 223, 0.1)',
                borderWidth: 2,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            },
            plugins: {
                legend: {
                    display: false
                }
            }
        }
    });
}

async function loadRecentActivities() {
    try {
        const response = await fetch('./index.php?action=get_recent_activities');
        const activities = await response.json();
        
        activitiesList.innerHTML = activities.length ? activities.map(activity => `
            <div class="activity-item">
                <div class="activity-icon ${getActivityIcon(activity.type)}">
                    <i class="${getActivityIconClass(activity.type)}"></i>
                </div>
                <div class="activity-details">
                    <p class="activity-text">${activity.description}</p>
                    <span class="activity-time">${formatTimeAgo(activity.timestamp)}</span>
                </div>
            </div>
        `).join('') : '<p class="no-activities">No recent activities</p>';
    } catch (error) {
        showAlert('Error loading activities', 'error');
    }
}

// Utility Functions
function getActivityIcon(type) {
    const icons = {
        'user': 'user',
        'event': 'calendar',
        'verification': 'check-circle'
    };
    return icons[type] || 'info-circle';
}

function getActivityIconClass(type) {
    const icons = {
        'user': 'fas fa-user',
        'event': 'fas fa-calendar',
        'verification': 'fas fa-check-circle'
    };
    return icons[type] || 'fas fa-info-circle';
}

function formatTimeAgo(timestamp) {
    const date = new Date(timestamp);
    const now = new Date();
    const seconds = Math.floor((now - date) / 1000);
    
    let interval = Math.floor(seconds / 31536000);
    if (interval > 1) return interval + ' years ago';
    
    interval = Math.floor(seconds / 2592000);
    if (interval > 1) return interval + ' months ago';
    
    interval = Math.floor(seconds / 86400);
    if (interval > 1) return interval + ' days ago';
    
    interval = Math.floor(seconds / 3600);
    if (interval > 1) return interval + ' hours ago';
    
    interval = Math.floor(seconds / 60);
    if (interval > 1) return interval + ' minutes ago';
    
    return 'just now';
}

function showAlert(message, type) {
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type}`;
    alertDiv.textContent = message;
    
    document.querySelector('.content-wrapper').insertBefore(
        alertDiv,
        document.querySelector('.stats-grid')
    );
    
    setTimeout(() => alertDiv.remove(), 3000);
} 
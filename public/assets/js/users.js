// DOM Elements
const userModal = document.getElementById('userModal');
const userForm = document.getElementById('userForm');
const modalTitle = document.getElementById('modalTitle');
const userTableBody = document.getElementById('userTableBody');

// Toggle sidebar
document.getElementById('sidebar-toggle').addEventListener('click', function() {
    document.querySelector('.sidebar').classList.toggle('collapsed');
    document.querySelector('.main-content').classList.toggle('expanded');
});

// Close modal when clicking (x) or outside
document.querySelector('.close').addEventListener('click', closeModal);
window.addEventListener('click', (e) => {
    if (e.target === userModal) closeModal();
});

// Load users on page load
document.addEventListener('DOMContentLoaded', loadUsers);

// Form submit handler
userForm.addEventListener('submit', handleFormSubmit);

// Functions
function showAddUserModal() {
    modalTitle.textContent = 'Tambah User';
    userForm.reset();
    userForm.dataset.mode = 'add';
    userModal.style.display = 'block';
}

function showEditUserModal(userId) {
    modalTitle.textContent = 'Edit User';
    userForm.dataset.mode = 'edit';
    userForm.dataset.userId = userId;
    
    // Fetch user data and populate form
    fetch(`./index.php?action=get_user&id=${userId}`)
        .then(response => response.json())
        .then(user => {
            document.getElementById('userId').value = user.user_id;
            document.getElementById('username').value = user.username;
            document.getElementById('nama_lengkap').value = user.nama_lengkap;
            document.getElementById('role').value = user.role;
            document.getElementById('ekskul').value = user.ekskul;
            userModal.style.display = 'block';
        })
        .catch(error => showAlert('Error loading user data', 'error'));
}

function closeModal() {
    userModal.style.display = 'none';
    userForm.reset();
}

async function loadUsers() {
    try {
        const response = await fetch('./index.php?action=get_users');
        const users = await response.json();
        
        userTableBody.innerHTML = users.map(user => `
            <tr>
                <td>${user.user_id}</td>
                <td>${user.username}</td>
                <td>${user.nama_lengkap}</td>
                <td>${user.role}</td>
                <td>${user.ekskul}</td>
                <td class="actions">
                    <button onclick="showEditUserModal(${user.user_id})" class="btn-edit">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button onclick="deleteUser(${user.user_id})" class="btn-delete">
                        <i class="fas fa-trash"></i>
                    </button>
                </td>
            </tr>
        `).join('');
    } catch (error) {
        showAlert('Error loading users', 'error');
    }
}

async function handleFormSubmit(e) {
    e.preventDefault();
    
    const formData = new FormData(userForm);
    const mode = userForm.dataset.mode;
    const userId = userForm.dataset.userId;
    
    try {
        const url = mode === 'add' 
            ? './index.php?action=add_user'
            : `./index.php?action=update_user&id=${userId}`;
            
        const response = await fetch(url, {
            method: 'POST',
            body: formData
        });
        
        const result = await response.json();
        
        if (result.success) {
            showAlert(result.message, 'success');
            closeModal();
            loadUsers();
        } else {
            showAlert(result.message, 'error');
        }
    } catch (error) {
        showAlert('Error processing request', 'error');
    }
}

async function deleteUser(userId) {
    if (!confirm('Are you sure you want to delete this user?')) return;
    
    try {
        const response = await fetch(`./index.php?action=delete_user&id=${userId}`, {
            method: 'POST'
        });
        const result = await response.json();
        
        if (result.success) {
            showAlert(result.message, 'success');
            loadUsers();
        } else {
            showAlert(result.message, 'error');
        }
    } catch (error) {
        showAlert('Error deleting user', 'error');
    }
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
document.addEventListener('DOMContentLoaded', () => {
  const userForm = document.getElementById('userForm');
  const userTable = document.getElementById('userTableBody');

  let users = JSON.parse(localStorage.getItem('users')) || [];

  function renderTable() {
    userTable.innerHTML = '';
    users.forEach((user, index) => {
      const tr = document.createElement('tr');
      tr.innerHTML = `
        <td>${user.username}</td>
        <td>${user.role}</td>
        <td>
          <button onclick="editUser(${index})">Edit</button>
          <button onclick="deleteUser(${index})">Hapus</button>
        </td>
      `;
      userTable.appendChild(tr);
    });
  }

  window.editUser = (index) => {
    const user = users[index];
    userForm.username.value = user.username;
    userForm.password.value = user.password;
    userForm.role.value = user.role;
    userForm.id.value = index;
  };

  window.deleteUser = (index) => {
    Swal.fire({
      title: 'Hapus User?',
      text: "Data tidak bisa dikembalikan!",
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#e74c3c',
      cancelButtonColor: '#aaa',
      confirmButtonText: 'Ya, hapus'
    }).then((result) => {
      if (result.isConfirmed) {
        users.splice(index, 1);
        localStorage.setItem('users', JSON.stringify(users));
        renderTable();
        Swal.fire('Dihapus!', 'User telah dihapus.', 'success');
      }
    });
  };

  userForm.addEventListener('submit', function (e) {
    e.preventDefault();
    const formData = new FormData(userForm);
    const data = Object.fromEntries(formData.entries());

    if (data.id === '') {
      // Tambah user baru
      users.push({ username: data.username, password: data.password, role: data.role });
    } else {
      // Update user
      users[data.id] = { username: data.username, password: data.password, role: data.role };
    }

    localStorage.setItem('users', JSON.stringify(users));
    userForm.reset();
    renderTable();

    Swal.fire({
      icon: 'success',
      title: 'Berhasil',
      text: 'Data user berhasil disimpan!',
      timer: 1500,
      showConfirmButton: false
    });
  });

  renderTable();
});

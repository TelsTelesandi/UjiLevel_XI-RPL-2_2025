@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <h2 class="text-2xl font-bold text-gray-800">Manajemen User</h2>
        <button class="rounded-lg bg-blue-500 px-4 py-2 text-white hover:bg-blue-600" onclick="openModal()">
            <i class="fas fa-user-plus mr-2"></i>
            Tambah User
        </button>
    </div>

    <!-- Flash Message -->
    @if(session('success'))
    <div class="rounded-lg bg-green-100 p-4 text-green-700">
        <div class="flex">
            <div class="flex-shrink-0">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="ml-3">
                <p class="text-sm font-medium">{{ session('success') }}</p>
            </div>
            <div class="ml-auto pl-3">
                <div class="-mx-1.5 -my-1.5">
                    <button type="button" class="inline-flex rounded-md bg-green-100 p-1.5 text-green-500 hover:bg-green-200" onclick="this.parentElement.parentElement.parentElement.parentElement.remove()">
                        <span class="sr-only">Dismiss</span>
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Search and Filter -->
    <div class="flex items-center justify-between rounded-xl bg-white p-4 shadow-sm">
        <div class="relative flex-1 max-w-xs">
            <input type="text" placeholder="Cari user..." class="w-full rounded-lg border border-gray-300 pl-10 pr-4 py-2 focus:border-blue-500 focus:outline-none">
            <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
        </div>
        <div class="flex items-center space-x-4">
            <select class="rounded-lg border border-gray-300 px-4 py-2 focus:border-blue-500 focus:outline-none">
                <option>Semua Role</option>
                <option>Admin</option>
                <option>User</option>
            </select>
            <select class="rounded-lg border border-gray-300 px-4 py-2 focus:border-blue-500 focus:outline-none">
                <option>Status</option>
                <option>Aktif</option>
                <option>Nonaktif</option>
            </select>
        </div>
    </div>

    <!-- Users Table -->
    <div class="rounded-xl bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Username</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Nama Lengkap</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Ekskul</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Role</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Tanggal Dibuat</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 bg-white">
                    @foreach($users as $user)
                    <tr>
                        <td class="whitespace-nowrap px-6 py-4">
                            <div class="text-sm font-medium text-gray-900">{{ $user->username }}</div>
                        </td>
                        <td class="whitespace-nowrap px-6 py-4">
                            <div class="flex items-center">
                                <div class="h-10 w-10 flex-shrink-0">
                                    <img class="h-10 w-10 rounded-full" src="https://ui-avatars.com/api/?name={{ urlencode($user->nama_lengkap) }}" alt="{{ $user->nama_lengkap }}">
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">{{ $user->nama_lengkap }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="whitespace-nowrap px-6 py-4">
                            <div class="text-sm text-gray-500">{{ $user->ekskul }}</div>
                        </td>
                        <td class="whitespace-nowrap px-6 py-4">
                            <span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold {{ $user->role === 'admin' ? 'bg-purple-100 text-purple-800' : 'bg-blue-100 text-blue-800' }}">
                                {{ ucfirst($user->role) }}
                            </span>
                        </td>
                        <td class="whitespace-nowrap px-6 py-4">
                            <div class="text-sm text-gray-500">{{ $user->created_at->format('d M Y') }}</div>
                        </td>
                        <td class="whitespace-nowrap px-6 py-4 text-sm">
                            <div class="flex space-x-2">
                                <button class="text-blue-600 hover:text-blue-900" onclick="openEditModal({{ $user->user_id }}, '{{ $user->username }}', '{{ $user->nama_lengkap }}', '{{ $user->ekskul }}', '{{ $user->role }}')">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="text-red-600 hover:text-red-900" onclick="openDeleteModal({{ $user->user_id }}, '{{ $user->nama_lengkap }}')">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        <div class="px-6 py-4">
            {{ $users->links() }}
        </div>
    </div>
</div>

<!-- Add User Modal -->
<div id="userModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex min-h-screen items-end justify-center px-4 pb-20 pt-4 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
        <span class="hidden sm:inline-block sm:h-screen sm:align-middle" aria-hidden="true">&#8203;</span>
        <div class="inline-block transform overflow-hidden rounded-lg bg-white text-left align-bottom shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg sm:align-middle">
            <form id="userForm" method="POST" action="{{ route('admin.users.store') }}">
                @csrf
                <div id="method_field"></div>
                <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mt-3 w-full text-center sm:ml-4 sm:mt-0 sm:text-left">
                            <h3 class="text-lg font-medium leading-6 text-gray-900" id="modal-title">
                                Tambah User Baru
                            </h3>
                            <div class="mt-4 space-y-4">
                                <div>
                                    <label for="username" class="block text-sm font-medium text-gray-700">Username</label>
                                    <input type="text" name="username" id="username" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-blue-500 sm:text-sm" required>
                                </div>
                                <div>
                                    <label for="nama_lengkap" class="block text-sm font-medium text-gray-700">Nama Lengkap</label>
                                    <input type="text" name="nama_lengkap" id="nama_lengkap" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-blue-500 sm:text-sm" required>
                                </div>
                                <div>
                                    <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                                    <input type="password" name="password" id="password" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-blue-500 sm:text-sm" required>
                                    <p class="mt-1 text-xs text-gray-500" id="password_hint">Wajib diisi untuk user baru</p>
                                </div>
                                <div>
                                    <label for="role" class="block text-sm font-medium text-gray-700">Role</label>
                                    <select id="role" name="role" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-blue-500 sm:text-sm" required>
                                        <option value="user">User (Ketua Ekskul)</option>
                                        <option value="admin">Admin</option>
                                    </select>
                                </div>
                                <div>
                                    <label for="ekskul" class="block text-sm font-medium text-gray-700">Ekstrakurikuler</label>
                                    <input type="text" name="ekskul" id="ekskul" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-blue-500 sm:text-sm" required>
                                    <p class="mt-1 text-xs text-gray-500">Isi dengan "-" jika user adalah admin</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6">
                    <button type="submit" class="inline-flex w-full justify-center rounded-md bg-blue-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-500 sm:ml-3 sm:w-auto">
                        Simpan
                    </button>
                    <button type="button" class="mt-3 inline-flex w-full justify-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 sm:mt-0 sm:w-auto" onclick="closeModal()">
                        Batal
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div id="deleteModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex min-h-screen items-end justify-center px-4 pb-20 pt-4 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
        <span class="hidden sm:inline-block sm:h-screen sm:align-middle" aria-hidden="true">&#8203;</span>
        <div class="inline-block transform overflow-hidden rounded-lg bg-white text-left align-bottom shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg sm:align-middle">
            <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                <div class="sm:flex sm:items-start">
                    <div class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                        <i class="fas fa-exclamation-triangle text-red-600"></i>
                    </div>
                    <div class="mt-3 text-center sm:ml-4 sm:mt-0 sm:text-left">
                        <h3 class="text-lg font-medium leading-6 text-gray-900" id="modal-title">
                            Hapus User
                        </h3>
                        <div class="mt-2">
                            <p class="text-sm text-gray-500" id="delete-message">
                                Apakah Anda yakin ingin menghapus user ini? Tindakan ini tidak dapat dibatalkan.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6">
                <form id="deleteForm" method="POST" action="">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="inline-flex w-full justify-center rounded-md bg-red-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-red-500 sm:ml-3 sm:w-auto">
                        Hapus
                    </button>
                </form>
                <button type="button" class="mt-3 inline-flex w-full justify-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 sm:mt-0 sm:w-auto" onclick="closeDeleteModal()">
                    Batal
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function openModal() {
    // Reset form for new user
    document.getElementById('userForm').reset();
    document.getElementById('userForm').action = "{{ route('admin.users.store') }}";
    document.getElementById('method_field').innerHTML = '';
    document.getElementById('modal-title').textContent = 'Tambah User Baru';
    document.getElementById('password').required = true;
    document.getElementById('password_hint').textContent = 'Wajib diisi untuk user baru';
    document.getElementById('userModal').classList.remove('hidden');
}

function closeModal() {
    document.getElementById('userModal').classList.add('hidden');
}

function openEditModal(userId, username, namaLengkap, ekskul, role) {
    // Set form for edit
    document.getElementById('userForm').reset();
    document.getElementById('userForm').action = "/admin/users/" + userId;
    document.getElementById('method_field').innerHTML = '<input type="hidden" name="_method" value="PUT">';
    document.getElementById('modal-title').textContent = 'Edit User';
    document.getElementById('username').value = username;
    document.getElementById('nama_lengkap').value = namaLengkap;
    document.getElementById('ekskul').value = ekskul;
    document.getElementById('role').value = role;
    document.getElementById('password').required = false;
    document.getElementById('password_hint').textContent = 'Kosongkan jika tidak ingin mengubah password';
    document.getElementById('userModal').classList.remove('hidden');
}

function openDeleteModal(userId, namaLengkap) {
    document.getElementById('deleteForm').action = "/admin/users/" + userId;
    document.getElementById('delete-message').textContent = 'Apakah Anda yakin ingin menghapus user "' + namaLengkap + '"? Tindakan ini tidak dapat dibatalkan.';
    document.getElementById('deleteModal').classList.remove('hidden');
}

function closeDeleteModal() {
    document.getElementById('deleteModal').classList.add('hidden');
}
</script>
@endsection 
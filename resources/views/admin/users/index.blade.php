@extends('layouts.app')

@section('content')
<div class="p-8">
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-2xl font-bold text-white">Kelola User</h1>
            <p class="text-gray-400">Manajemen akun pengguna ekstrakurikuler</p>
        </div>
        <a href="{{ route('admin.users.create') }}" class="btn-gradient">
            <i class="fas fa-plus mr-2"></i>Tambah User
        </a>
    </div>

    @if(session('success'))
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
        {{ session('success') }}
    </div>
    @endif

    @if(session('error'))
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
        {{ session('error') }}
    </div>
    @endif

    {{-- User List --}}
    <div class="bg-white/10 backdrop-blur-md border border-white/20 rounded-lg p-6">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="text-left text-gray-400 border-b border-white/20">
                        <th class="pb-3">Username</th>
                        <th class="pb-3">Nama Lengkap</th>
                        <th class="pb-3">Ekstrakurikuler</th>
                        <th class="pb-3">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/20">
                    @foreach($users as $user)
                    <tr class="text-white">
                        <td class="py-3">{{ $user->username }}</td>
                        <td class="py-3">{{ $user->nama_lengkap }}</td>
                        <td class="py-3">{{ $user->ekskul }}</td>
                        <td class="py-3">
                            <div class="flex gap-2">
                                <a href="{{ route('admin.users.edit', $user) }}" class="text-blue-400 hover:text-blue-300">
                                    Edit
                                </a>
                                <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="inline" onsubmit="return confirm('Yakin ingin menghapus user ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-400 hover:text-red-300">
                                        Hapus
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection 
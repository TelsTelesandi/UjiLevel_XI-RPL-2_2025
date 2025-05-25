@extends('layouts.app')

@section('content')
<div class="container mx-auto p-8">
    <h1 class="text-2xl font-bold mb-4">Dashboard User</h1>
    <p>Selamat datang, <span class="font-semibold">{{ auth()->user()->name }}</span>! Anda login sebagai <span class="text-green-600">user</span>.</p>
</div>
@endsection 
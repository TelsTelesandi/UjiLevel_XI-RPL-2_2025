@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <!-- Sidebar User -->
        <nav id="sidebar" class="col-md-3 col-lg-2 d-md-block bg-primary sidebar collapse">
            <div class="sidebar-sticky pt-3">
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a class="nav-link active text-white" href="{{ route('user.dashboard') }}">
                            <i class="bi bi-house-door mr-2"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white" href="{{ route('user.events.index') }}">
                            <i class="bi bi-calendar-event mr-2"></i> Pengajuan Saya
                        </a>
                    </li>
                </ul>
            </div>
        </nav>

        <!-- Main Content -->
        <main role="main" class="col-md-9 ml-sm-auto col-lg-10 px-md-4">
            @include('layouts.alerts')
            @yield('user-content')
        </main>
    </div>
</div>
@endsection
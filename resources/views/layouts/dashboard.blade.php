@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
        <div class="col-md-3 col-lg-2 d-md-block bg-light sidebar collapse">
            <div class="position-sticky pt-3">
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                            <i class="bi bi-speedometer2 me-2"></i>
                            Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('campaigns*') ? 'active' : '' }}" href="{{ route('campaigns.index') }}">
                            <i class="bi bi-envelope me-2"></i>
                            Campaigns
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('templates*') || request()->is('template-builder*') ? 'active' : '' }}" href="{{ route('templates.index') }}">
                            <i class="bi bi-file-earmark-richtext me-2"></i>
                            Email Templates
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('leads*') ? 'active' : '' }}" href="{{ route('leads.index') }}">
                            <i class="bi bi-people me-2"></i>
                            Leads
                        </a>
                    </li>
                </ul>

                <h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-1 text-muted">
                    <span>Tools</span>
                </h6>
                <ul class="nav flex-column mb-2">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('templates.builder') }}">
                            <i class="bi bi-brush me-2"></i>
                            Template Builder
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('leads.import.form') }}">
                            <i class="bi bi-upload me-2"></i>
                            Import Leads
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('leads.export') }}">
                            <i class="bi bi-download me-2"></i>
                            Export Leads
                        </a>
                    </li>
                </ul>
            </div>
        </div>

        <!-- Main content -->
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">@yield('title', 'Dashboard')</h1>
                <div class="btn-toolbar mb-2 mb-md-0">
                    @yield('actions')
                </div>
            </div>

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @yield('dashboard-content')
        </main>
    </div>
</div>
@endsection

@section('styles')
<style>
    .sidebar {
        position: fixed;
        top: 0;
        bottom: 0;
        left: 0;
        z-index: 100;
        padding: 48px 0 0;
        box-shadow: inset -1px 0 0 rgba(0, 0, 0, .1);
    }

    .sidebar .nav-link {
        font-weight: 500;
        color: #333;
    }

    .sidebar .nav-link.active {
        color: #6e48aa;
    }

    .sidebar-heading {
        font-size: .75rem;
        text-transform: uppercase;
    }

    .navbar-brand {
        padding-top: .75rem;
        padding-bottom: .75rem;
        font-size: 1rem;
        background-color: rgba(0, 0, 0, .25);
        box-shadow: inset -1px 0 0 rgba(0, 0, 0, .25);
    }
</style>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Ensure Bootstrap components are initialized
    document.addEventListener('DOMContentLoaded', function() {
        console.log('Bootstrap JS loaded successfully.');
    });
</script>
@endsection
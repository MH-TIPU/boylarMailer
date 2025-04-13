<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'BoylarMailer') }} - Email Marketing Platform</title>
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">
    <!-- Scripts -->
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
    <!-- Custom Styles -->
    <style>
        body {
            font-family: 'Nunito', sans-serif;
        }
        .hero-section {
            padding: 100px 0;
            background: linear-gradient(135deg, #4a69bb 0%, #6e48aa 100%);
            color: white;
        }
        .feature-card {
            transition: transform 0.3s ease;
            margin-bottom: 30px;
            border-radius: 10px;
            overflow: hidden;
            border: none;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        .feature-card:hover {
            transform: translateY(-10px);
        }
        .feature-icon {
            font-size: 48px;
            margin-bottom: 15px;
            color: #6e48aa;
        }
        .cta-section {
            background-color: #f8f9fa;
            padding: 80px 0;
        }
        .btn-primary {
            background-color: #6e48aa;
            border-color: #6e48aa;
        }
        .btn-primary:hover {
            background-color: #5b3c8b;
            border-color: #5b3c8b;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm">
        <div class="container">
            <a class="navbar-brand" href="{{ url('/') }}">
                {{ config('app.name', 'BoylarMailer') }}
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <!-- Left Side Of Navbar -->
                <ul class="navbar-nav me-auto">
                </ul>

                <!-- Right Side Of Navbar -->
                <ul class="navbar-nav ms-auto">
                    <!-- Authentication Links -->
                    @guest
                        @if (Route::has('login'))
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
                            </li>
                        @endif

                        @if (Route::has('register'))
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('register') }}">{{ __('Register') }}</a>
                            </li>
                        @endif
                    @else
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('dashboard') }}">Dashboard</a>
                        </li>
                        <li class="nav-item dropdown">
                            <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                {{ Auth::user()->name }}
                            </a>

                            <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                <a class="dropdown-item" href="{{ route('logout') }}"
                                   onclick="event.preventDefault();
                                                 document.getElementById('logout-form').submit();">
                                    {{ __('Logout') }}
                                </a>

                                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                    @csrf
                                </form>
                            </div>
                        </li>
                    @endguest
                </ul>
            </div>
        </div>
    </nav>

    <main>
        <!-- Hero Section -->
        <section class="hero-section">
            <div class="container text-center">
                <h1 class="display-4 fw-bold mb-4">Powerful Email Marketing Made Simple</h1>
                <p class="lead mb-5">Create, send, and track beautiful email campaigns with ease. Drive engagement and grow your business.</p>
                <div class="d-flex justify-content-center">
                    @guest
                        <a href="{{ route('register') }}" class="btn btn-light btn-lg me-3">Get Started Free</a>
                        <a href="{{ route('login') }}" class="btn btn-outline-light btn-lg">Log In</a>
                    @else
                        <a href="{{ route('dashboard') }}" class="btn btn-light btn-lg">Go to Dashboard</a>
                    @endguest
                </div>
            </div>
        </section>

        <!-- Features Section -->
        <section class="py-5">
            <div class="container">
                <h2 class="text-center mb-5">Powerful Features for Your Email Marketing</h2>
                <div class="row">
                    <div class="col-md-4">
                        <div class="card feature-card h-100">
                            <div class="card-body text-center p-4">
                                <div class="feature-icon">📧</div>
                                <h4 class="card-title">Visual Email Builder</h4>
                                <p class="card-text">Create stunning emails with our drag-and-drop editor. No coding skills required.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card feature-card h-100">
                            <div class="card-body text-center p-4">
                                <div class="feature-icon">📊</div>
                                <h4 class="card-title">Lead Management</h4>
                                <p class="card-text">Easily import, manage, and segment your contacts for targeted email campaigns.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card feature-card h-100">
                            <div class="card-body text-center p-4">
                                <div class="feature-icon">📈</div>
                                <h4 class="card-title">Analytics & Tracking</h4>
                                <p class="card-text">Monitor opens, clicks, and engagement to optimize your email campaigns.</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row mt-4">
                    <div class="col-md-4">
                        <div class="card feature-card h-100">
                            <div class="card-body text-center p-4">
                                <div class="feature-icon">🗓️</div>
                                <h4 class="card-title">Email Scheduler</h4>
                                <p class="card-text">Schedule your emails to be sent at the perfect time for maximum engagement.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card feature-card h-100">
                            <div class="card-body text-center p-4">
                                <div class="feature-icon">📝</div>
                                <h4 class="card-title">Reusable Templates</h4>
                                <p class="card-text">Save and reuse your best performing email templates for future campaigns.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card feature-card h-100">
                            <div class="card-body text-center p-4">
                                <div class="feature-icon">🔄</div>
                                <h4 class="card-title">Dynamic Content</h4>
                                <p class="card-text">Personalize your emails with dynamic content based on user data.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- CTA Section -->
        <section class="cta-section">
            <div class="container text-center">
                <h2 class="mb-4">Ready to Grow Your Business with Email Marketing?</h2>
                <p class="lead mb-5">Join thousands of businesses that use BoylarMailer to engage with their audience.</p>
                @guest
                    <a href="{{ route('register') }}" class="btn btn-primary btn-lg">Start Your Free Account Today</a>
                @else
                    <a href="{{ route('dashboard') }}" class="btn btn-primary btn-lg">Go to Dashboard</a>
                @endguest
            </div>
        </section>
    </main>

    <footer class="bg-dark text-white py-4">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <h5>{{ config('app.name', 'BoylarMailer') }}</h5>
                    <p>Powerful email marketing platform for businesses of all sizes.</p>
                </div>
                <div class="col-md-3">
                    <h5>Quick Links</h5>
                    <ul class="list-unstyled">
                        <li><a href="#" class="text-white">Features</a></li>
                        <li><a href="#" class="text-white">Pricing</a></li>
                        <li><a href="#" class="text-white">Documentation</a></li>
                    </ul>
                </div>
                <div class="col-md-3">
                    <h5>Legal</h5>
                    <ul class="list-unstyled">
                        <li><a href="#" class="text-white">Privacy Policy</a></li>
                        <li><a href="#" class="text-white">Terms of Service</a></li>
                    </ul>
                </div>
            </div>
            <hr>
            <div class="text-center">
                <p>&copy; {{ date('Y') }} {{ config('app.name', 'BoylarMailer') }}. All rights reserved.</p>
            </div>
        </div>
    </footer>
</body>
</html>

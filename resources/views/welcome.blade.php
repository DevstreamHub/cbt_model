@php
    use App\Models\Setting;
    $settings = Setting::first();
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>{{ $settings->platform_name ?? 'Platform Name' }}</title>

    @if($settings && $settings->logo)
        <link rel="icon" type="image/png" href="{{ asset('storage/' . $settings->logo) }}">
    @endif
    
    <meta name="robots" content="noindex,follow"/>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href="https://fonts.googleapis.com/css2?family=Roboto&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('vendors/core/core/base/libraries/font-awesome/css/fontawesome.min.css') }}">
    <link rel="stylesheet" href="{{ asset('vendors/core/core/base/css/core.css') }}">
    <link rel="stylesheet" href="{{ asset('vendors/core/core/acl/css/login.css') }}">

    <style>
    @media (max-width: 768px) {
    .login-sidebar {
        width: 100%;
        padding: 15px;
        overflow-y: auto;
        max-height: none;
    }

    .login-container {
        padding: 10px;
    }

    .login-button {
        font-size: 16px;
        padding: 10px;
    }

    .tab-content {
        padding-bottom: 80px; /* Ensure button at bottom is visible */
    }

    form button.btn-block {
        margin-bottom: 20px; /* Ensure spacing below */
    }
}

    .welcome-section {
    margin-top: 60px;
    margin-bottom: 50px;
    padding: 30px 20px;
    background: rgba(0, 0, 0, 0.5);
    border-radius: 10px;
    text-align: center;
    position: relative;
    z-index: 2;
}
body.login {
    background: url('{{ asset('vendors/img/dark.jpg') }}') no-repeat center center fixed;
    background-size: cover;
}

.welcome-section h2 {
    font-size: 28px;
    font-weight: 700;
}

.welcome-section p {
    font-size: 16px;
    margin-bottom: 20px;
    color: #f8f9fa;
}

@media (max-width: 768px) {
    .welcome-section {
        margin-top: 30px;
        padding: 20px 15px;
    }
}

        .instruction-card {
            background: rgba(255, 255, 255, 0.85);
            padding: 20px;
            border-radius: 10px;
            margin-top: 20px;
            box-shadow: 0 0 10px rgba(0,0,0,0.15);
        }
        .nav-tabs .nav-link.active {
            background-color: #0073aa;
            color: #fff !important;
        }
        .nav-tabs .nav-link {
            color: #0073aa;
        }
        .logo-title-container {
            padding: 20px;
            max-width: 65%;
            overflow-wrap: break-word;
        }
        .login-sidebar {
            max-height: 100vh;
            overflow-y: auto;
            padding-bottom: 20px;
        }
        
        .login-container {
            padding: 20px;
        }
        @media (max-width: 768px) {
    .login-sidebar {
        padding: 10px;
    }
}

    </style>
</head>
<body class="login">

    <div class="container-fluid">
        <div class="row">
            <div class="hidden-xs col-sm-7 col-md-8">
    <div class="row justify-content-center">
        <div class="col-md-10">

                        <div class="logo-title-container">
                            <div class="welcome-section text-white">
                                <h2 class="font-weight-bold">Welcome to {{ $settings->platform_name ?? 'Our College' }} Computer Base Test -  CBT Model</h2>
                                <p class="lead">Get to study the instruction below before proceeding usage.</p>
                            </div>



                            <div class="copy animated fadeIn">
                                <h1>Tictech CMS</h1>
                                <p>Copyright 2025 © Tictech CMS. Version: <span>1.0.0</span></p>
                            </div>
                        </div> <!-- .logo-title-container -->
                    </div>
                </div>
            </div>

            <div class="col-xs-12 col-sm-5 col-md-4 login-sidebar">
                <br>
                <center>
                    <div class="mb-2">
                        <a href="#" class="brand-link">
                        <img src="{{ asset('vendors/img/logo.jpg') }}" alt="Site Logo" width="100">
                        </a> 
                    </div>
                </center>

                <h5><center><b>{{ $settings->platform_name ?? 'College Name' }}</b></center></h5>
                <small><center><b><i>"{{ $settings->motto ?? 'College Motto' }}"</i></b></center></small>

                <div class="login-container">
                    <ul class="nav nav-tabs">
                        <li class="nav-item">
                            <a class="nav-link active" id="login-tab" data-toggle="tab" href="#loginTab" role="tab">Login</a>
                        </li>
                    </ul>

                    <div class="tab-content mt-3">
                        <!-- Login Form -->
                        <div class="tab-pane fade show active">
                            <p>Sign In Below:</p>
                            <form method="POST" action="{{ route('login') }}">
                                @csrf
                                <div class="form-group">
                                    <label for="email">{{ __('Email Address') }}</label>
                                    <input class="form-control" id="email" type="email" name="email" required>
                                    @error('email')<span class="invalid-feedback"><strong>{{ $message }}</strong></span>@enderror
                                </div>

                                <div class="form-group">
                                    <label for="password">{{ __('Password') }}</label>
                                    <input class="form-control" type="password" name="password" placeholder="Password" required>
                                    @error('password')<span class="invalid-feedback"><strong>{{ $message }}</strong></span>@enderror
                                </div>

                                <div>
                                    <label>
                                        <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}> {{ __('Remember Me') }}
                                    </label>
                                </div>

                                <br>
                                <button type="submit" class="btn btn-block login-button">{{ __('Login') }}</button><br>

                                <p class="mt-6">
                                    @if (Route::has('password.request'))
                                        <a href="{{ route('admin.password.request') }}">{{ __('Forgot Your Password?') }}</a>
                                    @endif
                                </p>
                            </form>
                        </div>

                    </div>
                </div> <!-- .login-container -->
            </div> <!-- .login-sidebar -->
        </div>
    </div>

    <script src="{{ asset('vendors/core/core/base/js/core.js') }}"></script><!-- Scripts -->
    
    <script src="{{ asset('js/jquery-3.6.0.min.js') }}"></script>
    
    <script src="{{ asset('js/bootstrap.bundle.min.4.6.0.js') }}"></script>
    <script>
    $('#signup-tab').on('shown.bs.tab', function () {
        document.querySelector('.login-sidebar').scrollTo({ top: 0, behavior: 'smooth' });
    });
</script>



</body>
</html>

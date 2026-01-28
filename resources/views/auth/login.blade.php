<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login SIM-PROMOSI</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            position: relative;
            overflow: hidden;
        }

        /* Background image */
        body::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-image: url('{{ asset("assets/bg.jpg") }}');
            background-size: cover;
            background-position: center;
            opacity: 0.7;
            z-index: 1;
        }

        /* Overlay */
        body::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(255, 255, 255, 0.85);
            z-index: 2;
        }

        .container {
            position: relative;
            z-index: 3;
            width: 100%;
            max-width: 450px;
            padding: 40px;
        }

        .login-card {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 15px;
            padding: 50px 40px;
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.15);
            text-align: center;
        }

        .login-header {
            margin-bottom: 40px;
        }

        .logo {
            max-width: 120px;
            margin: 0 auto 20px;
        }

        .logo img {
            width: 100%;
            height: auto;
        }

        .login-title {
            font-size: 24px;
            font-weight: 600;
            color: #333;
            margin-bottom: 5px;
        }

        .login-subtitle {
            font-size: 14px;
            color: #999;
        }

        .form-group {
            margin-bottom: 20px;
            text-align: left;
        }

        .form-group label {
            display: block;
            font-size: 12px;
            font-weight: 600;
            color: #666;
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .input-wrapper {
            position: relative;
            display: flex;
            align-items: center;
        }

        .input-icon {
            position: absolute;
            left: 15px;
            font-size: 18px;
            color: #999;
            z-index: 1;
        }

        .form-group input {
            width: 100%;
            padding: 12px 15px 12px 45px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 14px;
            transition: all 0.3s ease;
            background-color: #f8f8f8;
        }

        .form-group input:focus {
            outline: none;
            border-color: #1abc9c;
            background-color: #fff;
            box-shadow: 0 0 0 3px rgba(26, 188, 156, 0.1);
        }

        .form-group input::placeholder {
            color: #999;
        }

        .error-message {
            color: #e74c3c;
            font-size: 12px;
            margin-top: 5px;
            display: block;
        }

        .alert {
            padding: 12px 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 13px;
            line-height: 1.5;
        }

        .alert-danger {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .login-btn {
            width: 100%;
            padding: 12px;
            background: linear-gradient(135deg, #1abc9c 0%, #16a085 100%);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-top: 15px;
        }

        .login-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(26, 188, 156, 0.3);
        }

        .login-btn:active {
            transform: translateY(0);
        }

        .login-footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e0e0e0;
            font-size: 12px;
            color: #999;
        }

        .login-footer p {
            margin: 8px 0;
        }

        .copyright {
            font-size: 11px;
            color: #bbb;
            margin-top: 10px;
        }

        /* Responsive */
        @media (max-width: 600px) {
            .container {
                padding: 20px;
            }

            .login-card {
                padding: 30px 20px;
            }

            .login-title {
                font-size: 20px;
            }

            .login-btn {
                padding: 10px;
                font-size: 14px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="login-card">
            <div class="login-header">
                <div class="logo">
                    <img src="{{ asset('assets/logo.png') }}" alt="SIM-PROMOSI Logo">
                </div>
                <h1 class="login-title">Login SIM-PROMOSI</h1>
                <p class="login-subtitle">Sistem Informasi Manajemen Promosi</p>
            </div>

            @if ($errors->any())
                <div class="alert alert-danger">
                    @foreach ($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                </div>
            @endif

            @if (session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            <form action="{{ route('login.handle') }}" method="POST">
                @csrf

                <div class="form-group">
                    <label for="username">Username</label>
                    <div class="input-wrapper">
                        <i class="bi bi-person input-icon"></i>
                        <input
                            type="text"
                            id="username"
                            name="username"
                            placeholder="Masukkan username"
                            value="{{ old('username') }}"
                            required
                            autofocus
                        >
                    </div>
                    @error('username')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <div class="input-wrapper">
                        <i class="bi bi-lock input-icon"></i>
                        <input
                            type="password"
                            id="password"
                            name="password"
                            placeholder="Masukkan password"
                            required
                        >
                    </div>
                    @error('password')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <button type="submit" class="login-btn">
                    <i class="bi bi-check-lg"></i> Login
                </button>
            </form>

            <div class="login-footer">
                <p><strong>Demo Account:</strong></p>
                <p>Username: <strong>admin</strong></p>
                <p>Password: <strong>admin123</strong></p>
                <div class="copyright">
                    © 2018. Universitas Muhammadiyah Yogyakarta
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
</body>
</html>

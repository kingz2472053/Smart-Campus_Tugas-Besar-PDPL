<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'SmartCampus') }} — @yield('title', 'Login')</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

    <style>
        * { font-family: 'Inter', sans-serif; }

        body {
            min-height: 100vh;
            background: linear-gradient(135deg, #1E1B4B 0%, #312E81 40%, #4F46E5 100%);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .auth-card {
            background: #fff;
            border-radius: 1rem;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            width: 100%;
            max-width: 420px;
            padding: 2.5rem;
        }

        .auth-brand {
            text-align: center;
            margin-bottom: 2rem;
        }

        .auth-brand-icon {
            width: 64px;
            height: 64px;
            background: linear-gradient(135deg, #4F46E5, #7C3AED);
            border-radius: 1rem;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-size: 1.75rem;
            margin-bottom: 1rem;
        }

        .auth-brand h2 {
            font-weight: 700;
            color: #1E293B;
            margin-bottom: 0.25rem;
        }

        .auth-brand p {
            color: #64748B;
            font-size: 0.875rem;
            margin: 0;
        }

        .form-control {
            border-radius: 0.5rem;
            padding: 0.65rem 0.85rem;
            border: 1.5px solid #E2E8F0;
            font-size: 0.9rem;
        }

        .form-control:focus {
            border-color: #4F46E5;
            box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.15);
        }

        .form-label {
            font-weight: 500;
            font-size: 0.85rem;
            color: #334155;
        }

        .btn-primary {
            background: linear-gradient(135deg, #4F46E5, #7C3AED);
            border: none;
            border-radius: 0.5rem;
            padding: 0.65rem;
            font-weight: 600;
            font-size: 0.95rem;
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .btn-primary:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(79, 70, 229, 0.4);
            background: linear-gradient(135deg, #4338CA, #6D28D9);
        }

        .input-group-text {
            background: #F8FAFC;
            border: 1.5px solid #E2E8F0;
            border-right: none;
            color: #64748B;
        }

        .input-group .form-control {
            border-left: none;
        }
    </style>
</head>
<body>
    <div class="auth-card">
        @yield('content')
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

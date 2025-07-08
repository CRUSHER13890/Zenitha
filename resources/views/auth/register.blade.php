<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <title>Register | Sistem</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            background-color: #f5f5f5;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }
        
        .register-card {
            background: white;
            padding: 2rem;
            border-radius: 12px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 320px;
        }
        
        .form-group {
            margin-bottom: 1rem;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            color: #333;
            font-size: 0.9rem;
        }
        
        .form-control {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #ddd;
            border-radius: 6px;
            box-sizing: border-box;
            font-size: 1rem;
        }
        
        .form-control:focus {
            outline: none;
            border-color: #FFB800;
        }
        
        .btn {
            width: 100%;
            padding: 0.75rem;
            background: #FFB800;
            border: none;
            border-radius: 6px;
            color: #000;
            font-size: 1rem;
            font-weight: 500;
            cursor: pointer;
            margin-top: 1rem;
        }
        
        .btn:hover {
            background: #FFA500;
        }
        
        .login-link {
            display: block;
            text-align: center;
            margin-top: 1rem;
            color: #666;
            text-decoration: none;
            font-size: 0.9rem;
        }
        
        .login-link:hover {
            color: #FFB800;
        }
        
        .error-message {
            color: #dc3545;
            font-size: 0.85rem;
            margin-top: 0.25rem;
        }
    </style>
</head>

<body>
    <div class="register-card">
        <form method="POST" action="{{ route('register') }}">
            @csrf

            <div class="form-group">
                <label for="name">{{ __('Name') }}</label>
                <input 
                    type="text" 
                    id="name" 
                    class="form-control" 
                    name="name"
                    value="{{ old('name') }}" 
                    required 
                    autofocus
                    autocomplete="name"
                    placeholder=""
                >
                <x-input-error :messages="$errors->get('name')" class="error-message" />
            </div>

            <div class="form-group">
                <label for="email">{{ __('Email') }}</label>
                <input 
                    type="email" 
                    id="email" 
                    class="form-control" 
                    name="email"
                    value="{{ old('email') }}" 
                    required
                    autocomplete="username"
                    placeholder=""
                >
                <x-input-error :messages="$errors->get('email')" class="error-message" />
            </div>

            <div class="form-group">
                <label for="password">{{ __('Password') }}</label>
                <input 
                    type="password" 
                    id="password" 
                    class="form-control" 
                    name="password"
                    required
                    autocomplete="new-password"
                    placeholder=""
                >
                <x-input-error :messages="$errors->get('password')" class="error-message" />
            </div>

            <div class="form-group">
                <label for="password_confirmation">{{ __('Confirm Password') }}</label>
                <input 
                    type="password" 
                    id="password_confirmation" 
                    class="form-control" 
                    name="password_confirmation"
                    required
                    autocomplete="new-password"
                    placeholder=""
                >
                <x-input-error :messages="$errors->get('password_confirmation')" class="error-message" />
            </div>

            <button type="submit" class="btn">{{ __('Register') }}</button>

            <a href="{{ route('login') }}" class="login-link">
                {{ __('Already registered?') }}
            </a>
        </form>
    </div>
</body>
</html>
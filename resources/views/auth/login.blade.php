<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <title>Log In | Sistem</title>
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
        
        .login-card {
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
        
        .forgot-password {
            display: block;
            text-align: left;
            margin-top: 1rem;
            color: #666;
            text-decoration: none;
            font-size: 0.9rem;
        }
        
        .forgot-password:hover {
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
    <div class="login-card">
        <form method="POST" action="{{ route('login') }}">
            @csrf

            <div class="form-group">
                <label for="email">Email</label>
                <input 
                    type="email" 
                    id="email" 
                    class="form-control" 
                    name="email"
                    value="{{ old('email') }}" 
                    required 
                    autofocus
                    placeholder=" "
                >
                @error('email')
                    <div class="error-message">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input 
                    type="password" 
                    id="password" 
                    class="form-control" 
                    name="password"
                    required
                    placeholder=" "
                >
                @error('password')
                    <div class="error-message">{{ $message }}</div>
                @enderror
            </div>

            <button type="submit" class="btn">Sign In</button>

            @if (Route::has('password.request'))
                <a href="{{ route('password.request') }}" class="forgot-password">
                    Forgot password?
                </a>
            @endif
        </form>
    </div>
</body>
</html>
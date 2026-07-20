<!DOCTYPE html>
<html>
<head>
    <title>Coffee Haven - Register</title>
    <style>
        * {
            margin:0;
            padding:0;
            box-sizing:border-box;
            font-family:'Segoe UI', sans-serif;
        }

        body {
            min-height:100vh;
            background:url('https://images.unsplash.com/photo-1495474472287-4d71bcdd2085?w=1920');
            background-size:cover;
            background-position:center;
            display:flex;
            justify-content:center;
            align-items:center;
        }

        body::before {
            content:'';
            position:fixed;
            top:0; left:0;
            width:100%; height:100%;
            background:linear-gradient(135deg, rgba(30,15,5,0.85) 0%, rgba(74,44,42,0.75) 100%);
            z-index:0;
        }

        .box {
            position:relative;
            z-index:1;
            background:rgba(255,255,255,0.97);
            padding:45px 40px;
            border-radius:20px;
            box-shadow:0 20px 60px rgba(0,0,0,0.4);
            width:420px;
        }

        .logo {
            text-align:center;
            font-size:45px;
            margin-bottom:10px;
            animation:float 3s ease-in-out infinite;
        }

        @keyframes float {
            0%, 100% { transform:translateY(0); }
            50%       { transform:translateY(-8px); }
        }

        h2 {
            color:#6f4e37;
            text-align:center;
            font-size:26px;
            font-weight:800;
            margin-bottom:5px;
        }

        .subtitle {
            text-align:center;
            color:#999;
            font-size:13px;
            margin-bottom:30px;
        }

        label {
            color:#4a2c2a;
            font-weight:600;
            font-size:13px;
            display:block;
            margin-bottom:5px;
        }

        input[type="text"],
        input[type="email"],
        input[type="password"] {
            width:100%;
            padding:13px 15px;
            margin-bottom:18px;
            border:2px solid #f0e0d0;
            border-radius:10px;
            font-size:14px;
            transition:border 0.3s;
            background:#fffaf5;
        }

        input[type="text"]:focus,
        input[type="email"]:focus,
        input[type="password"]:focus {
            outline:none;
            border-color:#c68e5b;
            background:white;
        }

        button[type="submit"] {
            width:100%;
            padding:14px;
            background:linear-gradient(135deg, #6f4e37, #c68e5b);
            color:white;
            border:none;
            border-radius:10px;
            font-size:16px;
            font-weight:700;
            cursor:pointer;
            transition:all 0.3s;
            box-shadow:0 5px 15px rgba(111,78,55,0.4);
        }

        button[type="submit"]:hover {
            transform:translateY(-2px);
            box-shadow:0 8px 20px rgba(111,78,55,0.5);
        }

        .error {
            color:#c0392b;
            font-size:12px;
            margin-top:-12px;
            margin-bottom:12px;
        }

        .divider {
            text-align:center;
            color:#ccc;
            margin:20px 0;
            font-size:13px;
            position:relative;
        }

        .divider::before,
        .divider::after {
            content:'';
            position:absolute;
            top:50%;
            width:40%;
            height:1px;
            background:#eee;
        }

        .divider::before { left:0; }
        .divider::after  { right:0; }

        .login-link {
            text-align:center;
            font-size:14px;
            color:#666;
        }

        .login-link a {
            color:#6f4e37;
            font-weight:700;
            text-decoration:none;
        }

        .login-link a:hover {
            text-decoration:underline;
        }

        .back-link {
            text-align:center;
            margin-top:15px;
        }

        .back-link a {
            color:#999;
            font-size:13px;
            text-decoration:none;
        }

        .back-link a:hover {
            color:#6f4e37;
        }
    </style>
</head>
<body>

<div class="box">
    <div class="logo">☕</div>
    <h2>Coffee Haven</h2>
    <p class="subtitle">Create your account</p>

    <form method="POST" action="{{ route('register') }}">
        @csrf

        <label>Full Name</label>
        <input
            type="text"
            name="name"
            value="{{ old('name') }}"
            required
            autofocus
            autocomplete="name"
            placeholder="Enter your full name"
        >
        @error('name')
            <div class="error">{{ $message }}</div>
        @enderror

        <label>Email Address</label>
        <input
            type="email"
            name="email"
            value="{{ old('email') }}"
            required
            autocomplete="email"
            placeholder="Enter your email"
        >
        @error('email')
            <div class="error">{{ $message }}</div>
        @enderror

        <label>Password</label>
        <input
            type="password"
            name="password"
            required
            autocomplete="new-password"
            placeholder="Create a password"
        >
        @error('password')
            <div class="error">{{ $message }}</div>
        @enderror

        <label>Confirm Password</label>
        <input
            type="password"
            name="password_confirmation"
            required
            autocomplete="new-password"
            placeholder="Confirm your password"
        >

        <button type="submit">Create Account ☕</button>
    </form>

    <div class="divider">or</div>

    <div class="login-link">
        Already have an account? <a href="/login">Login here</a>
    </div>

    <div class="back-link">
        <a href="/">← Back to Home</a>
    </div>
</div>

</body>
</html>
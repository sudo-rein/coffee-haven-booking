<!DOCTYPE html>
<html>
<head>
    <title>Coffee Haven</title>
    <style>
        * {
            margin:0;
            padding:0;
            box-sizing:border-box;
            font-family:'Segoe UI', sans-serif;
        }

        body {
            min-height:100vh;
            background:url('https://images.unsplash.com/photo-1509042239860-f550ce710b93');
            background-size:cover;
            background-position:center;
            background-attachment:fixed;
            display:flex;
            flex-direction:column;
        }

        /* Dark overlay */
        body::before {
            content:'';
            position:fixed;
            top:0; left:0;
            width:100%; height:100%;
            background:rgba(30, 15, 5, 0.65);
            z-index:0;
        }

        .hero {
            position:relative;
            z-index:1;
            flex:1;
            display:flex;
            flex-direction:column;
            justify-content:center;
            align-items:center;
            text-align:center;
            padding:40px 20px;
            min-height:100vh;
        }

        .badge {
            background:rgba(111, 78, 55, 0.8);
            color:#f5eee6;
            padding:6px 18px;
            border-radius:20px;
            font-size:13px;
            letter-spacing:2px;
            text-transform:uppercase;
            margin-bottom:20px;
            display:inline-block;
        }

        h1 {
            font-size:58px;
            font-weight:800;
            color:white;
            line-height:1.1;
            margin-bottom:15px;
            text-shadow:0 2px 10px rgba(0,0,0,0.5);
        }

        h1 span {
            color:#c68e5b;
        }

        .subtitle {
            font-size:18px;
            color:rgba(255,255,255,0.8);
            max-width:480px;
            line-height:1.6;
            margin-bottom:40px;
        }

        .buttons {
            display:flex;
            gap:15px;
            justify-content:center;
            flex-wrap:wrap;
            margin-bottom:60px;
        }

        .btn {
            padding:14px 35px;
            border-radius:50px;
            font-size:16px;
            font-weight:600;
            text-decoration:none;
            transition:all 0.3s;
            display:inline-block;
        }

        .btn-primary {
            background:#6f4e37;
            color:white;
            box-shadow:0 4px 15px rgba(111,78,55,0.5);
        }

        .btn-primary:hover {
            background:#5a3f2d;
            transform:translateY(-2px);
            box-shadow:0 6px 20px rgba(111,78,55,0.6);
        }

        .btn-secondary {
            background:rgba(255,255,255,0.15);
            color:white;
            border:2px solid rgba(255,255,255,0.5);
            backdrop-filter:blur(5px);
        }

        .btn-secondary:hover {
            background:rgba(255,255,255,0.25);
            transform:translateY(-2px);
        }

        /* Features */
        .features {
            display:flex;
            gap:20px;
            justify-content:center;
            flex-wrap:wrap;
        }

        .feature {
            background:rgba(255,255,255,0.1);
            backdrop-filter:blur(10px);
            border:1px solid rgba(255,255,255,0.2);
            border-radius:15px;
            padding:20px 25px;
            text-align:center;
            width:160px;
            color:white;
        }

        .feature-icon {
            font-size:32px;
            margin-bottom:10px;
        }

        .feature-title {
            font-size:14px;
            font-weight:600;
            margin-bottom:5px;
        }

        .feature-desc {
            font-size:12px;
            color:rgba(255,255,255,0.7);
        }

        /* Footer */
        .footer {
            position:relative;
            z-index:1;
            text-align:center;
            padding:20px;
            color:rgba(255,255,255,0.4);
            font-size:13px;
        }
    </style>
</head>
<body>

<div class="hero">

    <div class="badge">☕ Premium Coffee Experience</div>

    <h1>Welcome to<br><span>Coffee Haven</span></h1>

    <p class="subtitle">
        Reserve your favorite room and enjoy premium coffee,
        freshly baked pastries, and a warm cozy atmosphere.
    </p>

    <div class="buttons">
        <a href="/login"    class="btn btn-primary">Login</a>
        <a href="/register" class="btn btn-secondary">Create Account</a>
    </div>

    <div class="features">
        <div class="feature">
            <div class="feature-icon">🏠</div>
            <div class="feature-title">Cozy Rooms</div>
            <div class="feature-desc">Private spaces for every occasion</div>
        </div>
        <div class="feature">
            <div class="feature-icon">📅</div>
            <div class="feature-title">Easy Booking</div>
            <div class="feature-desc">Reserve in just a few clicks</div>
        </div>
        <div class="feature">
            <div class="feature-icon">☕</div>
            <div class="feature-title">Premium Coffee</div>
            <div class="feature-desc">Finest beans from around the world</div>
        </div>
        <div class="feature">
            <div class="feature-icon">🎂</div>
            <div class="feature-title">Fresh Pastries</div>
            <div class="feature-desc">Baked fresh every morning</div>
        </div>
    </div>

</div>

<div class="footer">
    © 2026 Coffee Haven. All rights reserved.
</div>

</body>
</html>
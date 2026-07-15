<!DOCTYPE html>
<html>
<head>
    <title>Travel Booking</title>

    <style>
        *{
            margin:0;
            padding:0;
            box-sizing:border-box;
            font-family:'Segoe UI',sans-serif;
        }

        body{
            background:#f5eee6;
        }

        .navbar{
            background:#4a2c2a;
            color:white;
            padding:20px;
            text-align:center;
            font-size:28px;
            font-weight:bold;
        }

        .container{
            max-width:800px;
            margin:40px auto;
            background:white;
            padding:30px;
            border-radius:15px;
            box-shadow:0 5px 15px rgba(0,0,0,0.1);
        }

        h2{
            color:#6f4e37;
            margin-bottom:20px;
        }

        input, select{
            width:100%;
            padding:12px;
            margin-top:5px;
            margin-bottom:15px;
            border:1px solid #ddd;
            border-radius:8px;
        }

        button{
            background:#6f4e37;
            color:white;
            padding:12px 25px;
            border:none;
            border-radius:8px;
            cursor:pointer;
        }

        button:hover{
            background:#5a3f2d;
        }

        .card{
            background:#fffaf5;
            padding:20px;
            border-left:5px solid #c68e5b;
            margin-bottom:20px;
            border-radius:10px;
        }

        .error{
            color:red;
            font-size:14px;
        }

        .success{
            color:green;
            margin-bottom:15px;
        }

        table{
            width:100%;
            border-collapse:collapse;
            margin-top:20px;
        }

        th{
            background:#6f4e37;
            color:white;
            padding:12px;
            text-align:left;
        }

        td{
            padding:12px;
            border-bottom:1px solid #ddd;
        }

        tr:hover{
            background:#fffaf5;
        }
    </style>
</head>
<body>

<div class="navbar">
    Travel Reservation
</div>

<div class="container">

    @if(session('success'))
        <div class="success">
            {{ session('success') }}
        </div>
    @endif

    @yield('content')

</div>

</body>
</html> 
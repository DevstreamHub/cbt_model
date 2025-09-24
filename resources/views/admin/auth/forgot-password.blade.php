<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Reset Admin Password</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
        }

        .container {
            max-width: 400px;
            margin: 80px auto;
            background-color: #fff;
            padding: 25px 30px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }

        h4 {
            text-align: center;
            margin-bottom: 20px;
            color: #333;
        }

        label {
            display: block;
            margin-bottom: 6px;
            font-weight: bold;
            color: #333;
        }

        input[type="email"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        .btn {
            display: block;
            width: 100%;
            padding: 10px;
            background-color: #007bff;
            color: #fff;
            font-weight: bold;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .btn:hover {
            background-color: #0056b3;
        }

        .alert {
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 4px;
            font-size: 14px;
            text-align: center;
        }

        .alert-success {
            background-color: #d4edda;
            color: #155724;
        }

        .alert-danger {
            background-color: #f8d7da;
            color: #721c24;
        }
    </style>
</head>
<body>

    <div class="container">
        <h4>🔐 Reset Admin Password</h4>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger">{{ implode(', ', $errors->all()) }}</div>
        @endif

        <form method="POST" action="{{ route('admin.password.reset') }}">
            @csrf
            <div>
                <label for="email">Admin Email</label>
                <input type="email" name="email" id="email" placeholder="Enter admin email" required autofocus>
            </div>
            <button type="submit" class="btn">Reset to 12345</button>
        </form>
        <br>
        <a href="/"><center>Login</center></a>
    </div>

</body>
</html>

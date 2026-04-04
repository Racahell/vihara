<!DOCTYPE html>
<html>
<head>
    <title>Login Vihara</title>
    <style>
        body {
            font-family: Arial;
            background: #f2f2f2;
        }
        .box {
            width: 350px;
            margin: 100px auto;
            background: white;
            padding: 25px;
            border-radius: 10px;
        }
        input, button {
            width: 100%;
            padding: 10px;
            margin: 8px 0;
        }
        button {
            background: #3B5F86;
            color: white;
            border: none;
        }
    </style>
</head>
<body>

<form method="POST" action="/login">
    @csrf
    <input type="text" name="username" placeholder="Username" value="{{ old('username') }}" required>
    <input type="password" name="password" placeholder="Password" required>
    <button type="submit">Login</button>
</form>
@if($errors->any())
    <div style="color:red;">
        {{ $errors->first() }}
    </div>
@endif
</div>

</body>
</html>




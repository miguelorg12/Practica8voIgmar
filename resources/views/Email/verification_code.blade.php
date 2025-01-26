<!DOCTYPE html>
<html>

<head>
    <title>Codigo de Verificacion</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f9fa;
            color: #333;
            margin: 0;
            padding: 0;
        }

        .container {
            width: 100%;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }

        .card {
            background-color: #fff;
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 20px;
            margin-bottom: 20px;
        }

        h1,
        h2 {
            text-align: center;
        }
    </style>
</head>

<body>
    <div class="container">
        <h1>Tu codigo de verificacion es:</h1>
        <div class="card">
            <div class="card-body">
                <h2>{{ $verificationCode }}</h2>
            </div>
        </div>
    </div>
</body>

</html>
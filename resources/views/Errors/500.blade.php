{{-- filepath: c:\Users\migue\Desktop\PracticasIgmar\Practica_1\resources\views\Errors\500.blade.php --}}
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Error 500 - Algo salió mal</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            color: #333;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            text-align: center;
        }
        .container {
            max-width: 600px;
            padding: 20px;
        }
        h1 {
            font-size: 4rem;
            color: #ff6b6b;
        }
        p {
            font-size: 1.2rem;
            margin: 10px 0 20px;
        }
        img {
            max-width: 100%;
            height: 150px;
        }
        a {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 20px;
            background-color: #4caf50;
            color: #fff;
            text-decoration: none;
            border-radius: 5px;
            transition: background-color 0.3s;
        }
        a:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>
    <div class="container">
        <img src="https://png.pngtree.com/png-vector/20220617/ourmid/pngtree-symbol-error-warning-alert-icon-vector-design-png-image_5126968.png" alt="Robot descompuesto">
        <h1>Error 500</h1>
        <p>¡Ups! Algo salió mal en nuestro servidor.</p>
        <p>Estamos trabajando para solucionarlo. Por favor, intenta nuevamente más tarde.</p>
        <a href="{{ url('/') }}">Volver al inicio</a>
    </div>
</body>
</html>
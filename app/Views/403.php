<!-- app/Views/forbidden.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forbidden Access - 403</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f9fa;
            color: #333;
            text-align: center;
            padding: 50px;
        }
        h1 {
            font-size: 5em;
            color: #dc3545;
        }
        p {
            font-size: 1.5em;
            margin: 20px 0;
        }
        .button {
            padding: 10px 20px;
            font-size: 1.2em;
            color: white;
            background-color: #007bff;
            text-decoration: none;
            border-radius: 5px;
            transition: background-color 0.3s;
        }
        .button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <h1>403</h1>
    <p>Forbidden: You do not have permission to access this page.</p>
    <a href="/" class="button">Go Back to Home</a>
</body>
</html>

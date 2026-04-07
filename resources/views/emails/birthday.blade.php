<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Happy Birthday</title>
</head>
<body style="font-family: Arial, sans-serif;">
    <h2>🎉 Happy Birthday {{ $user->name }}!</h2>

    <p>
        Wishing you a wonderful year ahead filled with success,
        happiness, and good health 🎂
    </p>

    <p>
        Best wishes,<br>
        <strong>{{ config('app.name') }}</strong>
    </p>
</body>
</html>

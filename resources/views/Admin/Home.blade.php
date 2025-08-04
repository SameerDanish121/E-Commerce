<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    @vite('resources/js/admin_app.js')
    <script>
        window.laravel = {
            loginUrl: "{{ route('login') }}"
        };
    </script>
</head>

<body>
    <div id="admin-app"></div>
</body>

</html>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Customer Panel</title>
    <script src="https://js.stripe.com/v3/"></script>

    @vite('resources/js/customer_app.js')
    <script>
        window.laravel = {
            loginUrl: "{{ route('login') }}"
        };
    </script>
</head>

<body>
    <div id="customer-app"></div>
</body>

</html>
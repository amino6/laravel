<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">

</head>

<body class="antialiased">
    <h1>hi</h1>

    <script>
        function getCookie(name) {
            const value = `; ${document.cookie}`;
            const parts = value.split(`; ${name}=`);
            if (parts.length === 2) {
                return parts.pop().split(';').shift();
            }
        }

        function request(url, options) {
            // get cookie
            const csrfToken = getCookie('XSRF-TOKEN');
            return fetch(url, {
                headers: {
                    'content-type': 'application/json',
                    'accept': 'application/json',
                    'X-XSRF-TOKEN': decodeURIComponent(csrfToken),
                },
                credentials: 'include',
                ...options,
            })
        }

        function logout() {
            return request('/logout', {
                method: 'POST'
            });
        }

        function login() {
            return request('/login', {
                method: "POST",
                body: JSON.stringify({
                    email: 'hakmi2@gmail.com',
                    'password': '12345678'
                })
            })
        }
        fetch('/sanctum/csrf-cookie', {
                headers: {
                    'content-type': 'application/json',
                    'accept': 'application/json'
                },
                credentials: 'include'
            }).then(() => logout())
            .then(() => {
                return login();
            })
            .then(async () => {
                const res = await request('/api/users');
                const data = await res.json();
                console.log(data);
            })
    </script>
</body>

</html>

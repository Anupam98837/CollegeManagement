<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ragister Student</title>
    <link rel="stylesheet" href="{{ asset('css/Components/studentRegister.css') }}">
    <style>
        .step { display: none; }
        .step.active { display: block; }
        .navigation { display: flex; justify-content: space-between; }
    </style>
</head>
<body>
    <div class=" d-flex">
        <div>
            @include('users.principal.components.sidebar')
        </div>
        <div class="w-100 main-com">
            @include('users.principal.components.header')
            @include('modules.student.registerStudent')

        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script src="{{ asset('js/Components/registerStudent.js') }}"></script>

</body>
</html>

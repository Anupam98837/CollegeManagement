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
            @include('users.register.components.sidebar')
        </div>
        <div class="w-100 main-com">
            @include('users.register.components.header')
            <div class="admin_ragister_form">
                <div class="admin_ragister_student_form_con">
                    <div class="d-flex justify-content-between align-items-center gap-3 mb-4">
                        <p class="text-secondary text-14 mb-0">
                            Student <i class="fa-solid fa-angle-right"></i>
                            <span class="text-primary">Register</span>
                        </p>
                        @include('modules.student.registerBulkStudent')
                    </div>
                
                    @include('modules.student.registerStudent')
                </div>
                
              </div> 
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script src="{{ asset('js/Components/registerStudent.js') }}"></script>

</body>
</html>

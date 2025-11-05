<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <title>Manage Campus</title>
  <meta name="csrf-token" content="{{ csrf_token() }}">

</head>
<body>
  <div class="d-flex">
    <!-- Sidebar -->
    <div>
      @include('users.admin.components.sidebar')
    </div>

    <!-- Main Content -->
    <div class="w-100 main-com">
      @include('users.admin.components.header')
        <!-- Add Campus Form -->
        @include('modules.campus.manageCampus')

    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script src="{{ asset('js/Components/manageCampus.js') }}"></script>
</body>
</html>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <title>Attendance Report</title>
  <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body>
  <div class="d-flex">
    <!-- Sidebar -->
    <div>
      @include('users.faculty.components.sidebar')
    </div>

    <!-- Main Content -->
    <div class="w-100 main-com">
      @include('users.faculty.components.header')
        <!-- Add Campus Form -->
        @include('modules.report.attendanceReport')

    </div>
  </div>

</body>
</html>

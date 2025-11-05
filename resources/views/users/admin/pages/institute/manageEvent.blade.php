<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <title>Event Manage</title>
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
      <!-- Add module-->
        @include('modules.event.manageEvent')

    </div>
  </div>

</body>
</html>

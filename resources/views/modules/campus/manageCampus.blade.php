<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <title>Add Campus</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <!-- Font Awesome (for icons) -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" />
  <link href="https://unpkg.com/bootstrap-table@1.21.2/dist/bootstrap-table.min.css" rel="stylesheet"/>
<script src="https://unpkg.com/bootstrap-table@1.21.2/dist/bootstrap-table.min.js"></script>

</head>
<body>

      <div class="container mt-4">
        <!-- Add Campus Form -->
        <form id="addCampusForm" class="bg-white p-4 position-relative rounded d-none">
          <button type="button" class="btn btn-danger text-13 position-absolute top-0 end-0 m-3" onclick="hideAddCampusForm()">
            <i class="fa-solid fa-xmark"></i>
          </button>

          <p class="mb-4 text-secondary text-14">
            <i class="fa-solid fa-angle-right"></i>
            <span class="text-primary admin_add_campus_text">Add Campus</span>
          </p>

          <!-- CSRF Token -->
          @csrf

          <!-- Form Fields -->
          <div class="row g-3 align-items-end">
            <div class="col-md-5">
              <label for="campusName" class="form-label text-13">Campus Name <span class="text-danger">*</span></label>
              <input type="text" id="campusName" name="campus_name" class="form-control placeholder-14 text-13" placeholder="Enter Campus Name" required>
            </div>
            <div class="col-md-6">
              <label for="campusId" class="form-label text-13">Campus ID <span class="text-danger">*</span></label>
              <input type="text" id="campusId" name="campus_id" class="form-control placeholder-14 text-13" placeholder="Enter Campus ID" required>
            </div>
            <div class="col-md-1 text-center">
              <button type="submit" id="submitButton" class="btn btn-outline-secondary text-13">
                <span id="buttonText">Add</span>
                <span id="buttonSpinner" class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
              </button>
            </div>
          </div>
        </form>

        <!-- Edit Campus Form -->
        <form id="editCampusForm" class="bg-white p-4 rounded d-none">
          <p class="mb-4 text-secondary text-14">
            <i class="fa-solid fa-angle-right"></i>
            <span class="text-primary admin_add_campus_text">Edit Campus</span>
          </p>

          @csrf
          <input type="hidden" id="editCampusId" name="campus_id">
          <div class="row g-3 align-items-end">
            <div class="col-md-10">
              <label for="editCampusName" class="form-label text-13">Campus Name <span class="text-danger">*</span></label>
              <input type="text" id="editCampusName" name="campus_name" class="form-control placeholder-14 text-13" placeholder="Enter Campus Name" required>
            </div>
            <div class="col-md-2 text-center">
              <button type="submit" id="editSubmitButton" class="btn btn-outline-primary text-13">
                <span id="editButtonText"><i class="fa-regular fa-pen-to-square"></i> Update</span>
                <span id="editButtonSpinner" class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
              </button>
              <button type="button" onclick="cancelEdit()" class="btn btn-outline-danger text-13 ms-2">
                <i class="fa-solid fa-xmark"></i>
              </button>
            </div>
          </div>
        </form>

        <p class="mt-4 text-secondary text-14">
          <i class="fa-solid fa-angle-right"></i>
          <span class="text-primary">All Campus</span>
        </p>

        <!-- Campus Table and Search -->
        <div class="bg-white p-4 rounded">
          <div class="row mb-3 align-items-center justify-content-between">
            <div class="col-md-6 position-relative">
              <input type="text" id="searchCampus" class="form-control placeholder-14 text-13 ps-5" placeholder="Search by Campus Name or Campus ID">
              <i class="fa-solid fa-search position-absolute text-secondary" style="top: 50%; left: 15px; transform: translateY(-50%);"></i>
            </div>
            <div class="col-md-6 text-end">
              <button id="showAddCampusForm" class="btn btn-outline-primary text-13">
                <i class="fa-solid fa-plus"></i> Campus
              </button>
            </div>
          </div>
          
          <div class="table-responsive">
            <table class="table table-striped text-center">
              <thead>
                <tr>
                  <th class="text-13 text-secondary">Campus_Name</th>
                  <th class="text-13 text-secondary">Campus_ID</th>
                  <th class="text-13 text-secondary">Status</th>
                  <th class="text-13 text-secondary">Created_At</th>
                  <th class="text-13 text-secondary">Actions</th>
                </tr>
              </thead>
              <tbody id="campusTableBody">
                <tr>
                    <td colspan="100%" class="text-center">
                      <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                      </div>
                    </td>
                  </tr>
              </tbody>
            </table>
          </div>

          <!-- Pagination Container -->
          <div id="paginationContainer" class="mt-3 d-flex justify-content-center gap-2">
            <!-- Dynamic pagination buttons will be appended here -->
          </div>
        </div>
      </div>


  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script src="{{ asset('js/Components/manageCampus.js') }}"></script>
</body>
</html>

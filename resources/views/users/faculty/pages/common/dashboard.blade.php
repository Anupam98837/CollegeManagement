<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Principal Dashboard</title>
  <!-- Bootstrap CSS -->
  <link
    href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css"
    rel="stylesheet"
  />
  <!-- Font Awesome CSS -->
  <link
    href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css"
    rel="stylesheet"
  />
</head>
<body>
  <div class="d-flex">
    <div>
      @include('users.faculty.components.sidebar')
    </div>
    <div class="w-100 main-com">
      @include('users.faculty.components.header')
      
      <!-- Institution Details Card -->
      <div class="container mt-4">
        <div class="card text-center  border-0">
          <div class="card-body">
            <h5 class="card-title fs-3" id="instituteName">
              <i class="fa-solid fa-school me-2 text-primary"></i>
              <span class="text-secondary">Loading Institution...</span>
            </h5>
            <p class="card-text fs-4" id="instituteType">
              <i class="fa-solid fa-graduation-cap me-2"></i>
              Loading Type...
            </p>
          </div>
        </div>
      </div>
      
      <!-- Additional dashboard content can go here -->
    </div>
  </div>

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    // When the page loads, retrieve the institution details from sessionStorage and display them.
    document.addEventListener("DOMContentLoaded", function() {
      const instituteName = sessionStorage.getItem("institution_name") || "Institution Name Unavailable";
      const instituteType = sessionStorage.getItem("institution_type") || "Institution Type Unavailable";
      
      document.getElementById("instituteName").innerHTML = `
        <i class="fa-solid fa-school me-2 text-primary"></i>
        <span class="text-secondary">${instituteName}</span>
      `;
      document.getElementById("instituteType").innerHTML = `
        <i class="fa-solid fa-graduation-cap me-2"></i>
        ${instituteType}
      `;
    });
  </script>
</body>
</html>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Header with Sidebar</title>
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.10.5/font/bootstrap-icons.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
  <link rel="stylesheet" href="{{ asset('css/main.css') }}">
  <link rel="stylesheet" href="{{ asset('css/Users/Student/header.css') }}">
  <style>
    .main-com{
      background-color: #ECF1F9;
    }
  </style>
</head>
<body>

<!-- Header -->
<div class="user-header bg-white d-flex justify-content-between align-items-center px-3 py-2">
  <div>
    <button class="btn btn-outline-secondary" id="ah_toggleSidebar">
      <i class="bi bi-list"></i>
    </button>
    <button style="display: none;" class="btn btn-outline-secondary" id="ah_closeSidebar">
      <i class="bi bi-x"></i>
    </button>
  </div>

  <!-- Search Bar -->
  {{-- <div class="as-search-bar flex-grow-1 mx-3">
    <input type="text" placeholder="Search for anything...">
    <i class="bi bi-search"></i>
  </div> --}}

  <div class="d-flex align-items-center" id="userSection">
    <!-- Student data will be injected here -->
  </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
  document.addEventListener("DOMContentLoaded", function() {
    if (!sessionStorage.getItem("token")) {
      window.location.href = "/";
    }
    let Username = sessionStorage.getItem("student_name")
    // Sidebar toggle functionality
    const toggleSidebar = document.getElementById('ah_toggleSidebar');
    const closeSidebar = document.getElementById('ah_closeSidebar');
    const mainsidebar = document.querySelector('.as-slim-sidebar');
    const userSection = document.getElementById('userSection');
    

    toggleSidebar.addEventListener('click', () => {
      toggleSidebar.style.display = 'none';
      closeSidebar.style.display = 'block';
      if(mainsidebar) mainsidebar.style.display = 'block';
    });
    
    closeSidebar.addEventListener('click', () => {
      closeSidebar.style.display = 'none';
      toggleSidebar.style.display = 'block';
      if(mainsidebar) mainsidebar.style.display = 'none';
    });
    
    // Fetch student data from the API
    const email = sessionStorage.getItem("student_email");
    if (email) {
      fetch('/api/get-student-by-email', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
          'Authorization': sessionStorage.getItem('token')
        },
        body: JSON.stringify({ email: email })
      })
      .then(response => response.json())
      .then(data => {
        if (data.status === 'success') {
          const student = data.data;
          const studentName = student.name || 'Student';
          let photoUrl = "";
          if (student.student_photo) {
            // If the API returns only the filename, build the URL accordingly.
            photoUrl = `{{ asset('assets/student_documents/${student.student_photo}') }}`;
          } else {
            // Use the default profile picture if no photo is provided.
            photoUrl = "{{ asset('assets/web_assets/default-profile.jpg') }}";
          }
          userSection.innerHTML = `
            <div class="d-flex align-items-center">
              <a href="/student/profile" class="text-decoration-none text-dark">
                <img src="${photoUrl}" alt="Profile Picture" class="rounded-circle border border-primary p-1 me-2" style="width: 40px; height: 40px; object-fit: cover;">
              </a>
              <a href="/student/profile" class="text-decoration-none text-dark">
                <span>${studentName}</span>
              </a>
            </div>
          `;
        } else {
          // If student data isn't found, fallback to a signup button.
          userSection.innerHTML = `
            <div class="d-flex align-items-center">
              <a href="/student/profile" class="text-decoration-none text-dark">
                <img src="{{ asset('assets/web_assets/default-profile.jpg') }}" alt="Profile Picture" class="rounded-circle border border-primary p-1 me-2" style="width: 40px; height: 40px; object-fit: cover;">
              </a>
              <a href="/student/profile" class="text-decoration-none text-dark">
                <span>${Username}</span>
              </a>
            </div>
          `;
        }
      })
      .catch(error => {
        console.error('Error fetching student data:', error);
        userSection.innerHTML = `
          <div class="d-flex align-items-center">
              <a href="/student/profile" class="text-decoration-none text-dark">
                <img src="{{ asset('assets/web_assets/default-profile.jpg') }}" alt="Profile Picture" class="rounded-circle border border-primary p-1 me-2" style="width: 40px; height: 40px; object-fit: cover;">
              </a>
              <a href="/student/profile" class="text-decoration-none text-dark">
                <span>${Username}</span>
              </a>
            </div>
        `;
      });
    } else {
      // Fallback if no email is available in sessionStorage.
      userSection.innerHTML = `
        <div class="d-flex align-items-center">
              <a href="/student/profile" class="text-decoration-none text-dark">
                <img src="{{ asset('assets/web_assets/default-profile.jpg') }}" alt="Profile Picture" class="rounded-circle border border-primary p-1 me-2" style="width: 40px; height: 40px; object-fit: cover;">
              </a>
              <a href="/student/profile" class="text-decoration-none text-dark">
                <span>${Username}</span>
              </a>
            </div>
      `;
    }
  });
</script>

</body>
</html>

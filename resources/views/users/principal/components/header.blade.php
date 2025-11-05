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
    <link rel="stylesheet" href="{{ asset('css/Layout/header.css') }}">
    <style>
        .main-com{
          background-color: #ECF1F9;
        }
      </style>
</head>
<body>

<!-- Header -->
<div class="admin-header">
    <button class="btn btn-outline-secondary" id="ah_toggleSidebar">
        <i class="bi bi-list"></i>
    </button>
    <button style="display: none;" class="btn btn-outline-secondary" id="ah_closeSidebar"><i class="bi bi-x"></i></button>
    

    <div class="d-flex align-items-center position-relative">
        <div class="dropdown">
            <span type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="fa-solid fa-user ah_usericon"></i> Guest
            </span>
            <ul class="dropdown-menu dropdown-menu-end" id="dropdownMenu">
                <li class="dropdown-header" id="designationHeader">Administrator</li>
                <li><a class="dropdown-item" href="#">Profile</a></li>
                <li><a class="dropdown-item" href="#">Settings</a></li>
                <li><hr class="dropdown-divider"></li>
                <li><a class="dropdown-item" href="#" id="logoutBtn">Logout</a></li>
            </ul>
        </div>
    </div>
</div>



<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
  if (!sessionStorage.getItem("token")) {
    // Redirect to blank path or your preferred path if token is missing.
    window.location.href = "/";
  }
});
    document.addEventListener("DOMContentLoaded", function () {
        const userName = sessionStorage.getItem("name") || "Guest"; // Default to "Admin" if no name
        const designation = sessionStorage.getItem("designation") || "Administrator"; // Default to "Administrator" if no designation

        // Set the name in the dropdown button
        document.getElementById("dropdownMenuButton").innerHTML = '<i class="fa-solid fa-user ah_usericon"></i> ' + userName;

        // Update designation in the dropdown header
        document.getElementById("designationHeader").textContent = designation;

        // Add onclick event to manually toggle dropdown
        document.getElementById("dropdownMenuButton").addEventListener("click", function () {
            dropdownMenu.classList.toggle("show");
        });

        document.addEventListener("click", function (event) {
            if (!document.getElementById("dropdownMenuButton").contains(event.target) &&
                !dropdownMenu.contains(event.target)) {
                dropdownMenu.classList.remove("show");
            }
        });

        const toggleSidebar = document.getElementById('ah_toggleSidebar');
        const closeSidebar = document.getElementById('ah_closeSidebar');
        const mainsidebar = document.querySelector('.as-slim-sidebar');

        toggleSidebar.addEventListener('click', () => {
            toggleSidebar.style.display = 'none';
            closeSidebar.style.display = 'block';
            mainsidebar.style.display = 'block';
        });

        closeSidebar.addEventListener('click', () => {
            closeSidebar.style.display = 'none';
            toggleSidebar.style.display = 'block';
            mainsidebar.style.display = 'none';
        });

        // Handle Logout
        document.getElementById("logoutBtn").addEventListener("click", function (event) {
            event.preventDefault();
            
            const token = sessionStorage.getItem("token"); // Retrieve stored token
            if (!token) {
                alert("No token found, please login again.");
                window.location.href = "/institute_role_login";
                return;
            }

            let logoutAPI, redirectURL;
            if (designation.toLowerCase() === "admin") {
                logoutAPI = "/api/admin/logout";
                redirectURL = "/admin_login";
            } else {
                logoutAPI = "/api/institution-role-logout";
                redirectURL = "/institute_role_login";
            }

            fetch(logoutAPI, {
                method: "POST",
                headers: {
                    "Content-Type": "application/json"
                },
                body: JSON.stringify({ token })
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === "success") {
                    alert("Logged out successfully.");
                    sessionStorage.clear();
                    window.location.href = redirectURL;
                } else {
                    alert("Logout failed: " + data.message);
                }
            })
            .catch(error => {
                alert("Something went wrong: " + error);
            });
        });
    });
</script>

</body>
</html>

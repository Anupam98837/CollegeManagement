<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Expandable Student Sidebar</title>
  <!-- Bootstrap CSS -->
  <link
    href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css"
    rel="stylesheet"
  />
  <!-- Font Awesome -->
  <link
    href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css"
    rel="stylesheet"
  />
  <!-- Custom Sidebar CSS -->
  <link rel="stylesheet" href="{{ asset('css/Layout/sidebar.css') }}" />
</head>
<body>
  <!-- Slim Sidebar -->
  <div class="as-slim-sidebar">
    <!-- Logo -->
    <div class="as-logo">
      <img src="{{ asset('assets/web_assets/logo.png') }}"
           width="100%" alt="Logo" class="img-fluid rounded-circle">
    </div>

    <!-- Navigation -->
    <nav class="as-nav">
      <a href="/student/dashboard" class="as-nav-link" title="Dashboard">
        <i class="fa-solid fa-house"></i>
        <span>Dashboard</span>
      </a>
      {{-- <a href="/Student/Profile" class="as-nav-link" title="Profile">
        <i class="fa-solid fa-user"></i>
        <span>Profile</span>
      </a> --}}
      <hr class="text-white">

      
      <!-- Profi
        le Dropdown -->
      <div class="as-dropdown">
        <a href="#" class="as-nav-link" title="Profile" onclick="toggleDropdown(event)">
          <i class="fa-solid fa-user"></i>
          <span>Profile</span>
          <i class="fa-solid fa-chevron-down ms-auto"></i>
        </a>
        <div class="as-submenu">
          <a href="/student/register" class="as-nav-link">Register</a>
          <a href="/student/upload-Document" class="as-nav-link">Upload Documents</a>
          <a href="/student/profile" class="as-nav-link">Profile</a>
          {{-- <a href="/student/change_password" class="as-nav-link">Change Password</a> --}}
        </div>
      </div>

      <!-- Fees Link (no submenu) -->
      <a href="/student/fees" class="as-nav-link" title="Fees">
        <i class="fa-solid fa-receipt"></i>
        <span>Fees</span>
      </a>

      <!-- Notifications Dropdown -->
      {{-- <div class="as-dropdown">
        <a href="#" class="as-nav-link" title="Notifications" onclick="toggleDropdown(event)">
          <i class="fa-solid fa-bell"></i>
          <span>Notifications</span>
          <i class="fa-solid fa-chevron-down ms-auto"></i>
        </a>
        <div class="as-submenu">
          <a href="/student/view_notifications" class="as-nav-link">View Notifications</a>
          <a href="/student/notification_settings" class="as-nav-link">Notification Settings</a>
        </div>
      </div> --}}

      <!-- Help Dropdown -->
      {{-- <div class="as-dropdown">
        <a href="#" class="as-nav-link" title="Help" onclick="toggleDropdown(event)">
          <i class="fa-solid fa-circle-question"></i>
          <span>Help</span>
          <i class="fa-solid fa-chevron-down ms-auto"></i>
        </a>
        <div class="as-submenu">
          <a href="/student/faq" class="as-nav-link">FAQs</a>
          <a href="/student/support" class="as-nav-link">Contact Support</a>
        </div>
      </div> --}}
    </nav>

    <!-- Logout or Login Button (Dynamic Based on Session) -->
    <div id="authButtonContainer">
      <a href="#" class="as-logout" title="Logout" id="logoutButton" style="display: none;">
        <i class="fa-solid fa-sign-out-alt"></i>
        <span>Logout</span>
      </a>
      <a href="/student/login" class="as-logout" title="Login" id="loginButton" style="display: none;">
        <i class="fa-solid fa-sign-in-alt"></i>
        <span>Login</span>
      </a>
    </div>
  </div>

  <!-- Bootstrap JS and SweetAlert2 -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  <script>
    // Check for active session token; if not, redirect.
    document.addEventListener("DOMContentLoaded", function() {
      if (!sessionStorage.getItem("token")) {
        window.location.href = "/";
      }
    });

    // Toggle dropdown open/close and close any other open dropdowns.
    function toggleDropdown(event) {
      event.preventDefault();
      const parent = event.target.closest(".as-dropdown");
      const submenu = parent.querySelector(".as-submenu");
      const arrow = parent.querySelector(".fa-chevron-down, .fa-chevron-up");
      const dropdownKey = parent.querySelector("a.as-nav-link").getAttribute("title") || parent.innerText.trim();

      // If this dropdown is not open, close all others first.
      if (!parent.classList.contains("open")) {
        closeAllDropdowns();
        // Open the clicked dropdown.
        parent.classList.add("open");
        submenu.style.display = "block";
        arrow.classList.remove("fa-chevron-down");
        arrow.classList.add("fa-chevron-up");
        sessionStorage.setItem(dropdownKey, "open");
      } else {
        // Close the dropdown.
        parent.classList.remove("open");
        submenu.style.display = "none";
        arrow.classList.remove("fa-chevron-up");
        arrow.classList.add("fa-chevron-down");
        sessionStorage.removeItem(dropdownKey);
      }
    }

    // Close all open dropdowns.
    function closeAllDropdowns() {
      document.querySelectorAll(".as-dropdown.open").forEach(dropdown => {
        dropdown.classList.remove("open");
        const submenu = dropdown.querySelector(".as-submenu");
        if (submenu) { submenu.style.display = "none"; }
        const arrow = dropdown.querySelector(".fa-chevron-down, .fa-chevron-up");
        if (arrow) {
          arrow.classList.remove("fa-chevron-up");
          arrow.classList.add("fa-chevron-down");
        }
        const dropdownKey = dropdown.querySelector("a.as-nav-link").getAttribute("title") || dropdown.innerText.trim();
        sessionStorage.removeItem(dropdownKey);
      });
    }

    // Restore dropdown open state from sessionStorage.
    function restoreDropdownState() {
      document.querySelectorAll(".as-dropdown").forEach(dropdown => {
        const link = dropdown.querySelector(".as-nav-link");
        const dropdownKey = link.getAttribute("title") || link.innerText.trim();
        if (sessionStorage.getItem(dropdownKey) === "open") {
          dropdown.classList.add("open");
          const submenu = dropdown.querySelector(".as-submenu");
          if (submenu) { submenu.style.display = "block"; }
          const arrow = dropdown.querySelector(".fa-chevron-down, .fa-chevron-up");
          if (arrow) {
            arrow.classList.remove("fa-chevron-down");
            arrow.classList.add("fa-chevron-up");
          }
        }
      });
    }

    // Active link highlighting based on current URL.
    function setActiveLink() {
      const currentPath = window.location.pathname;
      const links = document.querySelectorAll(".as-nav .as-nav-link[href]:not([href='#'])");
      // Remove active classes initially.
      links.forEach(link => {
        link.classList.remove("active");
        const parentDropdown = link.closest(".as-dropdown");
        if (parentDropdown) {
          const parentLink = parentDropdown.querySelector(".as-nav-link");
          parentLink.classList.remove("active-parent");
        }
      });
      // Mark active links.
      links.forEach(link => {
        const linkHref = link.getAttribute("href");
        if (currentPath.includes(linkHref)) {
          link.classList.add("active");
          // If the link is in a dropdown, mark the parent header.
          const parentDropdown = link.closest(".as-dropdown");
          if (parentDropdown) {
            const parentLink = parentDropdown.querySelector(".as-nav-link");
            parentLink.classList.add("active-parent");
            parentDropdown.classList.add("open");
            const submenu = parentDropdown.querySelector(".as-submenu");
            if (submenu) { submenu.style.display = "block"; }
            const arrow = parentDropdown.querySelector(".fa-chevron-down, .fa-chevron-up");
            if (arrow) {
              arrow.classList.remove("fa-chevron-down");
              arrow.classList.add("fa-chevron-up");
            }
          }
        }
      });
    }

    // Check authentication status and update login/logout buttons.
    function checkAuthStatus() {
      const token = sessionStorage.getItem("token");
      if (token) {
        document.getElementById("logoutButton").style.display = "block";
        document.getElementById("loginButton").style.display = "none";
      } else {
        document.getElementById("logoutButton").style.display = "none";
        document.getElementById("loginButton").style.display = "block";
      }
    }

    // Logout functionality.
    document.getElementById("logoutButton").addEventListener("click", async function(event) {
      event.preventDefault();
      const token = sessionStorage.getItem("token");
      if (!token) {
        window.location.href = "/student/login";
        return;
      }
      Swal.fire({
        title: "Logging out...",
        allowOutsideClick: false,
        didOpen: () => {
          Swal.showLoading();
        }
      });
      try {
        const response = await fetch("/api/student/logout", {
          method: "POST",
          headers: {
            "Content-Type": "application/json",
            "Authorization": `Bearer ${token}`
          },
          body: JSON.stringify({ token: token })
        });
        const data = await response.json();
        Swal.close();
        if (response.ok) {
          sessionStorage.clear();
          localStorage.removeItem("student_photo");
          Swal.fire({
            icon: "success",
            title: "Logged Out",
            text: data.message,
            timer: 2000,
            showConfirmButton: false
          }).then(() => {
            window.location.href = "/";
          });
        } else {
          Swal.fire({
            icon: "error",
            title: "Logout Failed",
            text: data.message || "An error occurred. Try again."
          });
        }
      } catch (error) {
        Swal.fire({
          icon: "error",
          title: "Error",
          text: "An error occurred while logging out. Try again."
        });
      }
    });

    // Attach event listeners to links that don't have a submenu toggle.
    document.addEventListener("DOMContentLoaded", function() {
      restoreDropdownState();
      setActiveLink();
      checkAuthStatus();

      // For all links that are not dropdown toggles (i.e. no onclick attribute),
      // close any open dropdowns on click.
      document.querySelectorAll(".as-nav a.as-nav-link:not([onclick])").forEach(link => {
        link.addEventListener("click", function() {
          closeAllDropdowns();
        });
      });
    });
  </script>
</body>
</html>

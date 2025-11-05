<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Expandable Admin Sidebar</title>
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
  <link rel="stylesheet" href="{{ asset('css/main.css') }}">
  <link rel="stylesheet" href="{{ asset('css/Layout/sidebar.css') }}" />
</head>
<body>
  <!-- Slim Sidebar -->
  <div class="as-slim-sidebar">
    <!-- Logo -->
    <div class="as-logo">
      <img src="{{ asset('assets/web_assets/logo.png') }}"
           width="100%" alt="Logo" class="img-fluid">
    </div>

    <!-- Navigation -->
    <nav class="as-nav">
      <a href="/admin/dashboard" class="as-nav-link" title="Dashboard">
        <i class="fa-solid fa-house"></i>
        <span>Dashboard</span>
      </a>
      
      <hr class="text-white">

      <div class="as-dropdown">
        <a href="/admin/academy/dashboard" class="as-nav-link" title="Academy" onclick="toggleDropdown(event)">
          <i class="fa-solid fa-graduation-cap"></i>
          <span>Academy</span>
          <i class="fa-solid fa-angle-right ms-auto"></i>
        </a>
        <div class="as-submenu">
          <a href="/admin/academy/dashboard" class="as-nav-link">Dashboard</a>
          <a href="/admin/academy/campus/manage" class="as-nav-link">Manage Campus</a>
          <a href="/admin/academy/institute/manage" class="as-nav-link">Manage Institution</a>
        </div>
      </div>

      <div class="as-dropdown">
        <a href="/admin/institute/dashboard" class="as-nav-link" title="Institution" onclick="toggleDropdown(event)">
          <i class="fa-solid fa-university"></i>
          <span>Institution</span>
          <i class="fa-solid fa-angle-right ms-auto"></i>
        </a>
        <div class="as-submenu">
          <a href="/admin/institute/dashboard" class="as-nav-link">Dashboard</a>
          <a href="/admin/institute/course/manage" class="as-nav-link">Institution Courses</a>
          <a href="/admin/institute/subject/manage" class="as-nav-link">Subject</a>
          <a href="/admin/institute/notice/manage" class="as-nav-link">Notice</a>
          <a href="/admin/institute/event/manage" class="as-nav-link">Event</a>
          <a href="/admin/institute/routine/manage" class="as-nav-link">Routine</a>
          <a href="/admin/institute/study-materials/manage" class="as-nav-link">Study Material</a>

        </div>
      </div>

      <div class="as-dropdown">
        <a href="/admin/course/dashboard" class="as-nav-link" title="Course" onclick="toggleDropdown(event)">
          <i class="fa-solid fa-book-open"></i>
          <span>Course</span>
          <i class="fa-solid fa-angle-right ms-auto"></i>
        </a>
        <div class="as-submenu">
          <a href="/admin/course/dashboard" class="as-nav-link">Dashboard</a>
          <a href="/admin/course/type/manage" class="as-nav-link">Course Type</a>
          <a href="/admin/course/manage" class="as-nav-link">Manage Course</a>
          {{-- <a href="/admin/institute/course/manage" class="as-nav-link">Institution Courses</a> --}}
          <a href="/admin/course/intake/manage" class="as-nav-link">Intake</a>
        </div>
      </div>

      <div class="as-dropdown">
        <a href="/admin/accounting/dashboard" class="as-nav-link" title="Accounting" onclick="toggleDropdown(event)">
          <i class="fa-solid fa-calculator"></i>
          <span>Accounting</span>
          <i class="fa-solid fa-angle-right ms-auto"></i>
        </a>
        <div class="as-submenu">
          <a href="/admin/accounting/dashboard" class="as-nav-link">Dashboard</a>
          <a href="/admin/accounting/expense/manage" class="as-nav-link">Manage Expense</a>
          <a href="/admin/accounting/fees-structure/manage" class="as-nav-link">Fees Structure</a>
          <a href="/admin/accounting/fees/manage" class="as-nav-link">Manage Fees</a>
          <a href="/admin/accounting/fees/collect" class="as-nav-link">Collect Fees</a>
        </div>
      </div>

      <div class="as-dropdown">
        <a href="/admin/fees/report" class="as-nav-link" title="Report" onclick="toggleDropdown(event)">
          <i class="fa-solid fa-chart-line"></i>
          <span>Report</span>
          <i class="fa-solid fa-angle-right ms-auto"></i>
        </a>
        <div class="as-submenu">
          <a href="/admin/fees/report" class="as-nav-link">Fees Report</a>
          <a href="/admin/student/attendance/report" class="as-nav-link">Attendance Report</a>

        </div>
      </div>

      <hr class="text-white">

      <a href="/admin/users" class="as-nav-link" title="All Users">
        <i class="fa-solid fa-user-group"></i>
        <span>All Users</span>
      </a>

      <div class="as-dropdown">
        <a href="/admin/role/dashboard" class="as-nav-link" title="Role" onclick="toggleDropdown(event)">
          <i class="fa-solid fa-user-shield"></i>
          <span>Role</span>
          <i class="fa-solid fa-angle-right ms-auto"></i>
        </a>
        <div class="as-submenu">
          <a href="/admin/role/dashboard" class="as-nav-link">Dashboard</a>
          <a href="/admin/role/manage" class="as-nav-link">Institute Role</a>
        </div>
      </div>

      <div class="as-dropdown">
        <a href="/admin/faculty/manage" class="as-nav-link" title="Faculty" onclick="toggleDropdown(event)">
          <i class="fa-solid fa-chalkboard-teacher"></i>
          <span>Faculty</span>
          <i class="fa-solid fa-angle-right ms-auto"></i>
        </a>
        <div class="as-submenu">
          <a href="/admin/faculty/manage" class="as-nav-link">Manage Faculty</a>
        </div>
      </div>

      <div class="as-dropdown">
        <a href="/admin/student/dashboard" class="as-nav-link" title="Student" onclick="toggleDropdown(event)">
          <i class="fa-solid fa-users"></i>
          <span>Student</span>
          <i class="fa-solid fa-angle-right ms-auto"></i>
        </a>
        <div class="as-submenu">
          <a href="/admin/student/dashboard" class="as-nav-link">Dashboard</a>
          <a href="/admin/student/register" class="as-nav-link">Register</a>
          <a href="/admin/student/document/upload" class="as-nav-link">Upload Documents</a>
          <a href="/admin/student/details" class="as-nav-link">Student Details</a>
          <a href="/admin/student/promote" class="as-nav-link">Student Promote</a>
          <a href="/admin/student/scholarship" class="as-nav-link">Manage Scholarship</a>
        </div>
      </div>

      <div class="as-dropdown">
        <a href="/admin/agent/dashboard" class="as-nav-link" title="Agent" onclick="toggleDropdown(event)">
          <i class="fa-solid fa-user-tie"></i>
          <span>Agent</span>
          <i class="fa-solid fa-angle-right ms-auto"></i>
        </a>
        <div class="as-submenu">
          <a href="/admin/agent/dashboard" class="as-nav-link">Dashboard</a>
          <a href="/admin/agent/register" class="as-nav-link">Register Agent</a>
        </div>
      </div>

      <hr class="text-white">

      <div class="as-dropdown">
        <a href="/admin/transport/vehicle/manage" class="as-nav-link" title="Transport" onclick="toggleDropdown(event)">
          <i class="fa-solid fa-map-location-dot"></i>
          <span>Transport</span>
          <i class="fa-solid fa-angle-right ms-auto"></i>
        </a>
        <div class="as-submenu">
          <a href="/admin/transport/vehicle/manage" class="as-nav-link">Manage Vehicles</a>
          <a href="/admin/transport/route/manage" class="as-nav-link">Manage Routes</a>
        </div>
      </div>

      <div class="as-dropdown">
        <a href="/admin/hostel/manage" class="as-nav-link" title="Hostel" onclick="toggleDropdown(event)">
          <i class="fa-solid fa-hotel"></i>
          <span>Hostel</span>
          <i class="fa-solid fa-angle-right ms-auto"></i>
        </a>
        <div class="as-submenu">
          <a href="/admin/hostel/manage" class="as-nav-link">Manage Hostel</a>
          <a href="/admin/hostel/room/manage" class="as-nav-link">Manage Rooms</a>
        </div>
      </div>

      <hr class="text-white">

      <!-- Logout -->
      <a href="#" class="as-logout" title="Logout" id="authButton">
        <i class="fa-solid fa-sign-out-alt"></i>
        <span>Logout</span>
      </a>
    </nav>
  </div>

  <!-- Bootstrap JS & SweetAlert2 -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script>
    document.addEventListener("DOMContentLoaded", function() {
      // redirect if no token
      if (!sessionStorage.getItem("token")) {
        window.location.href = "/";
        return;
      }
      restoreDropdownState();
      setActiveLink();
      document.getElementById("authButton").style.display = "block";
    });

    function toggleDropdown(event) {
      const parent = event.target.closest(".as-dropdown");
      const submenu = parent.querySelector(".as-submenu");
      const arrow = parent.querySelector(".fa-angle-right, .fa-angle-down");
      // close others
      document.querySelectorAll(".as-dropdown.open").forEach(o => {
        if (o !== parent) {
          o.classList.remove("open");
          o.querySelector(".as-submenu").style.display = "none";
          o.querySelector(".fa-angle-down").classList.replace("fa-angle-down","fa-angle-right");
        }
      });
      // toggle this
      parent.classList.toggle("open");
      submenu.style.display = parent.classList.contains("open") ? "block" : "none";
      arrow.classList.toggle("fa-angle-right");
      arrow.classList.toggle("fa-angle-down");
    }

    function closeAllDropdowns() {
      document.querySelectorAll(".as-dropdown.open").forEach(o => {
        o.classList.remove("open");
        o.querySelector(".as-submenu").style.display = "none";
        o.querySelector(".fa-angle-down").classList.replace("fa-angle-down","fa-angle-right");
      });
    }

    function setActiveLink() {
      const path = window.location.pathname;
      document.querySelectorAll(".as-nav-link").forEach(link => {
        link.classList.remove("active","active-parent");
      });
      document.querySelectorAll(".as-nav-link[href]").forEach(link => {
        if (path.endsWith(link.getAttribute("href"))) {
          link.classList.add("active");
          const parent = link.closest(".as-dropdown");
          if (parent) {
            parent.classList.add("open");
            parent.querySelector(".as-submenu").style.display = "block";
            parent.querySelector(".fa-angle-right").classList.replace("fa-angle-right","fa-angle-down");
            parent.querySelector(".as-nav-link").classList.add("active-parent");
          }
        }
      });
    }

    function restoreDropdownState() {
      document.querySelectorAll(".as-dropdown").forEach(dropdown => {
        const key = dropdown.querySelector("a.as-nav-link").getAttribute("title");
        if (sessionStorage.getItem(key) === "open") {
          dropdown.classList.add("open");
          dropdown.querySelector(".as-submenu").style.display = "block";
          dropdown.querySelector(".fa-angle-right").classList.replace("fa-angle-right","fa-angle-down");
        }
      });
    }

    document.getElementById("authButton").addEventListener("click", async e => {
      e.preventDefault();
      const token = sessionStorage.getItem("token");
      Swal.fire({ title:"Logging out...", allowOutsideClick:false, didOpen:()=>Swal.showLoading() });
      try {
        await fetch("/api/admin/logout", {
          method:"POST",
          headers:{ "Content-Type":"application/json","Authorization":`Bearer ${token}` },
          body: JSON.stringify({ token })
        });
      } catch {}
      sessionStorage.clear();
      Swal.fire({ icon:"success", title:"Logged Out", timer:1500, showConfirmButton:false })
           .then(()=> window.location.href="/");
    });

    // scroll active into view
    document.addEventListener("DOMContentLoaded", () => {
      const active = document.querySelector(".as-nav-link.active, .as-nav-link.active-parent");
      if(active) active.scrollIntoView({behavior:"smooth",block:"center"});
    });
  </script>
</body>
</html>

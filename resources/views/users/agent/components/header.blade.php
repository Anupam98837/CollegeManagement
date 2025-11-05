<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Agent Header with Sidebar</title>
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Bootstrap Icons -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.10.5/font/bootstrap-icons.min.css">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
  <!-- Custom CSS -->
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

  <!-- Optionally, you can include a search bar here if desired -->
  
  <div class="d-flex align-items-center" id="userSection">
    <!-- Agent data will be injected here -->
  </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
  document.addEventListener("DOMContentLoaded", function() {
    if (!sessionStorage.getItem("token")) {
      window.location.href = "/";
    }
    
    // Get agent name from sessionStorage; fallback to "Agent"
    let agentName = sessionStorage.getItem("agent_name") || "Agent";
    
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
    
    // For agents, simply display dummy profile picture and the agent name from sessionStorage
    userSection.innerHTML = `
      <div class="d-flex align-items-center">
        <a href="/agent/profile" class="text-decoration-none text-dark">
          <img src="{{ asset('assets/web_assets/default-profile.jpg') }}" alt="Profile Picture" 
               class="rounded-circle border border-primary p-1 me-2" 
               style="width: 40px; height: 40px; object-fit: cover;">
        </a>
        <a href="/agent/profile" class="text-decoration-none text-dark">
          <span>${agentName}</span>
        </a>
      </div>
    `;
  });
</script>

</body>
</html>

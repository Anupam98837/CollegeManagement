<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>College Management Portal</title>
  <!-- Bootstrap CSS -->
  <link
    href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css"
    rel="stylesheet"
  />
  <!-- Font Awesome -->
  <link
    rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"
  />
  <style>
    body {
      display: flex;
      flex-direction: column;
      min-height: 100vh;
    }

    .navbar-brand {
      font-weight: bold;
      font-size: 1.25rem;
    }

    .role-button {
      display: block;
      position: relative;
      color: #fff;
      padding: 2rem 1rem;
      text-align: center;
      text-decoration: none;
      font-size: 1rem;
      font-weight: bold;
      border-radius: 4px;
      transition: transform 0.3s ease, box-shadow 0.3s ease;
      clip-path: polygon(15% 0, 100% 0, 85% 100%, 0% 100%);
    }

    .role-button i {
      font-size: 2rem;
      margin-bottom: 0.5rem;
      display: block;
    }

    .role-button:hover {
      transform: scale(1.05);
      box-shadow: 0 6px 18px rgba(0, 0, 0, 0.2);
    }

    .admin {
      background-color: #dc3545;
    }
    .institution {
      background-color: #28a745;
    }
    .student {
      background-color: #0d6efd;
    }
    .agent {
      background-color: #ffc107;
    }

    .as-logo {
      margin-bottom: 1.5rem;
      display: flex;
      justify-content: center;
    }

    .as-logo img {
      width: 200px;
      height: auto;
      border-radius: 50%;
    }

    footer {
      margin-top: auto;
    }
  </style>
</head>
<body>
  <!-- Header -->
  <header>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
      <div class="container">
        <a class="navbar-brand" href="#">College Management</a>
        <button
          class="navbar-toggler"
          type="button"
          data-bs-toggle="collapse"
          data-bs-target="#navbarContent"
          aria-controls="navbarContent"
          aria-expanded="false"
          aria-label="Toggle navigation"
        >
          <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarContent">
          <ul class="navbar-nav ms-auto">
            {{-- <li class="nav-item">
              <a class="nav-link" href="/about">About</a>
            </li> --}}
          </ul>
        </div>
      </div>
    </nav>
  </header>

  <!-- Main Content -->
  <main class="flex-grow-1">
    <div class="container my-5">
      <div class="card shadow-sm">
        <div class="card-body text-center">
          <div class="as-logo">
            <img src="{{ asset('assets/web_assets/logo.png') }}" alt="Logo" class="img-fluid">
          </div>
          <h1 class="card-title mb-4 text-primary">Camellia Academic Management Portal</h1>
          <p class="card-text mb-4">Select your role to login:</p>
          <div class="row g-3">
            <div class="col-12 col-md-3">
              <a href="/admin/login" class="role-button admin">
                <i class="fa-solid fa-user-shield"></i>
                Admin
              </a>
            </div>
            <div class="col-12 col-md-3">
              <a href="/institution-role/login" class="role-button institution">
                <i class="fa-solid fa-building-columns"></i>
                Institution
              </a>
            </div>
            <div class="col-12 col-md-3">
              <a href="/agent/login" class="role-button agent">
                <i class="fa-solid fa-user-tie"></i>
                Agent
              </a>
            </div>
            <div class="col-12 col-md-3">
              <a href="/student/login" class="role-button student">
                <i class="fa-solid fa-user-graduate"></i>
                Student
              </a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </main>

  <!-- Footer -->
  <footer class="bg-primary text-white text-center py-3">
    <div class="container">
      <small>&copy; 2025 Camellia Academic Management Portal. All rights reserved.</small>
    </div>
  </footer>

  <!-- Bootstrap Bundle with Popper -->
  <script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"
  ></script>
</body>
</html>

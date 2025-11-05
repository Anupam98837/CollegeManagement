<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Login Page</title>

  <!-- Font Awesome for icons -->
  <link
    href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css"
    rel="stylesheet"
  />
  <!-- SweetAlert2 for alerts -->
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <link rel="stylesheet" href="{{ asset('css/login.css') }}">

</head>
<body>
  <div class="container">
    <!-- Left Section -->
    <div class="left-section">
      {{-- <h1>Get more things done with Iofrm platform.</h1>
      <p>Access to the most powerful tool in the entire design and web industry.</p> --}}
      <!-- Replace the src below with your own illustration -->
      <img
        src="{{ asset('assets/web_assets/login.png') }}"
        alt="Team Illustration"
      />
    </div>

    <!-- Right Section -->
    <div class="right-section">
      <div class="form-container">
        <h2 class="text-secondary">Admin Login</h2>
        <form id="admin-loginForm">
          <!-- Identifier -->
          <div class="form-group">
            <label for="admin-identifier">Email Address</label>
            <div class="input-wrapper">
              <i class="fa-solid fa-user"></i>
              <input
                type="text"
                id="admin-identifier"
                placeholder="Enter your email or phone"
                required
              />
            </div>
          </div>
          <!-- Password -->
          <div class="form-group">
            <label for="admin-password">Password</label>
            <div class="input-wrapper">
              <i class="fa-solid fa-lock"></i>
              <input
                type="password"
                id="admin-password"
                placeholder="Enter password"
                required
              />
              <button
                type="button"
                class="toggle-btn"
                id="admin-togglePassword"
              >
                <i class="fa-solid fa-eye"></i>
              </button>
            </div>
          </div>
          <!-- Submit Button -->
          <button type="submit" class="btn-submit" id="admin-loginBtn">
            Login
          </button>
        </form>
        <!-- Social Login -->
        {{-- <div class="social-login">
          <span>Or login with</span>
          <a href="#"><i class="fab fa-google"></i></a>
          <a href="#"><i class="fab fa-facebook-f"></i></a>
          <a href="#"><i class="fab fa-apple"></i></a>
        </div> --}}
      </div>
    </div>
  </div>

  <!-- JavaScript -->
  <script>
    document.addEventListener("DOMContentLoaded", function () {
      // Toggle password visibility
      document
        .getElementById("admin-togglePassword")
        .addEventListener("click", function () {
          let passwordField = document.getElementById("admin-password");
          let type = passwordField.type === "password" ? "text" : "password";
          passwordField.type = type;
          // Toggle the icon
          this.querySelector("i").classList.toggle("fa-eye");
          this.querySelector("i").classList.toggle("fa-eye-slash");
        });

      const loginForm = document.getElementById("admin-loginForm");
      const submitBtn = document.getElementById("admin-loginBtn");

      loginForm.addEventListener("submit", async function (event) {
        event.preventDefault();

        // Disable the button and show a loading text/spinner
        submitBtn.disabled = true;
        submitBtn.innerHTML = ` Loading...`;

        let identifier = document.getElementById("admin-identifier").value;
        let password = document.getElementById("admin-password").value;

        try {
          let response = await fetch("/api/admin/login", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ identifier, password }),
          });

          let data = await response.json();

          if (response.ok) {
            // Store token and other info in sessionStorage
            sessionStorage.setItem("token", data.admin_token);
            sessionStorage.setItem("designation", data.admin.designation);
            sessionStorage.setItem("name", data.admin.name);

            Swal.fire({
              icon: "success",
              title: "Login Successful",
              text: "Redirecting to dashboard...",
              timer: 1500,
              showConfirmButton: false,
            }).then(() => {
              window.location.href = "/admin/dashboard";
            });
          } else {
            Swal.fire({
              icon: "error",
              title: "Login Failed",
              text: data.message,
            });
            // Re-enable the button on error
            submitBtn.disabled = false;
            submitBtn.innerHTML = "Login";
          }
        } catch (error) {
          Swal.fire({
            icon: "error",
            title: "Login Failed",
            text: "An error occurred while logging in.",
          });
          // Re-enable the button on error
          submitBtn.disabled = false;
          submitBtn.innerHTML = "Login";
        }
      });
    });
  </script>
</body>
</html>

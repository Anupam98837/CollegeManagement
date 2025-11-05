<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Institution Role Login</title>
  <!-- Custom CSS -->
  <link rel="stylesheet" href="{{ asset('css/login.css') }}">
  <!-- Font Awesome for icons -->
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
  <!-- SweetAlert2 for alerts -->
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
  <div class="container">
    <!-- Left Section (Illustration) -->
    <div class="left-section">
      
      <img src="{{ asset('assets/web_assets/login.png') }}" alt="Login Illustration">
    </div>
    <!-- Right Section (Form) -->
    <div class="right-section">
      <div class="form-container">
        <h2>Institution Role Login</h2>
        <form id="institution-loginForm">
          <div class="form-group">
            <label for="institution-identifier">Email or Official Phone</label>
            <div class="input-wrapper">
              <i class="fa-solid fa-user"></i>
              <input
                type="text"
                id="institution-identifier"
                placeholder="Enter your email or phone"
                required>
            </div>
          </div>
          <div class="form-group">
            <label for="institution-password">Password</label>
            <div class="input-wrapper">
              <i class="fa-solid fa-lock"></i>
              <input
                type="password"
                id="institution-password"
                placeholder="Enter your password"
                required>
              <button type="button" class="toggle-btn" id="institution-togglePassword">
                <i class="fa-solid fa-eye"></i>
              </button>
            </div>
          </div>
          <button type="submit" class="btn-submit" id="institution-loginButton">Login</button>
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

  <script>
    // Toggle password visibility
    document.getElementById("institution-togglePassword").addEventListener("click", function() {
      let passwordField = document.getElementById("institution-password");
      let type = passwordField.type === "password" ? "text" : "password";
      passwordField.type = type;
      this.querySelector("i").classList.toggle("fa-eye");
      this.querySelector("i").classList.toggle("fa-eye-slash");
    });

    // Handle form submission
    document.getElementById("institution-loginForm").addEventListener("submit", function(event) {
      event.preventDefault();

      const loginButton = document.getElementById("institution-loginButton");
      // Disable the button and show a spinner
      loginButton.disabled = true;
      loginButton.innerHTML = `<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Logging in...`;

      const identifier = document.getElementById("institution-identifier").value;
      const password = document.getElementById("institution-password").value;
      
      fetch("/api/institution-role-login", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ identifier, password })
      })
      .then(response => response.json())
      .then(data => {
        if (data.status === "success") {
          const id = data.user.id.$oid;
          const token = data.role_based_token;
          const designation = data.user.designation;
          const name = data.user.name;
          const institutionId = data.user.institution_id;
          const institutionName = data.user.institution_name;
          const institutionType = data.user.institution_type;
          // Store token, designation, name, institution id and institution name in session storage
          sessionStorage.setItem("id", id);
          sessionStorage.setItem("token", token);
          sessionStorage.setItem("designation", designation);
          sessionStorage.setItem("name", name);
          sessionStorage.setItem("institution_id", institutionId);
          sessionStorage.setItem("institution_name", institutionName);
          sessionStorage.setItem("institution_type", institutionType);

          Swal.fire({
            icon: 'success',
            title: 'Login Successful',
            text: 'Redirecting to dashboard...',
            timer: 1500,
            showConfirmButton: false
          }).then(() => {
            window.location.href = `/${designation.toLowerCase()}/dashboard`;
          });
        } else {
          Swal.fire({
            icon: 'error',
            title: 'Login Failed',
            text: data.message
          });
          // Re-enable the button on error
          loginButton.disabled = false;
          loginButton.innerHTML = "Login";
        }
      })
      .catch(error => {
        Swal.fire({
          icon: 'error',
          title: 'Login Failed',
          text: 'Something went wrong. Please try again.'
        });
        // Re-enable the button on error
        loginButton.disabled = false;
        loginButton.innerHTML = "Login";
      });
    });
  </script>
</body>
</html>

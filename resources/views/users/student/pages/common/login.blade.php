<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Student Login</title>
  <!-- Custom CSS -->
  <link rel="stylesheet" href="{{ asset('css/login.css') }}">
  <!-- Font Awesome for icons -->
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" rel="stylesheet">
  <!-- SweetAlert2 for alerts -->
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
  <div class="container">
    <!-- Left Section (Illustration) -->
    <div class="left-section">
      <img src="{{ asset('assets/web_assets/login.png') }}" alt="Student Login Illustration">
    </div>
    <!-- Right Section (Form) -->
    <div class="right-section">
      <div class="form-container">
        <h2>Student Login</h2>
        <form id="student-loginForm">
          <div class="form-group">
            <label for="student-identifier">Email or Phone</label>
            <div class="input-wrapper">
              <i class="fa-solid fa-user"></i>
              <input type="text" id="student-identifier" placeholder="Enter your email or phone number" required>
            </div>
          </div>
          <div class="form-group">
            <label for="student-password">Password</label>
            <div class="input-wrapper">
              <i class="fa-solid fa-lock"></i>
              <input type="password" id="student-password" placeholder="Enter your password" required>
              <button type="button" class="toggle-btn" id="student-togglePassword">
                <i class="fa-solid fa-eye"></i>
              </button>
            </div>
          </div>
          <button type="submit" class="btn-submit" id="student-loginButton">
            <span id="student-loginText">Login</span>
            <span id="student-loginSpinner" class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
          </button>
          <div class="mb-3 text-center">
            <p class="mt-3" style="color: gray; margin-top: 10px; font-size: 13px;">
                Don't have an account? <a href="/student/signup" class="text-primary">Sign Up</a>
              </p>              
          </div>
        </form>
        <div id="student-responseMessage" class="mt-3 text-center"></div>
      </div>
    </div>
  </div>

  <script>
    // Toggle password visibility
    document.getElementById("student-togglePassword").addEventListener("click", function() {
      let passwordField = document.getElementById("student-password");
      let type = passwordField.type === "password" ? "text" : "password";
      passwordField.type = type;
      this.querySelector("i").classList.toggle("fa-eye");
      this.querySelector("i").classList.toggle("fa-eye-slash");
    });

    // Handle form submission
    document.getElementById("student-loginForm").addEventListener("submit", async function(event) {
      event.preventDefault();

      const loginButton = document.getElementById("student-loginButton");
      const loginText = document.getElementById("student-loginText");
      const loginSpinner = document.getElementById("student-loginSpinner");

      loginButton.disabled = true;
      loginText.textContent = "Logging in...";
      loginSpinner.classList.remove("d-none");

      const identifier = document.getElementById("student-identifier").value;
      const password = document.getElementById("student-password").value;

      try {
        const response = await fetch("/api/student/login", {
          method: "POST",
          headers: {
            "Content-Type": "application/json"
          },
          body: JSON.stringify({ identifier, password })
        });

        const data = await response.json();

        if (response.ok) {
          const message = data.message;
          const student = data.student;
          const token = data.student_token;

            sessionStorage.setItem("student_name", student.name);
            sessionStorage.setItem("student_uid", student.uid);
            sessionStorage.setItem("student_email", student.email);
            sessionStorage.setItem("student_phone", student.phone);
            sessionStorage.setItem("token", token); // Store token
            sessionStorage.setItem("designation", "Student")

          Swal.fire({
            icon: "success",
            title: "Login Successful!",
            text: message,
            timer: 2000,
            showConfirmButton: false
          }).then(() => {
            window.location.href = "/student/dashboard";
          });
        } else {
          Swal.fire({
            icon: "error",
            title: "Login Failed",
            text: data.message || "Invalid credentials"
          });
        }
      } catch (error) {
        Swal.fire({
          icon: "error",
          title: "Login Failed",
          text: "An error occurred. Please try again."
        });
      } finally {
        loginButton.disabled = false;
        loginText.textContent = "Login";
        loginSpinner.classList.add("d-none");
      }
    });
  </script>
</body>
</html>

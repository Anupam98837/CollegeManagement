<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Agent Login</title>
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
      <img src="{{ asset('assets/web_assets/login.png') }}" alt="Agent Login Illustration">
    </div>
    <!-- Right Section (Form) -->
    <div class="right-section">
      <div class="form-container">
        <h2>Agent Login</h2>
        <form id="agent-loginForm">
          <div class="form-group">
            <label for="agent-identifier">Email or Phone</label>
            <div class="input-wrapper">
              <i class="fa-solid fa-user"></i>
              <input type="text" id="agent-identifier" placeholder="Enter your email or phone number" required>
            </div>
          </div>
          <div class="form-group">
            <label for="agent-password">Password</label>
            <div class="input-wrapper">
              <i class="fa-solid fa-lock"></i>
              <input type="password" id="agent-password" placeholder="Enter your password" required>
              <button type="button" class="toggle-btn" id="agent-togglePassword">
                <i class="fa-solid fa-eye"></i>
              </button>
            </div>
          </div>
          <button type="submit" class="btn-submit" id="agent-loginButton">
            <span id="agent-loginText">Login</span>
            <span id="agent-loginSpinner" class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
          </button>
          <div class="mb-3 text-center">
            <p class="mt-3" style="color: gray; margin-top: 10px; font-size: 13px;">
              Don't have an account? <a href="/agent/signup" class="text-primary">Sign Up</a>
            </p>
          </div>
        </form>
        <div id="agent-responseMessage" class="mt-3 text-center"></div>
      </div>
    </div>
  </div>

  <script>
    // Toggle password visibility
    document.getElementById("agent-togglePassword").addEventListener("click", function() {
      let passwordField = document.getElementById("agent-password");
      let type = passwordField.type === "password" ? "text" : "password";
      passwordField.type = type;
      this.querySelector("i").classList.toggle("fa-eye");
      this.querySelector("i").classList.toggle("fa-eye-slash");
    });

    // Handle form submission
    document.getElementById("agent-loginForm").addEventListener("submit", async function(event) {
      event.preventDefault();

      const loginButton = document.getElementById("agent-loginButton");
      const loginText = document.getElementById("agent-loginText");
      const loginSpinner = document.getElementById("agent-loginSpinner");

      loginButton.disabled = true;
      loginText.textContent = "Logging in...";
      loginSpinner.classList.remove("d-none");

      const identifier = document.getElementById("agent-identifier").value;
      const password = document.getElementById("agent-password").value;

      try {
        const response = await fetch("/api/agent/login", {
          method: "POST",
          headers: {
            "Content-Type": "application/json"
          },
          body: JSON.stringify({ identifier, password })
        });

        const data = await response.json();

        if (response.ok) {
          const message = data.message;
          const agent = data.agent;
          const token = data.agent_token;

          // Store agent info in session storage
          sessionStorage.setItem("agent_name", agent.name);
          sessionStorage.setItem("agent_uid", agent.uid);
          sessionStorage.setItem("agent_email", agent.email);
          sessionStorage.setItem("agent_phone", agent.mobile);
          sessionStorage.setItem("token", token); // Store token
          sessionStorage.setItem("designation", "Agent");

          Swal.fire({
            icon: "success",
            title: "Login Successful!",
            text: message,
            timer: 2000,
            showConfirmButton: false
          }).then(() => {
            window.location.href = "/agent/dashboard";
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

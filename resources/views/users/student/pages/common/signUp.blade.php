<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Student Signup with OTP</title>
  <!-- Custom Signup CSS -->
  <link rel="stylesheet" href="{{ asset('css/signup.css') }}">
  <!-- Font Awesome for icons -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" 
        integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg==" 
        crossorigin="anonymous" referrerpolicy="no-referrer" />
  <!-- SweetAlert2 for alerts -->
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
  <div class="container">
    <!-- Left Section -->
    <div class="left-section">
      <img src="{{ asset('assets/web_assets/login.png') }}" alt="Signup Image">
    </div>
    <!-- Right Section -->
    <div class="right-section">
      <div class="form-container">
        <h3>Student Sign Up</h3>
        <form id="signupForm">
          <!-- Name Field -->
          <div class="form-group">
            <label for="name">Name</label>
            <div class="input-group">
              <span class="input-group-text"><i class="fa-solid fa-user"></i></span>
              <input type="text" id="name" name="name" placeholder="Enter your name" required>
            </div>
          </div>
          <!-- Email Field -->
          <div class="form-group">
            <label for="email">Email</label>
            <div class="input-group">
              <span class="input-group-text"><i class="fa-solid fa-envelope"></i></span>
              <input type="email" id="email" name="email" placeholder="Enter your email" required>
              <button type="button" class="otp-btn" onclick="sendOtp('email')">OTP</button>
            </div>
            <input type="text" id="email_otp" placeholder="Enter Email OTP" class="otp-input" style="display: none;">
          </div>
          <!-- Phone Field -->
          <div class="form-group">
            <label for="phone">Phone</label>
            <div class="input-group">
              <span class="input-group-text"><i class="fa-solid fa-phone"></i></span>
              <input type="text" id="phone" name="phone" placeholder="Enter your phone number" maxlength="10" required>
              <button type="button" class="otp-btn" onclick="sendOtp('phone')">OTP</button>
            </div>
            <input type="text" id="phone_otp" placeholder="Enter Phone OTP" class="otp-input" style="display: none;">
          </div>
          <!-- Password Field -->
          <div class="form-group">
            <label for="password">Password</label>
            <div class="input-group">
              <span class="input-group-text"><i class="fa-solid fa-lock"></i></span>
              <input type="password" id="password" name="password" placeholder="Enter your password" required>
              <button type="button" class="toggle-btn" onclick="togglePasswordVisibility('password')">
                <i class="fa-regular fa-eye"></i>
              </button>
            </div>
          </div>
          <!-- Confirm Password Field -->
          <div class="form-group">
            <label for="password_confirmation">Confirm Password</label>
            <div class="input-group">
              <span class="input-group-text"><i class="fa-solid fa-lock"></i></span>
              <input type="password" id="password_confirmation" name="password_confirmation" placeholder="Confirm your password" required>
              <button type="button" class="toggle-btn" onclick="togglePasswordVisibility('password_confirmation')">
                <i class="fa-regular fa-eye"></i>
              </button>
            </div>
          </div>
          <!-- Captcha Section -->
          <div class="form-group">
            <label for="captcha_input">Captcha</label>
            <div class="captcha-wrapper">
              <span id="captcha" class="captcha-text">12345</span>
              <button type="button" class="refresh-btn" onclick="refreshCaptcha()">
                <i class="fa-solid fa-rotate"></i>
              </button>
            </div>
            <input type="text" id="captcha_input" name="captcha_input" class="captcha-input" placeholder="Enter Captcha" required>
          </div>
          <!-- Signup Button -->
          <button type="submit" class="btn-submit" id="signupButton">
            <span id="signupText">Signup</span>
            <span id="signupSpinner" class="spinner d-none" role="status" aria-hidden="true"></span>
          </button>
          <!-- Login Link -->
          <div class="login-link">
            <p>Already have an account? <a href="/student/login" class="text-primary">Login</a></p>
          </div>
        </form>
      </div>
    </div>
  </div>

  <script>
    let generatedOtps = { email: '', phone: '' };

    function togglePasswordVisibility(fieldId) {
      const field = document.getElementById(fieldId);
      field.type = field.type === 'password' ? 'text' : 'password';
    }

    function refreshCaptcha() {
      const captcha = Math.random().toString(36).substring(2, 7).toUpperCase();
      document.getElementById('captcha').innerText = captcha;
    }

    function sendOtp(type) {
      const inputField = document.getElementById(type);
      const otpField = document.getElementById(`${type}_otp`);
      const value = inputField.value;
      if (!value) {
        Swal.fire({
          icon: 'error',
          title: 'Error',
          text: `Please enter your ${type} before requesting an OTP.`
        });
        return;
      }
      generatedOtps[type] = Math.floor(100000 + Math.random() * 900000).toString();
      console.log(`Generated ${type} OTP:`, generatedOtps[type]);
      Swal.fire({
        icon: 'success',
        title: `${type.charAt(0).toUpperCase() + type.slice(1)} OTP Sent`,
        text: `An OTP has been sent to your ${type}.`
      });
      otpField.style.display = 'block';
    }

    document.getElementById('signupForm').addEventListener('submit', async function(event) {
      event.preventDefault();
      const signupButton = document.getElementById("signupButton");
      const signupText = document.getElementById("signupText");
      const signupSpinner = document.getElementById("signupSpinner");
      signupButton.disabled = true;
      signupText.textContent = "Signing up...";
      signupSpinner.classList.remove("d-none");
      
      const emailOtp = document.getElementById('email_otp').value;
      const phoneOtp = document.getElementById('phone_otp').value;
      const captchaInput = document.getElementById('captcha_input').value;
      const captcha = document.getElementById('captcha').innerText;
      
      if (captchaInput !== captcha) {
        Swal.fire({
          icon: 'error',
          title: 'Captcha Error',
          text: 'Invalid Captcha.'
        });
        resetSignupButton();
        return;
      }
      if (emailOtp !== generatedOtps.email) {
        Swal.fire({
          icon: 'error',
          title: 'Error',
          text: 'Invalid Email OTP.'
        });
        resetSignupButton();
        return;
      }
      if (phoneOtp !== generatedOtps.phone) {
        Swal.fire({
          icon: 'error',
          title: 'Error',
          text: 'Invalid Phone OTP.'
        });
        resetSignupButton();
        return;
      }
      Swal.fire({
        title: 'Processing...',
        allowOutsideClick: false,
        didOpen: () => { Swal.showLoading(); }
      });
      const formData = {
        name: document.getElementById('name').value,
        email: document.getElementById('email').value,
        phone: document.getElementById('phone').value,
        password: document.getElementById('password').value,
        password_confirmation: document.getElementById('password_confirmation').value
      };
      try {
        const response = await fetch('/api/student/signup', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify(formData)
        });
        Swal.close();
        const data = await response.json();
        if (response.ok) {
          Swal.fire({
            icon: 'success',
            title: 'Signup Successful',
            text: data.message,
            timer: 2000,
            showConfirmButton: false
          }).then(() => { window.location.href = '/student/login'; });
        } else {
          Swal.fire({
            icon: 'error',
            title: 'Signup Failed',
            text: data.message || 'Please check your inputs.'
          });
          resetSignupButton();
        }
      } catch (error) {
        Swal.fire({
          icon: 'error',
          title: 'Error',
          text: error.message
        });
        resetSignupButton();
      }
    });

    function resetSignupButton() {
      const signupButton = document.getElementById("signupButton");
      const signupText = document.getElementById("signupText");
      const signupSpinner = document.getElementById("signupSpinner");
      signupButton.disabled = false;
      signupText.textContent = "Signup";
      signupSpinner.classList.add("d-none");
    }
    refreshCaptcha();
  </script>
</body>
</html>

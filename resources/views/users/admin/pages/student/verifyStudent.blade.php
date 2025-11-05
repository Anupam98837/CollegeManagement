<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Student Register</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
    <div class="d-flex">
        <!-- Sidebar -->
        <div>
            @include('Layout.sidebar')
        </div>

        <!-- Main Content -->
        <div class="w-100 main-com">
            @include('Layout.header')

            <div class="container mt-4">
                <p class="mb-4 text-secondary text-14">Student <i class="fa-solid fa-angle-right"></i> <span class="text-primary">Register Student</span></p>
                <form id="studentRegisterForm" class="bg-white p-4 rounded">
                    <!-- Name Field -->
                    <div class="row g-3 align-items-end">
                        <div class="col-md-12">
                            <label for="name" class="form-label text-13">Name <span class="text-danger">*</span></label>
                            <input type="text" id="name" name="name" class="form-control placeholder-14 text-13" placeholder="Enter your name" required>
                        </div>
                    </div>

                    <!-- Email and Phone Fields -->
                    <div class="row g-3 mt-2">
                        <div class="col-md-6">
                            <label for="email" class="form-label text-13">Email <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="email" id="email" name="email" class="form-control placeholder-14 text-13" placeholder="Enter your email" required>
                                <button type="button" class="btn btn-outline-secondary text-13" onclick="sendEmailOtp()">
                                    <span id="emailOtpButtonText">Send OTP</span>
                                    <span id="emailOtpSpinner" class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                                </button>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label for="phone" class="form-label text-13">Phone Number <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="text" id="phone" name="phone" class="form-control placeholder-14 text-13" placeholder="Enter your phone number" required>
                                <button type="button" class="btn btn-outline-secondary text-13" onclick="sendPhoneOtp()">
                                    <span id="phoneOtpButtonText">Send OTP</span>
                                    <span id="phoneOtpSpinner" class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- OTP Fields -->
                    <div class="row g-3 mt-2 d-none" id="emailOtpField">
                        <div class="col-md-12">
                            <label for="emailOtp" class="form-label text-13">Email OTP <span class="text-danger">*</span></label>
                            <input type="text" id="emailOtp" name="emailOtp" class="form-control placeholder-14 text-13" placeholder="Enter email OTP">
                        </div>
                    </div>
                    <div class="row g-3 mt-2 d-none" id="phoneOtpField">
                        <div class="col-md-12">
                            <label for="phoneOtp" class="form-label text-13">Phone OTP <span class="text-danger">*</span></label>
                            <input type="text" id="phoneOtp" name="phoneOtp" class="form-control placeholder-14 text-13" placeholder="Enter phone OTP">
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="mt-4">
                        <button type="submit" id="submitButton" class="btn btn-outline-primary text-13">
                            <span id="buttonText">Register</span>
                            <span id="buttonSpinner" class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
  if (!sessionStorage.getItem("token")) {
    // Redirect to blank path or your preferred path if token is missing.
    window.location.href = "/";
  }
});
        function toggleButtonState(buttonId, spinnerId, disable) {
            const button = document.getElementById(buttonId);
            const spinner = document.getElementById(spinnerId);

            if (disable) {
                button.setAttribute('disabled', 'disabled');
                spinner.classList.remove('d-none');
            } else {
                button.removeAttribute('disabled');
                spinner.classList.add('d-none');
            }
        }

        function sendEmailOtp() {
            const email = document.getElementById('email').value;
            if (!email) {
                Swal.fire('Error', 'Please enter a valid email.', 'error');
                return;
            }

            toggleButtonState('emailOtpButtonText', 'emailOtpSpinner', true);

            setTimeout(() => {
                Swal.fire('Success', 'OTP sent to your email.', 'success');
                document.getElementById('emailOtpField').classList.remove('d-none');
                toggleButtonState('emailOtpButtonText', 'emailOtpSpinner', false);
            }, 1000);
        }

        function sendPhoneOtp() {
            const phone = document.getElementById('phone').value;
            if (!phone || phone.length !== 10) {
                Swal.fire('Error', 'Please enter a valid phone number.', 'error');
                return;
            }

            toggleButtonState('phoneOtpButtonText', 'phoneOtpSpinner', true);

            setTimeout(() => {
                Swal.fire('Success', 'OTP sent to your phone number.', 'success');
                document.getElementById('phoneOtpField').classList.remove('d-none');
                toggleButtonState('phoneOtpButtonText', 'phoneOtpSpinner', false);
            }, 1000);
        }

        document.getElementById('studentRegisterForm').addEventListener('submit', function (e) {
            e.preventDefault();

            const name = document.getElementById('name').value;
            const email = document.getElementById('email').value;
            const emailOtp = document.getElementById('emailOtp').value;
            const phone = document.getElementById('phone').value;
            const phoneOtp = document.getElementById('phoneOtp').value;

            if (!name || !email || !emailOtp || !phone || !phoneOtp) {
                Swal.fire('Error', 'Please fill all fields and verify OTPs.', 'error');
                return;
            }

            toggleButtonState('submitButton', 'buttonSpinner', true);

            setTimeout(() => {
                Swal.fire('Success', 'Registration completed successfully.', 'success');
                document.getElementById('studentRegisterForm').reset();
                document.getElementById('emailOtpField').classList.add('d-none');
                document.getElementById('phoneOtpField').classList.add('d-none');
                toggleButtonState('submitButton', 'buttonSpinner', false);
            }, 1000);
        });
    </script>
</body>
</html>

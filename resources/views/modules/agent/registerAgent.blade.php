<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agent Register</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        .step { display: none; }
        .step.active { display: block; }
        .navigation { display: flex; justify-content: space-between; }
        .form-row { display: flex; flex-wrap: wrap; gap: 20px; }
        .form-row .form-group { flex: 1; min-width: 300px; }
    </style>
</head>
<body>
            <div class="container mt-4">
                <p class=" mb-4 text-secondary text-14">Agent <i class="fa-solid fa-angle-right"></i> <span class="text-primary ">Register Agent</span> </p>
                <form id="agentRegisterForm" enctype="multipart/form-data" class="bg-white p-4 rounded" novalidate>
                    <!-- Progress Bar -->
                    <div class="progress mb-4">
                        <div class="progress-bar" id="progressBar" style="width: 33%;" aria-valuenow="33" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>

                    <!-- Step 1: Basic Details -->
                    <div class="step active" id="step1">
                        <h4 class="text-13 text-secondary">Basic Details</h4>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="name" class="form-label text-13">Name <span class="text-danger">*</span></label>
                                <input type="text" id="name" name="name" class="form-control text-13 placeholder-14" placeholder="Enter Name" required>
                            </div>
                            <div class="form-group">
                                <label for="email" class="form-label text-13">Email Id <span class="text-danger">*</span></label>
                                <input type="email" id="email" name="email" class="form-control text-13 placeholder-14" placeholder="Enter Email" required>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="mobile" class="form-label text-13">Mobile <span class="text-danger">*</span></label>
                                <input type="number" id="mobile" name="mobile" class="form-control text-13 placeholder-14" placeholder="Enter Mobile" required>
                            </div>
                            <div class="form-group">
                                <label for="whatsapp" class="form-label text-13">WhatsApp Number <span class="text-danger">*</span></label>
                                <input type="number" id="whatsapp" name="whatsapp" class="form-control text-13 placeholder-14" placeholder="Enter WhatsApp Number" required>
                                <div class="form-check mt-2">
                                    <input class="form-check-input" type="checkbox" id="sameAsMobile" onchange="toggleWhatsAppNumber()">
                                    <label class="form-check-label text-13" for="sameAsMobile">Same as Mobile</label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Step 2: Address -->
                    <div class="step" id="step2">
                        <h4 class="text-13 text-secondary">Address</h4>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="street" class="form-label text-13">Street Name <span class="text-danger">*</span></label>
                                <input type="text" id="street" name="street" class="form-control text-13 placeholder-14" placeholder="Enter Street Name" required>
                            </div>
                            <div class="form-group">
                                <label for="postOffice" class="form-label text-13">Post Office <span class="text-danger">*</span></label>
                                <input type="text" id="postOffice" name="postOffice" class="form-control text-13 placeholder-14" placeholder="Enter Post Office" required>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="policeStation" class="form-label text-13">Police Station <span class="text-danger">*</span></label>
                                <input type="text" id="policeStation" name="policeStation" class="form-control text-13 placeholder-14" placeholder="Enter Police Station" required>
                            </div>
                            <div class="form-group">
                                <label for="city" class="form-label text-13">City <span class="text-danger">*</span></label>
                                <input type="text" id="city" name="city" class="form-control text-13 placeholder-14" placeholder="Enter City" required>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="state" class="form-label text-13">State <span class="text-danger">*</span></label>
                                <select id="state" name="state" class="form-control text-13 placeholder-14" required>
                                    <option value="" disabled selected>Select State</option>
                                    <option value="Andhra Pradesh">Andhra Pradesh</option>
                                    <option value="Arunachal Pradesh">Arunachal Pradesh</option>
                                    <option value="Assam">Assam</option>
                                    <option value="Bihar">Bihar</option>
                                    <option value="Chhattisgarh">Chhattisgarh</option>
                                    <option value="Goa">Goa</option>
                                    <option value="Gujarat">Gujarat</option>
                                    <option value="Haryana">Haryana</option>
                                    <option value="Himachal Pradesh">Himachal Pradesh</option>
                                    <option value="Jharkhand">Jharkhand</option>
                                    <option value="Karnataka">Karnataka</option>
                                    <option value="Kerala">Kerala</option>
                                    <option value="Madhya Pradesh">Madhya Pradesh</option>
                                    <option value="Maharashtra">Maharashtra</option>
                                    <option value="Manipur">Manipur</option>
                                    <option value="Meghalaya">Meghalaya</option>
                                    <option value="Mizoram">Mizoram</option>
                                    <option value="Nagaland">Nagaland</option>
                                    <option value="Odisha">Odisha</option>
                                    <option value="Punjab">Punjab</option>
                                    <option value="Rajasthan">Rajasthan</option>
                                    <option value="Sikkim">Sikkim</option>
                                    <option value="Tamil Nadu">Tamil Nadu</option>
                                    <option value="Telangana">Telangana</option>
                                    <option value="Tripura">Tripura</option>
                                    <option value="Uttar Pradesh">Uttar Pradesh</option>
                                    <option value="Uttarakhand">Uttarakhand</option>
                                    <option value="West Bengal">West Bengal</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="country" class="form-label text-13">Country <span class="text-danger">*</span></label>
                                <select id="country" name="country" class="form-control text-13 placeholder-14" required>
                                    <option value="" disabled selected>Select Country</option>
                                    <option value="India">India</option>
                                    <option value="United States">United States</option>
                                    <option value="United Kingdom">United Kingdom</option>
                                    <option value="Australia">Australia</option>
                                    <option value="Canada">Canada</option>
                                    <option value="Germany">Germany</option>
                                    <option value="France">France</option>
                                    <option value="China">China</option>
                                    <option value="Japan">Japan</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="pincode" class="form-label text-13">Pincode <span class="text-danger">*</span></label>
                                <input type="text" id="pincode" name="pincode" class="form-control text-13 placeholder-14" placeholder="Enter Pincode" required>
                            </div>
                        </div>
                    </div>
                    

                    <!-- Step 3: Documents -->
                    <div class="step" id="step3">
                        <h4 class="text-13 text-secondary">PAN and Document Upload</h4>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="pan" class="form-label text-13">PAN Number <span class="text-danger">*</span></label>
                                <input type="text" id="pan" name="pan" class="form-control text-13 placeholder-14" placeholder="Enter PAN Number" required>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="panCard" class="form-label text-13">PAN Card <span class="text-danger">*</span></label>
                                <input type="file" id="panCard" name="panCard" class="form-control text-13"  accept=".jpeg, .jpg, .png, .pdf" required>
                                <small class="text-muted">Accepted formats: JPEG, JPG, PNG, PDF</small>
                            </div>
                            <div class="form-group">
                                <label for="aadharCard" class="form-label text-13">Aadhar Card <span class="text-danger">*</span></label>
                                <input type="file" id="aadharCard" name="aadharCard" class="form-control text-13"  accept=".jpeg, .jpg, .png, .pdf" required>
                                <small class="text-muted">Accepted formats: JPEG, JPG, PNG, PDF</small>
                            </div>
                        </div>
                        <!-- Confirmation Checkbox -->
                        <div class="form-check mt-4">
                            <input class="form-check-input" type="checkbox" id="confirmCheckbox" onchange="toggleSubmitButton()">
                            <label class="form-check-label text-13" for="confirmCheckbox">
                                I confirm that the details provided are accurate to the best of my knowledge.
                            </label>
                        </div>
                    </div>

                    <!-- Navigation Buttons -->
                    <div class="navigation mt-4">
                        <button type="button" class="btn btn-outline-secondary text-13" id="prevBtn" onclick="changeStep(-1)" style="display: none;">Previous</button>
                        <button type="button" class="btn btn-outline-primary text-13" id="nextBtn" onclick="changeStep(1)">Next</button>
                        <button type="submit" class="btn btn-success text-13" id="agent_ragister_submitBtn" style="display: none;">Submit</button>
                    </div>
                </form>
            </div>
    {{-- <script src="{{ asset('js/Admin/Agent_Register.js') }}"></script> --}}
    <script src="{{ asset('js/Components/registerAgent.js') }}"></script>


    <script>
        document.addEventListener("DOMContentLoaded", function() {
  if (!sessionStorage.getItem("token")) {
    // Redirect to blank path or your preferred path if token is missing.
    window.location.href = "/";
  }
});
    </script>
</body>
</html>

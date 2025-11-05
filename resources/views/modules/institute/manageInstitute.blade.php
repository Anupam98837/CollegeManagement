<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Add Institution</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body>
            <div class="container mt-4 mb-4 ">

                <form id="addInstitutionForm" class="bg-white p-4 rounded position-relative d-none" enctype="multipart/form-data">
                    <p class="mb-4 text-secondary text-14"><i class="fa-solid fa-angle-right"></i> <span class="text-primary admin_add_Institution_text">Add Institution</span></p>
                    <button type="button" class="btn btn-danger text-13 position-absolute top-0 end-0 m-3"  onclick="hideAddInstitutionForm()"><i class="fa-solid fa-xmark"></i></button>


                    <!-- CSRF Token -->
                    @csrf

                    <!-- Form Fields -->
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="campusDropdown" class="form-label text-13">Campus <span class="text-danger">*</span></label>
                            <select id="campusDropdown" name="campus_id" class="form-control placeholder-14 text-13" required>
                                <option value="" disabled selected>Select Campus</option>
                                <!-- Options will be dynamically added here -->
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="institutionName" class="form-label text-13">Institution Name <span class="text-danger">*</span></label>
                            <input type="text" id="institutionName" name="institution_name" class="form-control placeholder-14 text-13" placeholder="Enter Institution Name" required>
                        </div>
                    </div>
                    <div class="row g-3 mt-2">
                        <div class="col-md-4">
                            <label for="institutionLogo" class="form-label text-13">Upload Institution Logo</label>
                            <input 
                                type="file" 
                                id="institutionImage" 
                                name="institution_logo" 
                                class="form-control placeholder-14 text-13" 
                                accept="image/*"
                            >
                        </div>                        
                        <div class="col-md-4">
                            <label for="institutionShortCode" class="form-label text-13">
                                Institution Short Code <span class="text-danger">*</span>
                            </label>
                            <input 
                                type="text" 
                                id="institutionShortCode" 
                                name="institution_short_code" 
                                class="form-control placeholder-14 text-13" 
                                placeholder="Enter Short Code (max 10 chars)" 
                                maxlength="10" 
                                required
                            >
                        </div>                        
                        <div class="col-md-4">
                            <label for="type" class="form-label text-13">Type <span class="text-danger">*</span></label>
                            <select id="type" name="type" class="form-control placeholder-14 text-13" required>
                                <option value="" disabled selected>Select Type</option>
                                <option value="School">School</option>
                                <option value="College">College</option>
                            </select>
                        </div>
                        
                    </div>
                    <div class="row g-3 mt-2">
                        <div class="col-md-6">
                            <label for="url" class="form-label text-13">URL</label>
                            <input type="url" id="url" name="url" class="form-control placeholder-14 text-13" placeholder="Enter URL">
                        </div>
                        <div class="col-md-6">
                            <label for="street" class="form-label text-13">Street Name <span class="text-danger">*</span></label>
                            <input type="text" id="street" name="street" class="form-control placeholder-14 text-13" placeholder="Enter Street Name" required>
                        </div>
                    </div>
                    <div class="row g-3 mt-2">
                        <div class="col-md-6">
                            <label for="po" class="form-label text-13">Post Office (PO) <span class="text-danger">*</span></label>
                            <input type="text" id="po" name="po" class="form-control placeholder-14 text-13" placeholder="Enter Post Office" required>
                        </div>
                        <div class="col-md-6">
                            <label for="ps" class="form-label text-13">Police Station (PS) <span class="text-danger">*</span></label>
                            <input type="text" id="ps" name="ps" class="form-control placeholder-14 text-13" placeholder="Enter Police Station" required>
                        </div>
                    </div>
                    <div class="row g-3 mt-2">
                        <div class="col-md-6">
                            <label for="city" class="form-label text-13">City <span class="text-danger">*</span></label>
                            <input type="text" id="city" name="city" class="form-control placeholder-14 text-13" placeholder="Enter City" required>
                        </div>
                        <div class="col-md-6">
                            <label for="state" class="form-label text-13">State <span class="text-danger">*</span></label>
                            <select id="state" name="state" class="form-control placeholder-14 text-13" required>
                                <option value="" disabled selected>Select State</option>
                                <option value="Andaman and Nicobar Islands">Andaman and Nicobar Islands</option>
                                <option value="Andhra Pradesh">Andhra Pradesh</option>
                                <option value="Arunachal Pradesh">Arunachal Pradesh</option>
                                <option value="Assam">Assam</option>
                                <option value="Bihar">Bihar</option>
                                <option value="Chandigarh">Chandigarh</option>
                                <option value="Chhattisgarh">Chhattisgarh</option>
                                <option value="Dadra and Nagar Haveli and Daman and Diu">Dadra and Nagar Haveli and Daman and Diu</option>
                                <option value="Delhi">Delhi</option>
                                <option value="Goa">Goa</option>
                                <option value="Gujarat">Gujarat</option>
                                <option value="Haryana">Haryana</option>
                                <option value="Himachal Pradesh">Himachal Pradesh</option>
                                <option value="Jammu and Kashmir">Jammu and Kashmir</option>
                                <option value="Jharkhand">Jharkhand</option>
                                <option value="Karnataka">Karnataka</option>
                                <option value="Kerala">Kerala</option>
                                <option value="Ladakh">Ladakh</option>
                                <option value="Lakshadweep">Lakshadweep</option>
                                <option value="Madhya Pradesh">Madhya Pradesh</option>
                                <option value="Maharashtra">Maharashtra</option>
                                <option value="Manipur">Manipur</option>
                                <option value="Meghalaya">Meghalaya</option>
                                <option value="Mizoram">Mizoram</option>
                                <option value="Nagaland">Nagaland</option>
                                <option value="Odisha">Odisha</option>
                                <option value="Puducherry">Puducherry</option>
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
                    </div>
                    <div class="row g-3 mt-2">
                        <div class="col-md-6">
                            <label for="country" class="form-label text-13">Country <span class="text-danger">*</span></label>
                            <select id="country" name="country" class="form-control placeholder-14 text-13" required>
                                <option value="" disabled selected>Select Country</option>
                                <option value="India">India</option>
                                <!-- Add other countries if required -->
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="pincode" class="form-label text-13">Pincode <span class="text-danger">*</span></label>
                            <input type="text" id="pincode" name="pincode" class="form-control placeholder-14 text-13" placeholder="Enter Pincode" required>
                        </div>
                        
                    </div>
                    <div class="row g-3 mt-2">
                        <div class="col-md-6">
                            <label for="contact" class="form-label text-13">Contact No</label>
                            <input type="text" id="contact" name="contact_no" class="form-control placeholder-14 text-13" placeholder="Enter Contact Number">
                        </div>
                        <div class="col-md-6">
                            <label for="email" class="form-label text-13">Email ID</label>
                            <input type="email" id="email" name="email_id" class="form-control placeholder-14 text-13" placeholder="Enter Email ID">
                        </div>
                    </div>
                    <button type="submit" id="submitButton" class="btn btn-outline-primary text-13 mt-4">
                        <span id="buttonText">Add Institution</span>
                        <span id="buttonSpinner" class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                    </button>
                </form>
                <form id="aei_editInstitutionForm" class="bg-white p-4 rounded d-none">
                    @csrf
                    <!-- Hidden Field for ObjectId -->
                    <input type="hidden" id="aei_editInstitutionId" name="institution_id">
                
                    <!-- Campus Dropdown -->
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="aei_editCampusDropdown" class="form-label text-13">Campus <span class="text-danger">*</span></label>
                            <select id="aei_editCampusDropdown" name="campus_id" class="form-control placeholder-14 text-13" required>
                                <option value="" disabled selected>Select Campus</option>
                                <!-- Options will be dynamically added -->
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="aei_editInstitutionName" class="form-label text-13">Institution Name <span class="text-danger">*</span></label>
                            <input type="text" id="aei_editInstitutionName" name="institution_name" class="form-control placeholder-14 text-13" placeholder="Enter Institution Name" required>
                        </div>
                    </div>
                
                    <!-- Institution Type -->
                    <div class="row g-3 mt-2">
                        <div class="col-md-6">
                            <label for="aei_editType" class="form-label text-13">Type <span class="text-danger">*</span></label>
                            <select id="aei_editType" name="type" class="form-control placeholder-14 text-13" required>
                                <option value="" disabled selected>Select Type</option>
                                <option value="School">School</option>
                                <option value="College">College</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="aei_editUrl" class="form-label text-13">URL</label>
                            <input type="url" id="aei_editUrl" name="url" class="form-control placeholder-14 text-13" placeholder="Enter URL">
                        </div>
                    </div>
                
                    <!-- Address Details -->
                    <div class="row g-3 mt-2">
                        <div class="col-md-6">
                            <label for="aei_editStreet" class="form-label text-13">Street Name <span class="text-danger">*</span></label>
                            <input type="text" id="aei_editStreet" name="street" class="form-control placeholder-14 text-13" placeholder="Enter Street Name" required>
                        </div>
                        <div class="col-md-6">
                            <label for="aei_editPo" class="form-label text-13">Post Office (PO) <span class="text-danger">*</span></label>
                            <input type="text" id="aei_editPo" name="po" class="form-control placeholder-14 text-13" placeholder="Enter Post Office" required>
                        </div>
                    </div>
                    <div class="row g-3 mt-2">
                        <div class="col-md-6">
                            <label for="aei_editPs" class="form-label text-13">Police Station (PS) <span class="text-danger">*</span></label>
                            <input type="text" id="aei_editPs" name="ps" class="form-control placeholder-14 text-13" placeholder="Enter Police Station" required>
                        </div>
                        <div class="col-md-6">
                            <label for="aei_editCity" class="form-label text-13">City <span class="text-danger">*</span></label>
                            <input type="text" id="aei_editCity" name="city" class="form-control placeholder-14 text-13" placeholder="Enter City" required>
                        </div>
                    </div>
                    <div class="row g-3 mt-2">
                        <div class="col-md-6">
                            <label for="aei_editState" class="form-label text-13">State <span class="text-danger">*</span></label>
                            <select id="aei_editState" name="state" class="form-control placeholder-14 text-13" required>
                                <option value="" disabled selected>Select State</option>
                                <option value="Andaman and Nicobar Islands">Andaman and Nicobar Islands</option>
                                <option value="Andhra Pradesh">Andhra Pradesh</option>
                                <option value="Arunachal Pradesh">Arunachal Pradesh</option>
                                <option value="Assam">Assam</option>
                                <option value="Bihar">Bihar</option>
                                <option value="Chandigarh">Chandigarh</option>
                                <option value="Chhattisgarh">Chhattisgarh</option>
                                <option value="Dadra and Nagar Haveli and Daman and Diu">Dadra and Nagar Haveli and Daman and Diu</option>
                                <option value="Delhi">Delhi</option>
                                <option value="Goa">Goa</option>
                                <option value="Gujarat">Gujarat</option>
                                <option value="Haryana">Haryana</option>
                                <option value="Himachal Pradesh">Himachal Pradesh</option>
                                <option value="Jammu and Kashmir">Jammu and Kashmir</option>
                                <option value="Jharkhand">Jharkhand</option>
                                <option value="Karnataka">Karnataka</option>
                                <option value="Kerala">Kerala</option>
                                <option value="Ladakh">Ladakh</option>
                                <option value="Lakshadweep">Lakshadweep</option>
                                <option value="Madhya Pradesh">Madhya Pradesh</option>
                                <option value="Maharashtra">Maharashtra</option>
                                <option value="Manipur">Manipur</option>
                                <option value="Meghalaya">Meghalaya</option>
                                <option value="Mizoram">Mizoram</option>
                                <option value="Nagaland">Nagaland</option>
                                <option value="Odisha">Odisha</option>
                                <option value="Puducherry">Puducherry</option>
                                <option value="Punjab">Punjab</option>
                                <option value="Rajasthan">Rajasthan</option>
                                <option value="Sikkim">Sikkim</option>
                                <option value="Tamil Nadu">Tamil Nadu</option>
                                <option value="Telangana">Telangana</option>
                                <option value="Tripura">Tripura</option>
                                <option value="Uttar Pradesh">Uttar Pradesh</option>
                                <option value="Uttarakhand">Uttarakhand</option>
                                <option value="West Bengal">West Bengal</option>
                                <!-- Add other states -->
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="aei_editCountry" class="form-label text-13">Country <span class="text-danger">*</span></label>
                            <select id="aei_editCountry" name="country" class="form-control placeholder-14 text-13" required>
                                <option value="" disabled selected>Select Country</option>
                                <option value="India">India</option>
                                <!-- Add other countries -->
                            </select>
                        </div>
                    </div>
                
                    <!-- Pincode, Contact No, and Email -->
                    <div class="row g-3 mt-2">
                        <div class="col-md-6">
                            <label for="aei_editPincode" class="form-label text-13">Pincode <span class="text-danger">*</span></label>
                            <input type="text" id="aei_editPincode" name="pincode" class="form-control placeholder-14 text-13" placeholder="Enter Pincode" required>
                        </div>
                        <div class="col-md-6">
                            <label for="aei_editContact" class="form-label text-13">Contact No</label>
                            <input type="text" id="aei_editContact" name="contact_no" class="form-control placeholder-14 text-13" placeholder="Enter Contact Number">
                        </div>
                    </div>
                    <div class="row g-3 mt-2">
                        <div class="col-md-6">
                            <label for="aei_editEmail" class="form-label text-13">Email ID</label>
                            <input type="email" id="aei_editEmail" name="email_id" class="form-control placeholder-14 text-13" placeholder="Enter Email ID">
                        </div>
                    </div>
                
                    <!-- Submit and Cancel Buttons -->
                    <div class="mt-4">
                        <button type="submit" id="aei_editSubmitButton" class="btn btn-outline-primary text-13">
                            <span id="aei_editButtonText">Update Institution</span>
                            <span id="aei_editButtonSpinner" class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                        </button>
                        <button type="button" onclick="cancelEditInstitution()" class="btn btn-outline-danger text-13 ms-2">Cancel</button>
                    </div>
                </form>
                
                
                <p class="mt-4 text-secondary text-14"><i class="fa-solid fa-angle-right"></i> <span class="text-primary">All Institutions</span></p>
                <div class="bg-white  p-4 rounded">
                    <div class="row mb-3  align-items-center justify-content-between">
                        <div class="col-md-6 position-relative">
                            <input type="text" id="institutionSearch" class="form-control placeholder-14 text-13 ps-5" placeholder="Search by Campus ID, Institution Name, or Type">
                            <i class="fa-solid fa-search position-absolute text-secondary" style="top: 50%; left: 15px; transform: translateY(-50%);"></i>
                        </div>
                        <div class="text-end col-md-6">
                            <button id="showAddInstitutionForm" class="btn btn-outline-primary text-13">
                                <i class="fa-solid fa-plus"></i> Institution
                            </button>
                        </div>
                    </div>
                    <div class="table-responsive ">
                                        
                        <table class="table table-striped text-center">
                            <thead>
                                <tr>
                                    <th class="text-13 text-secondary">Logo</th>
                                    <th class="text-13 text-secondary">Campus ID</th>
                                    <th class="text-13 text-secondary">Institution_Name</th>
                                    <th class="text-13 text-secondary">Type</th>
                                    <th class="text-13 text-secondary">Address</th>
                                    <th class="text-13 text-secondary">URL</th>
                                    <th class="text-13 text-secondary">Contact_No</th>
                                    <th class="text-13 text-secondary">Email_ID</th>
                                    <th class="text-14 text-secondary">State</th>
                                    <th class="text-13 text-secondary">Created_At</th>
                                    <th class="text-13 text-secondary">Actions</th>
                                </tr>
                                
                            </thead>
                            <tbody id="institutionTableBody">
                                <tr>
                                    <td colspan="100%" class="text-center">
                                      <div class="spinner-border text-primary" role="status">
                                        <span class="visually-hidden">Loading...</span>
                                      </div>
                                    </td>
                                  </tr>
                            </tbody>
                        </table>
                        <div id="institutionPaginationContainer" class="mt-3 d-flex justify-content-center"></div>

                    </div>
                </div>
            </div>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="{{ asset('js/Components/manageInstitution.js') }}"></script>
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



<div class="container mt-4">
    @if (session('success'))
        <div class="alert alert-success text-13">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger text-13">
            {{ session('error') }}
        </div>
    @endif
</div>
<form class="admin_ragister_student_form bg-white"  id="multiStepForm"  enctype="multipart/form-data" novalidate>
    @csrf
    <!-- Animated Progress Bar with Step Details -->
    <div class="progress-container">
        <div class="progress">
            <div class="progress-bar" id="progressBarAnimated" role="progressbar" style="width: 25%;" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
        </div>
        <div class="step-details mt-2 d-flex justify-content-between">
            <span id="step1Label" class="text-primary progress-container-text text-13">Basic Details</span>
            <span id="step2Label" class="progress-container-text text-13">Additional Details</span>
            <span id="step3Label"  class="progress-container-text text-13">Address</span>
            <span id="step4Label"  class="progress-container-text text-13">Guidance Details</span>
            <span id="step5Label"  class="progress-container-text text-13">Academic Details</span>

        </div>
    </div>
    <!-- Step 1: Personal Details -->
    <div class="step active" id="step-1">
        {{-- <p class="text-secondary mb-3">Basic Details</p> --}}
        <div class="row g-3">
            <!-- Student Name -->
    <div class="col-md-6">
        <label for="studentName" class="form-label text-13">Student Name <span class="text-danger">*</span></label>
        <input type="text" class="form-control placeholder-14 text-13" id="studentName" name="name" placeholder="Enter student name" value="{{ old('name') }}" required>
    </div>
    <!-- Phone -->
    <div class="col-md-6">
        <label for="phone" class="form-label text-13">Phone <span class="text-danger">*</span></label>
        <input type="number" class="form-control placeholder-14 text-13" id="phone" name="phone" placeholder="Enter phone number" required>
    </div>
    <!-- Phone -->
    <div class="col-md-6">
        <label for="alternative-phone" class="form-label text-13">Alternative Phone</label>
        <input type="number" class="form-control placeholder-14 text-13" id="alternative-phone" name="alternative-phone" placeholder="Enter alernative phone number" >
    </div>
    <!-- Email -->
    <div class="col-md-6">
        <label for="email" class="form-label text-13">Email <span class="text-danger">*</span></label>
        <input type="email" class="form-control placeholder-14 text-13" id="email" name="email" placeholder="Enter email" required>
    </div>
    <!-- Email -->
    <div class="col-md-6">
        <label for="alternative-email" class="form-label text-13">Alternative Email </label>
        <input type="email" class="form-control placeholder-14 text-13" id="alternative-email" name="alternative-email" placeholder="Enter alernative email" >
    </div>
    <div class="col-md-6">
        <label for="whatsapp-no" class="form-label text-13">Whatsapp Phone<span class="text-danger">*</span></label>
        <input type="number" class="form-control placeholder-14 text-13" id="whatsapp-no" name="whatsapp-no" placeholder="Enter whatsapp phone number" required>
    </div>

        </div>
    </div>
    <!-- Step 2: Academic Details -->
    <div class="step" id="step-2">
        {{-- <p class="text-secondary mb-3">Additional Details</p> --}}
        <div class="row g-3">
            <!-- Date of Birth -->
    <div class="col-md-6">
        <label for="dob" class="form-label text-13">Date of Birth <span class="text-danger">*</span></label>
        <input type="date" class="form-control placeholder-14 text-13" id="dob" name="date_of_birth" required>
    </div>

    <div class="col-md-6">
        <label for="place_of_birth" class="form-label text-13">Place of Birth</label>
        <input type="text" class="form-control placeholder-14 text-13" id="place_of_birth" name="place_of_birth" placeholder="Enter caste">
    </div>

    <!-- Religion -->
    <div class="col-md-6">
        <label for="religion" class="form-label text-13">Religion</label>
        <select class="form-select placeholder-14 text-13" id="religion" name="religion">
            <option value="">Select Religion</option>
            <option value="Islam">Islam</option>
            <option value="Hinduism">Hinduism</option>
            <option value="Christianity">Christianity</option>
            <option value="Buddhism">Buddhism</option>
            <option value="Sikhism">Sikhism</option>
            <option value="Other">Other</option>
        </select>
    </div>                        

    <!-- Caste -->
    <div class="col-md-6">
        <label for="caste" class="form-label text-13">Caste</label>
        <select class="form-select placeholder-14 text-13" id="caste" name="caste">
            <option value="">Select Caste</option>
            <option value="General">General</option>
            <option value="OBC">OBC</option>
            <option value="SC">SC</option>
            <option value="ST">ST</option>
            <option value="Other">Other</option>
        </select>
    </div>                        

    <!-- Blood Group -->
    <div class="col-md-6">
        <label for="bloodGroup" class="form-label text-13">Blood Group</label>
        <select class="form-select placeholder-14 text-13" id="bloodGroup" name="blood_group">
            <option value="">Select Blood Group</option>
            <option value="A+">A+</option>
            <option value="A-">A-</option>
            <option value="B+">B+</option>
            <option value="B-">B-</option>
            <option value="O+">O+</option>
            <option value="O-">O-</option>
            <option value="AB+">AB+</option>
            <option value="AB-">AB-</option>
        </select>
    </div>
    
    <!-- Identity Details -->
    <div class="col-md-6">
        <label for="identityType" class="form-label text-13">Identity Details <span class="text-danger">*</span></label>
        <select class="form-select text-13" id="identityType" name="identity_type" onchange="showIdentityInput()" required>
            <option class="text-13" value="" selected disabled>Select Identity Type</option>
            <option value="Aadhar">Aadhar</option>
            <option value="Voter ID">Voter ID</option>
            <option value="PAN">PAN</option>
        </select>
    </div>
    <!-- Dynamic Input for Identity Details -->
    <div class="col-md-6" id="identityInputDiv" style="display: none;">
        <label for="identityDetails" class="form-label text-13" id="identityLabel">Enter Details<span class="text-danger">*</span></label>
        <input type="text" class="form-control placeholder-14 text-13" id="identityDetails" name="identity_details" placeholder="Enter Identity Details" required>
    </div>
        </div>
    </div>

    <!-- Step 3: Academic Details -->
    <div class="step" id="step-3">
        {{-- <p class="text-secondary mb-3">Student Address</p> --}}
        <div class="row g-3">
            <!-- City -->
            <div class="col-md-6">
                <label for="city" class="form-label text-13">Street Name / Village <span class="text-danger">*</span></label>
                <input type="text" class="form-control placeholder-14 text-13" id="city" name="city" placeholder="Enter city" required>
            </div>
            <!-- PO (Post Office) -->
            <div class="col-md-6">
                <label for="po" class="form-label text-13">Post Office (PO) <span class="text-danger">*</span></label>
                <input type="text" class="form-control placeholder-14 text-13" id="po" name="po" placeholder="Enter Post Office" required>
            </div>

            <!-- PS (Police Station) -->
            <div class="col-md-6">
                <label for="ps" class="form-label text-13">Police Station (PS) <span class="text-danger">*</span></label>
                <input type="text" class="form-control placeholder-14 text-13" id="ps" name="ps" placeholder="Enter Police Station" required>
            </div>

            <!-- Address -->
            {{-- <div class="col-md-6">
                <label for="address" class="form-label text-13">Address <span class="text-danger">*</span></label>
                <textarea class="form-control" id="address" name="address" rows="4" placeholder="Enter address" required></textarea>
            </div> --}}
    

    <!-- State -->
    <div class="col-md-6">
        <label for="state" class="form-label text-13">State <span class="text-danger">*</span></label>
        <input type="text" class="form-control placeholder-14 text-13" id="state" name="state" placeholder="Enter state" required>
    </div>

    <!-- Country -->
    <div class="col-md-6">
        <label for="country" class="form-label text-13">Country <span class="text-danger">*</span></label>
        <input type="text" class="form-control placeholder-14 text-13" id="country" name="country" placeholder="Enter country" required>
    </div>
    <div class="col-md-6">
        <label for="pin" class="form-label text-13">Pin <span class="text-danger">*</span></label>
        <input type="number" class="form-control placeholder-14 text-13" id="pin" name="pin" placeholder="Enter Pin" required>
    </div>
        </div>
        
    </div>

   <!-- Step 4: Parents Details -->
    <div class="step" id="step-4">
        
        <div class="row g-3">
            <!-- Father's Details -->
           <!-- Father's Details -->
        
        <div class="row g-3">
            <p class="text-secondary">Father's Details</p>
            <div class="col-md-6">
                <label for="fatherName" class="form-label text-13">Name <span class="text-danger">*</span></label>
                <input type="text" class="form-control placeholder-14 text-13" id="fatherName" name="father_name" placeholder="Enter father's name" required>
            </div>
            <div class="col-md-6">
                <label for="fatherOccupation" class="form-label text-13">Occupation <span class="text-danger">*</span></label>
                <input type="text" class="form-control placeholder-14 text-13" id="fatherOccupation" name="father_occupation" placeholder="Enter occupation" required>
            </div>
            <div class="col-md-6">
                <label for="fatherPhone" class="form-label text-13">Phone No <span class="text-danger">*</span></label>
                <input type="number" class="form-control placeholder-14 text-13" id="fatherPhone" name="father_phone" placeholder="Enter phone number" required>
            </div>
            <div class="col-md-6">
                <label for="fatherEmail" class="form-label text-13">Email ID</label>
                <input type="email" class="form-control placeholder-14 text-13" id="fatherEmail" name="father_email" placeholder="Enter email ID">
            </div>
            <!-- Autofill Address Checkbox -->
            <div class="col-md-12">
                <input type="checkbox" id="fatherAutoFill" onchange="autoFillAddress('father')">
                <label for="fatherAutoFill" class="form-label  text-13">Same as Student's Address</label>
            </div>
            <!-- Father's Address -->
            <div class="col-md-6">
                <label for="fatherStreet" class="form-label text-13">Street Name / Village <span class="text-danger">*</span></label>
                <input type="text" class="form-control placeholder-14 text-13" id="fatherStreet" name="father_street" placeholder="Enter street/village"  required>
            </div>
            <div class="col-md-6">
                <label for="fatherPO" class="form-label text-13">Post Office (PO)<span class="text-danger">*</span></label>
                <input type="text" class="form-control placeholder-14 text-13" id="fatherPO" name="father_po" placeholder="Enter PO" required>
            </div>
            <div class="col-md-6">
                <label for="fatherPS" class="form-label text-13">Police Station (PS)<span class="text-danger">*</span></label>
                <input type="text" class="form-control placeholder-14 text-13" id="fatherPS" name="father_ps" placeholder="Enter PS" required>
            </div>
            <div class="col-md-6">
                <label for="fatherCity" class="form-label text-13">City<span class="text-danger">*</span></label>
                <input type="text" class="form-control placeholder-14 text-13" id="fatherCity" name="father_city" placeholder="Enter city" required>
            </div>
            <div class="col-md-6">
                <label for="fatherState" class="form-label text-13">State<span class="text-danger">*</span></label>
                <input type="text" class="form-control placeholder-14 text-13" id="fatherState" name="father_state" placeholder="Enter state" required>
            </div>
            <div class="col-md-6">
                <label for="fatherCountry" class="form-label text-13">Country<span class="text-danger">*</span></label>
                <input type="text" class="form-control placeholder-14 text-13" id="fatherCountry" name="father_country" placeholder="Enter country" required>
            </div>
            <div class="col-md-6">
                <label for="fatherPincode" class="form-label text-13">Pincode<span class="text-danger">*</span></label>
                <input type="number" class="form-control placeholder-14 text-13" id="fatherPincode" name="father_pincode" placeholder="Enter pincode" required>
            </div>
        </div>
        <hr>
        <!-- Mother's Details -->
        <p class="text-secondary">Mother's Details</p>
        <div class="row g-3">
            <div class="col-md-6">
                <label for="motherName" class="form-label text-13">Name <span class="text-danger">*</span></label>
                <input type="text" class="form-control placeholder-14 text-13" id="motherName" name="mother_name" placeholder="Enter mother's name" required>
            </div>
            <div class="col-md-6">
                <label for="motherOccupation" class="form-label text-13">Occupation <span class="text-danger">*</span></label>
                <input type="text" class="form-control placeholder-14 text-13" id="motherOccupation" name="mother_occupation" placeholder="Enter occupation" required>
            </div>
            <div class="col-md-6">
                <label for="motherPhone" class="form-label text-13">Phone No <span class="text-danger">*</span></label>
                <input type="number" class="form-control placeholder-14 text-13" id="motherPhone" name="mother_phone" placeholder="Enter phone number" required>
            </div>
            <div class="col-md-6">
                <label for="motherEmail" class="form-label text-13">Email ID</label>
                <input type="email" class="form-control placeholder-14 text-13" id="motherEmail" name="mother_email" placeholder="Enter email ID">
            </div>
            <!-- Autofill Address Checkbox -->
            <div class="col-md-12">
                <input type="checkbox" id="motherAutoFill" onchange="autoFillAddress('mother')">
                <label for="motherAutoFill" class="form-label text-13">Same as Student's Address</label>
            </div>
            <!-- Mother's Address -->
            <div class="col-md-6">
                <label for="motherStreet" class="form-label text-13">Street Name / Village<span class="text-danger">*</span></label>
                <input type="text" class="form-control placeholder-14 text-13" id="motherStreet" name="mother_street" placeholder="Enter street/village"required>
            </div>
            <div class="col-md-6">
                <label for="motherPO" class="form-label text-13">Post Office (PO)<span class="text-danger">*</span></label>
                <input type="text" class="form-control placeholder-14 text-13" id="motherPO" name="mother_po" placeholder="Enter PO" required>
            </div>
            <div class="col-md-6">
                <label for="motherPS" class="form-label text-13">Police Station (PS)<span class="text-danger">*</span></label>
                <input type="text" class="form-control placeholder-14 text-13" id="motherPS" name="mother_ps" placeholder="Enter PS" required>
            </div>
            <div class="col-md-6">
                <label for="motherCity" class="form-label text-13">City<span class="text-danger">*</span></label>
                <input type="text" class="form-control placeholder-14 text-13" id="motherCity" name="mother_city" placeholder="Enter city" required>
            </div>
            <div class="col-md-6">
                <label for="motherState" class="form-label text-13">State<span class="text-danger">*</span></label>
                <input type="text" class="form-control placeholder-14 text-13" id="motherState" name="mother_state" placeholder="Enter state" required>
            </div>
            <div class="col-md-6">
                <label for="motherCountry" class="form-label text-13">Country<span class="text-danger">*</span></label>
                <input type="text" class="form-control placeholder-14 text-13" id="motherCountry" name="mother_country" placeholder="Enter country">
            </div>
            <div class="col-md-6">
                <label for="motherPincode" class="form-label text-13">Pincode<span class="text-danger">*</span></label>
                <input type="number" class="form-control placeholder-14 text-13" id="motherPincode" name="mother_pincode" placeholder="Enter pincode" required>
            </div>
        </div>

        <hr>
            <!-- Add Local Guardian Button -->
            <div class="col-md-12">
                <button type="button" class="btn btn-outline-primary text-13" id="addGuardianBtn" onclick="toggleGuardian()"> <i class="fa-solid fa-plus"></i> Local Guardian</button>
            </div>
            {{-- <div id="localGuardian" style="display: none;" class="mt-4"> --}}
                <div class="col-md-6 admin_localGurdian_field" style="display: none;">
                    <label for="guardianName" class="form-label text-13">Name <span class="text-danger">*</span></label>
                    <input type="text" class="form-control as_localgurdian_field placeholder-14 text-13" id="guardianName" name="guardian_name" placeholder="Enter guardian's name" >
                </div>
                <div class="col-md-6 admin_localGurdian_field" style="display: none;">
                    <label for="guardianOccupation" class="form-label text-13">Occupation <span class="text-danger">*</span></label>
                    <input type="text" class="form-control as_localgurdian_field placeholder-14 text-13" id="guardianOccupation" name="guardian_occupation" placeholder="Enter occupation" >
                </div>
                <div class="col-md-6 admin_localGurdian_field" style="display: none;" >
                    <label for="guardianPhone" class="form-label text-13">Phone No <span class="text-danger">*</span></label>
                    <input type="number" class="form-control as_localgurdian_field placeholder-14 text-13" id="guardianPhone" name="guardian_phone" placeholder="Enter phone number" >
                </div>
                <div class="col-md-6 admin_localGurdian_field" style="display: none;" >
                    <label for="guardianEmail" class="form-label text-13">Email ID</label>
                    <input type="email" class="form-control as_localgurdian_field placeholder-14 text-13" id="guardianEmail" name="guardian_email" placeholder="Enter email ID">
                </div>
                <!-- Autofill Address Checkbox -->
                <div class="col-md-12 admin_localGurdian_field" style="display: none;" class="admin_localGurdian_field">
                    <input type="checkbox" id="guardianAutoFill" onchange="autoFillAddress('guardian')">
                    <label for="guardianAutoFill" class="form-label text-13">Same as Student's Address</label>
                </div>
                <!-- Local Guardian Address -->
                <div class="col-md-6 admin_localGurdian_field" style="display: none;" >
                    <label for="guardianStreet" class="form-label text-13">Street Name / Village<span class="text-danger">*</span></label>
                    <input type="text" class="form-control as_localgurdian_field placeholder-14 text-13" id="guardianStreet" name="guardian_street" placeholder="Enter street/village" >
                </div>
                <div class="col-md-6 admin_localGurdian_field" style="display: none;" >
                    <label for="guardianPO" class="form-label text-13">Post Office (PO)<span class="text-danger">*</span></label>
                    <input type="text" class="form-control as_localgurdian_field placeholder-14 text-13" id="guardianPO" name="guardian_po" placeholder="Enter PO" >
                </div>
                <div class="col-md-6 admin_localGurdian_field" style="display: none;" >
                    <label for="guardianPS" class="form-label text-13">Police Station (PS)<span class="text-danger">*</span></label>
                    <input type="text" class="form-control as_localgurdian_field placeholder-14 text-13" id="guardianPS" name="guardian_ps" placeholder="Enter PS" >
                </div>
                <div class="col-md-6 admin_localGurdian_field" style="display: none;" >
                    <label for="guardianCity" class="form-label text-13">City<span class="text-danger">*</span></label>
                    <input type="text" class="form-control as_localgurdian_field placeholder-14 text-13" id="guardianCity" name="guardian_city" placeholder="Enter city" >
                </div>
                <div class="col-md-6 admin_localGurdian_field" style="display: none;" >
                    <label for="guardianState" class="form-label text-13">State<span class="text-danger">*</span></label>
                    <input type="text" class="form-control as_localgurdian_field placeholder-14 text-13" id="guardianState" name="guardian_state" placeholder="Enter state" >
                </div>
                <div class="col-md-6 admin_localGurdian_field" style="display: none;">
                    <label for="guardianCountry" class="form-label text-13">Country<span class="text-danger">*</span></label>
                    <input type="text" class="form-control as_localgurdian_field placeholder-14 text-13" id="guardianCountry" name="guardian_country" placeholder="Enter country">
                </div>
                <div class="col-md-6 admin_localGurdian_field" style="display: none;">
                    <label for="guardianPincode" class="form-label text-13">Pincode<span class="text-danger">*</span></label>
                    <input type="number" class="form-control as_localgurdian_field placeholder-14 text-13" id="guardianPincode" name="guardian_pincode" placeholder="Enter pincode" >
                </div>
        

        </div>
    </div>
    <!-- Step 5: Academic Details -->
    <div class="step" id="step-5">
        <p class="text-secondary mb-3">Academic Details</p>
        
        <div class="row g-3">
            <div class="col-md-4">
                <label for="institute" class="form-label text-13">
                  Select Institution<span class="text-danger">*</span>
                </label>
                <select class="form-select text-13" id="institute" name="institute" onchange="handleInstituteSelectChange()" required>
                  <option value="" disabled selected>Loading...</option>
                </select>
              </div>
              
              <div class="col-md-4">
                <label for="feesType" class="form-label text-13">
                  Select Fees Type<span class="text-danger">*</span>
                </label>
                <select class="form-select text-13" id="feesType" name="feesType" required>
                  <option value="" disabled selected>Select Fees Type</option>
                  <option value="GEN">GEN</option>
                  <option value="EWS">EWS</option>
                  <option value="TFW">TFW</option>
                </select>
              </div>
              
              <div class="col-md-4">
                <label for="courseType" class="form-label text-13">
                  Select Course<span class="text-danger">*</span>
                </label>
                <select class="form-select text-13" id="courseType" name="course" onchange="handleCourseTypeChange()" required>
                  <option value="" disabled selected>Select Fees Type First</option>
                </select>
              </div>
        
            <!-- Class X Details -->
            <button class="" id="as_showClassXButton" style="display: none;"><i class="fa-solid fa-pen-to-square"></i> Class X Data</button>
            <div class="col-md-6 asr_classx_field" style="display: none;">
                <label for="classXExam" class="form-label text-13">Exam Name <span class="text-danger">*</span></label>
                <input type="text" class="form-control placeholder-14 text-13" id="classXExam" name="class_x_exam_name" placeholder="Enter exam name" required>
            </div>
            <div class="col-md-6 asr_classx_field" style="display: none;">
                <label for="classXInstitution" class="form-label text-13">Institution Name <span class="text-danger">*</span></label>
                <input type="text" class="form-control placeholder-14 text-13" id="classXInstitution" name="class_x_institution_name" placeholder="Enter institution name" required>
            </div>
            <div class="col-md-6 asr_classx_field" style="display: none;">
                <label for="classXBoard" class="form-label text-13">Board <span class="text-danger">*</span></label>
                <input type="text" class="form-control placeholder-14 text-13" id="classXBoard" name="class_x_board" placeholder="Enter board" required>
            </div>
            <div class="col-md-12 asr_classx_field" style="display: none;">
                <div class="table-responsive">
                    <table class="table table-bordered text-center text-13">
                        <thead class="bg-light">
                            <tr>
                                <th class="text-secondary">Subject</th>
                                <th class="text-secondary">Full_Marks</th>
                                <th class="text-secondary">Marks_Obtained</th>
                                <th class="text-secondary">Percentage</th>
                                <th class="text-secondary">Action</th>
                            </tr>
                        </thead>
                        <tbody id="classXMarksTable">
                            <!-- Rows will be dynamically added here -->
                        </tbody>
                    </table>
                </div>
                <button type="button" class="btn btn-outline-primary text-13 mt-3" onclick="addRow('classXMarksTable', 'class_x')">Add Subject</button>
            </div>
            
            <input type="hidden" id="class_x_data" name="class_x_data">
            <hr class="asr_classx_field" style="display: none;">
            
            <!-- Class XII Details -->
            <button class="" id="as_addClassXIIBtn" style="display: none;"><i class="fa-solid fa-pen-to-square"></i> Class XII Data</button>

            <!-- Class XII Fields -->
            <div class="col-md-6 asr_classxii_field" style="display: none;">
                <label for="classXIIExam" class="form-label text-13">Exam Name <span class="text-danger">*</span></label>
                <input type="text" class="form-control as_classXIIExam placeholder-14 text-13" id="classXIIExam" name="class_xii_exam_name" placeholder="Enter exam name">
            </div>
            <div class="col-md-6 asr_classxii_field" style="display: none;">
                <label for="classXIIInstitution" class="form-label text-13">Institution Name <span class="text-danger">*</span></label>
                <input type="text" class="form-control as_classXIIExam placeholder-14 text-13" id="classXIIInstitution" name="class_xii_institution_name" placeholder="Enter institution name">
            </div>
            <div class="col-md-6 asr_classxii_field" style="display: none;">
                <label for="classXIIBoard" class="form-label text-13">Board <span class="text-danger">*</span></label>
                <input type="text" class="form-control as_classXIIExam placeholder-14 text-13" id="classXIIBoard" name="class_xii_board" placeholder="Enter board">
            </div>
            <div class="col-md-12 asr_classxii_field" style="display: none;">
                <div class="table-responsive">
                    <table class="table table-bordered text-center text-13">
                        <thead class="bg-light">
                            <tr>
                                <th class="text-secondary">Subject</th>
                                <th class="text-secondary">Full_Marks</th>
                                <th class="text-secondary">Marks_Obtained</th>
                                <th class="text-secondary">Percentage</th>
                                <th class="text-secondary">Action</th>
                            </tr>
                        </thead>
                        <tbody id="classXIIMarksTable">
                            <!-- Rows will be dynamically added here -->
                        </tbody>
                    </table>
                </div>
                <button type="button" class="btn btn-outline-primary text-13 mt-3" onclick="addRow('classXIIMarksTable', 'class_xii')">Add Subject</button>
            </div>
            
            <input type="hidden" id="class_xii_data" name="class_xii_data">
            <hr class="asr_classxii_field" style="display: none;">
    
            <!-- College Details -->
            <button class="" id="as_addCollegeBtn" style="display: none;"><i class="fa-solid fa-pen-to-square"></i> College Data</button>

            <!-- College Fields -->
            <div class="col-md-6 asr_college_field" style="display: none;">
                <label for="collegeName" class="form-label text-13">College Name <span class="text-danger">*</span></label>
                <input type="text" class="form-control as_collagefield placeholder-14 text-13" id="collegeName" name="college_name" placeholder="Enter college name">
            </div>
            <div class="col-md-6 asr_college_field" style="display: none;">
                <label for="collegeUniversity" class="form-label text-13">University Name <span class="text-danger">*</span></label>
                <input type="text" class="form-control as_collagefield placeholder-14 text-13" id="collegeUniversity" name="college_university" placeholder="Enter university name">
            </div>
            <div class="col-md-6 asr_college_field" style="display: none;">
                <label for="collegeDegree" class="form-label text-13">Degree <span class="text-danger">*</span></label>
                <input type="text" class="form-control as_collagefield placeholder-14 text-13" id="collegeDegree" name="college_degree" placeholder="Enter degree">
            </div>
            <div class="col-md-6 asr_college_field" style="display: none;">
                <label for="collegePassingYear" class="form-label text-13">Year of Passing <span class="text-danger">*</span></label>
                <input type="number" class="form-control as_collagefield placeholder-14 text-13" id="collegePassingYear" name="college_passing_year" placeholder="Enter year of passing">
            </div>
            <div class="col-md-12 asr_college_field" style="display: none;">
                <div class="table-responsive">
                    <table class="table table-bordered text-center text-13">
                        <thead class="bg-light">
                            <tr>
                                <th class="text-secondary">Semester</th>
                                <th class="text-secondary">Full_Marks</th>
                                <th class="text-secondary">Marks_Obtained</th>
                                <th class="text-secondary">Percentage</th>
                                <th class="text-secondary">Action</th>
                            </tr>
                        </thead>
                        <tbody id="collegeMarksTable">
                            <!-- Rows will be dynamically added here -->
                        </tbody>
                    </table>
                </div>
                <button type="button" class="btn btn-outline-primary text-13 mt-3" onclick="addRow('collegeMarksTable', 'college')">Add Semester</button>
            </div>
    
            <input type="hidden" id="college_data" name="college_data">
        </div>
    </div>
   
    
    <!-- Navigation Buttons -->
    <hr>
    <div class="navigation mt-4 d-flex">
        <button type="button" class="btn btn-outline-secondary text-13" id="prevBtn" onclick="changeStep(-1)">previous</button>
        <button type="button" class="btn btn-outline-primary text-13" id="nextBtn" onclick="changeStep(1)">next</button>
    </div>

    <!-- Submit Button -->
    <div>
        <button type="submit" class="btn btn-success admin_ragister_form_submitbtn text-13" id="submitBtn" style="display: none;">Submit</button>
    </div>
</form>

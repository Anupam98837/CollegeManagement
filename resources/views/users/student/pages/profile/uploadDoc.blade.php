<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Upload Student Document</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body>
    <div class="d-flex">
        <!-- Sidebar -->
        <div>
            @include('users.student.components.sidebar')
        </div>

        <!-- Main Content -->
        <div class="w-100 main-com">
            @include('users.student.components.header')
            <div class="container mt-4">
                <p class="mb-4 text-secondary text-14"><i class="fa-solid fa-angle-right"></i> <span class="text-primary">Upload Document</span></p>


                <!-- Search Form -->
                <form id="auud_searchStudentForm" class="bg-white p-4 rounded mb-4 d-none">
                    <div class="row g-3 align-items-end">
                        <div class="col-md-12">
                            <label for="email" class="form-label text-13">Your Email <span class="text-danger">*</span></label>
                            <input type="email" id="auud_email" name="email" class="form-control placeholder-14 text-13" placeholder="Enter student email" required>
                        </div>
                        <div class="col-md-5 d-none">
                            <button type="submit" class="btn btn-primary w-100 text-13">
                                <span id="searchButtonText">Document Upload</span>
                                <span id="searchButtonSpinner" class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                            </button>
                        </div>
                    </div>
                </form>

                <!-- Document Upload Form -->
                <form id="uploadDocumentForm" class="bg-white p-4 rounded d-none" enctype="multipart/form-data">
                    <h5 class="text-13 mb-4">Document Upload</h5>
                    <div id="Admin_student_ragister_documentUploadFields" class="row g-3">
                        
                    </div>
                    <input type="hidden" id="asud_emailHidden" name="email">
                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn btn-outline-success text-13 mt-4 Admin_student_ragister_documentUploadFields_btn">
                            Upload Documents
                        </button>
                    </div>                
                </form>
                <div id="registerMessageContainer" class=" p-4 rounded mb-4 text-center d-none d-flex flex-column justify-content-center align-items-center vh-50">
                    <img src="{{ asset('assets/web_assets/noData.png') }}" alt="">
                    <p class="fs-5">Please complete your registration first.</p>
                    <a href="/student/register" class="btn btn-primary">Register</a>
                </div>                
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="{{ asset('js/Users/Student/uploadStudentDoc.js') }}"></script>
    <script>document.addEventListener("DOMContentLoaded", function() {
        if (!sessionStorage.getItem("token")) {
          // Redirect to blank path or your preferred path if token is missing.
          window.location.href = "/";
        }
      });</script>
</body>
</html>

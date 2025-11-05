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

            <div class="container mt-4">
                <p class="mb-4 text-secondary text-14">Student <i class="fa-solid fa-angle-right"></i> <span class="text-primary">Upload Document</span></p>

                <!-- Search Form -->
                <form id="auud_searchStudentForm" class="bg-white p-4 rounded mb-4">
                    <div class="row g-3 align-items-end">
                        <div class="col-md-10">
                            <label for="email" class="form-label text-13">Search by Email <span class="text-danger">*</span></label>
                            <input type="email" id="auud_email" name="email" class="form-control placeholder-14 text-13" placeholder="Enter student email" required>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary w-100 text-13">
                                <span id="searchButtonText">Search</span>
                                <span id="searchButtonSpinner" class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                            </button>
                        </div>
                    </div>
                </form>

                <div  class=" p-4 rounded mb-4 text-center d-flex flex-column justify-content-center align-items-center vh-50 " id="search_Data_div">
                    <img src="{{ asset('assets/web_assets/search.png') }}" alt="" style="width: 300px;">
                    <p class="fs-5">Search for a Student</p>
                </div>

                <!-- Document Upload Form -->
                <form id="uploadDocumentForm" class="bg-white p-4 rounded d-none" enctype="multipart/form-data">
                    <h5 class="text-13 mb-4">Document Upload</h5>
                    <div id="Admin_student_ragister_documentUploadFields" class="row g-3">
                        
                    </div>
                    <input type="hidden" id="asud_emailHidden" name="email">
                    <button type="submit" class="btn btn-success text-13 mt-4 Admin_student_ragister_documentUploadFields_btn">Upload Documents</button>
                </form>
            </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="{{ asset('js/Components/uploadStudentDoc.js') }}"></script>
</body>
</html>

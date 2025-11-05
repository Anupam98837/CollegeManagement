<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta http-equiv="X-UA-Compatible" content="ie=edge" />
  <title>Student Fee Structure</title>
  <!-- Bootstrap CSS -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" />
  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" />
  <!-- SweetAlert2 -->
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <!-- CSRF Token -->
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <style>
    .list-group-item.total-item { text-align: right; }
    /* Card Header */
    .card-header { background-color: var(--bs-primary); color: var(--bs-secondary); }
    /* Spinner Centering */
    #loadingSpinner {
      position: absolute;
      top: 50%;
      left: 50%;
      transform: translate(-50%, -50%);
    }
    /* Rotation animation */
    @keyframes rotation {
      from { transform: rotate(0deg); }
      to { transform: rotate(360deg); }
    }
    .rotate {
      animation: rotation 1s linear;
    }
  </style>
</head>
<body class="bg-light">
  <div class="d-flex">
    <!-- Sidebar -->
    <div>
      @include('users.student.components.sidebar')
    </div>
    <!-- Main Content -->
    <div class="w-100 main-com position-relative">
      @include('users.student.components.header')
      
      <!-- Registration Message Container (hidden by default) -->
      <div id="registerMessageContainer" class="p-4 rounded mb-4 text-center d-none d-flex flex-column justify-content-center align-items-center vh-50">
        <img src="{{ asset('assets/web_assets/noData.png') }}" alt="No Data">
        <p class="fs-5">Please complete your registration first.</p>
        <a href="/Student/Register" class="btn btn-primary">Register</a>
      </div>
      
      <!-- Fee Structure Content Container -->
      <div id="feeStructureContent" class="container mt-4">
        <p id="feesStructureHeader" class="mb-4 text-secondary text-14">
          Fees <i class="fa-solid fa-angle-right"></i>
          <span class="text-primary">My Fee Structure</span>
        </p>

        <!-- Student Info Card -->
        <div id="studentInfoCard" class="card mb-4 shadow-sm">
          <div class="card-body">
            <h4 id="studentName" class="fw-bold"></h4>
            <p id="studentEmail" class="small-text"></p>
            <p id="studentCourse" class="small-text"></p>
          </div>
        </div>

        <!-- Fee Structure Card -->
        <div id="feeStructureCard" class="card shadow-sm">
          <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Fee Details</h5>
            <button class="btn btn-outline-secondary btn-sm" onclick="refreshFeeStructure()">
              <i class="fa-solid fa-rotate-right"></i> Refresh
            </button>
          </div>
          <div class="card-body position-relative">
            <!-- Spinner (visible until data loads) -->
            <div id="loadingSpinner">
              <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
              </div>
            </div>
            <!-- Fee details will be loaded here -->
            <div id="paymentDetails" class="d-none text-13"></div>
          </div>
        </div>
      </div><!-- end feeStructureContent -->
    </div><!-- end main-com -->
  </div><!-- end d-flex -->

  <script>
    document.addEventListener("DOMContentLoaded", function() {
      if (!sessionStorage.getItem("token")) {
        window.location.href = "/";
      }
    });
    document.addEventListener('DOMContentLoaded', function () {
      const email = sessionStorage.getItem("student_email");
      if (email) {
        fetchStudentData(email);
      } else {
        Swal.fire('Error', 'No student email found. Please log in.', 'error');
      }
    });

    // Refresh fee structure with a rotating icon and disable pay buttons during refresh
    function refreshFeeStructure() {
      // Disable all existing pay buttons
      document.querySelectorAll('.btn-outline-success.btn-sm').forEach(btn => btn.disabled = true);

      const email = sessionStorage.getItem("student_email");
      const refreshButton = document.querySelector(".btn-outline-secondary.btn-sm");
      const refreshIcon = refreshButton.querySelector("i");

      if (refreshIcon) {
        refreshIcon.classList.add("rotate");
      }
      
      if (email) {
        fetchStudentData(email).finally(() => {
          if (refreshIcon) {
            refreshIcon.classList.remove("rotate");
          }
        });
      } else {
        Swal.fire('Error', 'No student email found. Please log in.', 'error');
        if (refreshIcon) {
          refreshIcon.classList.remove("rotate");
        }
      }
    }

    // Fetch student data from API endpoint.
    function fetchStudentData(email) {
      return fetch('/api/get-student-by-email', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
          'Authorization': sessionStorage.getItem('token')
        },
        body: JSON.stringify({ email })
      })
      .then(response => response.json())
      .then(data => {
        if (data.status === 'success' && data.data) {
          // Check if the student's status is Active
          if (data.data.status !== 'Active') {
            document.getElementById('loadingSpinner').classList.add('d-none');
            document.getElementById('feeStructureContent').classList.add('d-none');
            document.getElementById('registerMessageContainer').classList.remove('d-none');
            document.getElementById('registerMessageContainer').innerHTML = `
              <img src="{{ asset('assets/web_assets/noData.png') }}" alt="No Data">
              <p class="fs-5">Your fee structure will be available after validation by the Institute..</p>
            `;
            return;
          }
          populateStudentInfo(data.data);
          let courseData = {};
          let instituteData = {};
          try {
            courseData = data.data.course ? JSON.parse(data.data.course) : {};
            instituteData = data.data.institute ? JSON.parse(data.data.institute) : {};
          } catch (e) {
            console.error('Error parsing course data:', e);
          }
          let programCode = courseData.program_code || '';
          let programName = courseData.program_name || '';
          let intakeType = courseData.intake_type || 'General';
          let institute_id = instituteData.institution_id || "";
          let institute_name = instituteData.institution_name || "";
          let year = courseData.intake_year || '';
          let feetype = courseData.fee_type || '';
          let studentemail = data.data.email || '';
          let studentname = data.data.name || '';
          // For fees and scholarship APIs, use student UID from sessionStorage
          let studentuid = sessionStorage.getItem("student_uid");
          fetchFeeStructure(institute_id, institute_name, programCode, programName, intakeType, year, feetype, studentuid, studentname);
        } else {
          document.getElementById('loadingSpinner').classList.add('d-none');
          document.getElementById('feeStructureContent').classList.add('d-none');
          document.getElementById('registerMessageContainer').classList.remove('d-none');
        }
      })
      .catch(error => {
        console.error('Error fetching student data:', error);
        Swal.fire('Error', 'An error occurred while fetching your profile.', 'error');
        document.getElementById('loadingSpinner').classList.add('d-none');
        document.getElementById('feeStructureContent').classList.add('d-none');
        document.getElementById('registerMessageContainer').classList.remove('d-none');
      });
    }

    // Populate the student info card.
    function populateStudentInfo(student) {
      profileUrl = 
      document.getElementById("studentName").innerHTML = `
        <img src="${student.student_photo ? `{{ asset('assets/student_documents') }}/${student.student_photo}` : `{{ asset('assets/web_assets/default-profile.jpg') }}`}" 
            alt="Profile Picture" class="rounded-circle border border-primary p-1 me-2" 
            style="width: 40px; height: 40px; object-fit: cover;"> 
        ${student.name || "N/A"}`;      
      document.getElementById("studentEmail").innerHTML = `<i class="fa-regular fa-envelope"></i> ${student.email || "N/A"}`;
      try {
        let courseData = student.course ? JSON.parse(student.course) : {};
        let instituteData = student.institute ? JSON.parse(student.institute) : {};
        document.getElementById("studentCourse").innerText =
          `${instituteData.institution_name || "N/A"} | ${courseData.program_name || "N/A"} | ${courseData.program_duration || "N/A"} Year | Intake: ${courseData.intake_type || "N/A"}`;
      } catch(e) {
        document.getElementById("studentCourse").innerText = "Course details not available.";
      }
    }

    // Fetch fee structure using program_code and intake_type.
    function fetchFeeStructure(institutionId, institute_name, programCode, programName, intakeType, year, feetype, studentuid, studentname) {
      fetch('/api/search-fees', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
          'Authorization': sessionStorage.getItem('token'),
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ 
          institute_id: institutionId,
          program_code: programCode,
          intake_type: intakeType,
          year: String(year),
          fee_type: feetype
        })
      })
      .then(response => response.json())
      .then(data => {
        document.getElementById('loadingSpinner').classList.add('d-none');
        if (data.status === 'success' && data.data && data.data.length > 0) {
          let feeRecord = data.data[0];
          // Pass feeRecord along with student details to populate the payment interface.
          getDiscount(feeRecord, institutionId, institute_name, programCode, programName, intakeType, year, feetype, studentname, studentuid);
        } else {
          document.getElementById('feeStructureContent').classList.add('d-none');
          document.getElementById('registerMessageContainer').classList.remove('d-none');
        }
      })
      .catch(error => {
        console.error('Error fetching fee structure:', error);
        document.getElementById('loadingSpinner').classList.add('d-none');
        document.getElementById('feeDetails').classList.remove('d-none');
        document.getElementById('feeDetails').innerHTML = '<p class="text-danger small-text">An error occurred while fetching fee structure.</p>';
      });
    }

    // In getDiscount, update the scholarship API to use student_uid instead of email.
    function getDiscount(feeRecord, institutionId, institute_name, programCode, programName, intakeType, year, feetype, studentname, studentuid) {
  // Set the current payment parameters for later use
  fetch('/api/scholarship/view', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'Accept': 'application/json',
      'Authorization': sessionStorage.getItem('token'),
      'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
    },
    // Use student_uid from sessionStorage (or the passed studentuid) for the scholarship API call
    body: JSON.stringify({ student_uid: sessionStorage.getItem("student_uid") })
  })
  .then(response => response.json())
  .then(data => {
    let scholarshipOverallDiscount = "";
    let scholarshipOneTimeDiscount = "";
    let scholarshipSemWiseDiscount = "";
    if (data.status === 'success' && data.data) {
      const scholarship = data.data;
      scholarshipOverallDiscount = scholarship.overall_discount || "";
      scholarshipOneTimeDiscount = scholarship.one_time_discount || "";
      scholarshipSemWiseDiscount = scholarship.sem_wise_discount || "";
    }
    // Pass the fetched discount values (or empty strings) to the populatePaymentDetails function.
    populatePaymentDetails(feeRecord, institutionId, institute_name, programCode, programName, intakeType, year, feetype, studentname, studentuid, scholarshipOverallDiscount, scholarshipOneTimeDiscount, scholarshipSemWiseDiscount);
  })
  .catch(error => {
    console.error("Error fetching scholarship record:", error);
  });
}

function computeDiscount(total, discountStr) {
  let discountAmount = 0;
  if (!discountStr) return { discountAmount, finalTotal: total };
  discountStr = discountStr.trim();
  if (discountStr.endsWith('%')) {
    const perc = parseFloat(discountStr.slice(0, -1));
    if (!isNaN(perc)) {
      discountAmount = total * perc / 100;
    }
  } else {
    discountAmount = parseFloat(discountStr) || 0;
    if (discountAmount > total) {
      discountAmount = total;
    }
  }
  return { discountAmount, finalTotal: total - discountAmount };
}

    

    // Populate fee structure details in an accordion format.
    async function populatePaymentDetails(
      fee,
      instituteId,
      instituteName,
      programCode,
      programName,
      intakeType,
      year,
      feeType,
      studentName,
      studentEmail,
      scholarshipOverallDiscount,
      scholarshipOneTimeDiscount,
      scholarshipSemWiseDiscount
    ) {
      // Log fee and related parameters
console.log("Fee Object:", fee);
console.log("Institute ID:", instituteId);
console.log("Institute Name:", instituteName);
console.log("Program Code:", programCode);
console.log("Program Name:", programName);
console.log("Intake Type:", intakeType);
console.log("Year:", year);
console.log("Fee Type:", feeType);
console.log("Student Email:", studentEmail);
console.log("Student Name:", studentName);

// Log scholarship discount values with proper indicators
console.log("Scholarship Overall Discount:", scholarshipOverallDiscount);
console.log("Scholarship One-Time Discount:", scholarshipOneTimeDiscount);

try {
  const parsedSemWiseDiscount = JSON.parse(scholarshipSemWiseDiscount);
  console.log("Scholarship Semester-Wise Discount (parsed JSON):", parsedSemWiseDiscount);
} catch (error) {
  console.error("Scholarship Semester-Wise Discount: Invalid JSON:", scholarshipSemWiseDiscount);
}

      let feesData = null;
      const token = sessionStorage.getItem('token');
      
      // Fetch fee details from the API using student_uid instead of student_email.
      try {
        const response = await fetch('/api/get-fees', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'Authorization': token,
            'X-CSRF-TOKEN': document
              .querySelector('meta[name="csrf-token"]')
              .getAttribute('content'),
          },
          body: JSON.stringify({
            student_uid: sessionStorage.getItem("student_uid"),
            institute_id: instituteId,
            program_code: programCode,
            intake_type: intakeType,
            intake_year: year,
            fee_type: feeType,
          }),
        });

        if (!response.ok) {
          throw new Error(`Network response was not ok (Status: ${response.status})`);
        }
        
        const data = await response.json();
        if (data.status === 'success') {
          feesData = data.data;
        } else {
          console.error("API returned error:", data.message);
          feesData = data.data;
        }
      } catch (error) {
        console.error("Error during fetch operation:", error);
      }
      
      // Parse fee details from the provided fee object
      let oneTimeFees = {};
      let semesterFees = {};
      let otherFees = {};
      
      try {
        oneTimeFees = fee.one_time_fees ? JSON.parse(fee.one_time_fees) : {};
      } catch (e) {
        console.error("Error parsing one-time fees:", e);
      }
      try {
        semesterFees = fee.semester_wise_fees ? JSON.parse(fee.semester_wise_fees) : {};
      } catch (e) {
        console.error("Error parsing semester-wise fees:", e);
      }
      try {
        otherFees = fee.other_fees ? JSON.parse(fee.other_fees) : {};
      } catch (e) {
        console.error("Error parsing other fees:", e);
      }
      
      // Build the HTML structure
      let html = "";

      let rawOnetimeTotal = 0;
      let onetimeDiscount = 0
      let rawSemTotal = 0;
      let semWiseDiscount = 0;
      let oneTimeTotal = 0;
      // Parse paid fees from feesData (if available)
      let paid_onetimefees = (() => { try { return JSON.parse(feesData.one_time_fees); } catch (e) { return ""; } })();
      let paid_semesterfees = (() => { try { return JSON.parse(feesData.semester_fees); } catch (e) { return ""; } })();
      let Paid_allfees = (() => { try { return JSON.parse(feesData.overall_total_fees); } catch (e) { return ""; } })();

      // One-Time Fees Section
      html += '<h6 class="mb-2 text-14"><i class="fa-solid fa-receipt me-1"></i> One-Time Fees</h6>';
      if (Object.keys(oneTimeFees).length > 0) {
        html += '<ul class="list-group mb-3 text-13">';
        for (let head in oneTimeFees) {
          let amount = parseFloat(oneTimeFees[head]) || 0;
          oneTimeTotal += amount;
          html += `<li class="list-group-item d-flex justify-content-between align-items-center">
                     <span>${head}</span>
                     <span class="badge bg-primary">₹ ${amount}</span>
                   </li>`;
        }
        let discountAmount = 0;
      let finalOneTime = (oneTimeTotal).toFixed(2);
      rawOnetimeTotal = (oneTimeTotal).toFixed(2);
      if (scholarshipOverallDiscount && scholarshipOverallDiscount.trim() !== "") {
        let overAllDiscount= JSON.parse(scholarshipOverallDiscount)
        const discountValue = (overAllDiscount.one_time_discount).toString();
        const result = computeDiscount(oneTimeTotal, discountValue);
        oneTimeDiscountApplied = result.discountAmount;
        discountAmount = (result.discountAmount).toFixed(2);
        onetimeDiscount = discountAmount;
        oneTimeTotal = (result.finalTotal).toFixed(2);
      } else if (scholarshipOneTimeDiscount && scholarshipOneTimeDiscount.trim() !== "") {
        const result = computeDiscount(oneTimeTotal, scholarshipOneTimeDiscount);
        oneTimeDiscountApplied = result.discountAmount;
        discountAmount = (result.discountAmount).toFixed(2);
        onetimeDiscount = discountAmount;
        oneTimeTotal = (result.finalTotal).toFixed(2);
      }

        html += `<li class="list-group-item d-flex flex-column">
                  <div class="d-flex justify-content-end">
                     ${discountAmount
                  ?
                  `<span>Total One-Time Fees: <del>₹ ${finalOneTime}</del>  <span class="text-primary">-₹ ${discountAmount}</span>  <strong>= ₹ ${oneTimeTotal}</strong></span>
`
                  :`<span>Total One-Time Fees: <strong> ₹ ${oneTimeTotal.toFixed(2)}</strong></span>` 
                  }
                  </div>
                  <div class="mt-2">
                    ${ (paid_onetimefees && typeof paid_onetimefees === 'object' && Object.keys(paid_onetimefees).length > 0) ? (() => {
                        let totalPaid = Object.values(paid_onetimefees).reduce((sum, payment) => sum + parseFloat(payment.amount || 0), 0);
                        let breakdown = Object.entries(paid_onetimefees).map(([key, payment]) => {
                            return `<div class="d-flex justify-content-end align-items-center mb-1">
                                      <span class="me-2">Payment ${key}: <span class="text-primary">₹ ${payment.amount}</span> | ${payment.date}</span>
                                      <button class="btn btn-outline-primary btn-sm" onclick='showInvoice("one_time", {
                                            student_email: "${studentEmail}",
                                            student_name: "${studentName}",
                                            institute_id: "${instituteName}",
                                            course_id: "${programName}",
                                            intake_type: "${fee.intake_type || "General"}",
                                            intake_year: "${fee.intake_year || "2025"}",
                                            fee_type: "${fee.fee_type || "gen"}",
                                            fee_head: "One-Time Fees",
                                            transactionId : "${payment.transaction_id}",
                                            fee_detail: JSON.stringify({amount: "${payment.amount}"})
                                      })'><i class="fa-solid fa-print"></i></button>
                                    </div>`;
                        }).join('');
                        let remaining = oneTimeTotal - totalPaid;
                        return breakdown + (remaining > 0 
                                ? `<div class="d-flex justify-content-end align-items-center mt-2">
                                    <span class="me-2">Remaining Fees: <strong>₹ ${remaining.toFixed(2)}</strong></span>
                                  </div>`
                                : `<div class="d-flex justify-content-end align-items-center mt-2">
                                    <span class="text-success"><i class="fa-solid fa-check"></i> Fully paid</span>
                                  </div>`
                              );
                          })() : `<div class="d-flex justify-content-end align-items-center mt-2">
                                    <span class="me-2">Remaining Fees: <strong>₹ ${oneTimeTotal}</strong></span>
                                  </div>` }
                  </div>
                  <div class="d-flex justify-content-end mt-2">
                    ${ ((!paid_onetimefees) || (typeof paid_onetimefees !== 'object') || (Object.keys(paid_onetimefees).length === 0) 
                        || (oneTimeTotal - Object.values(paid_onetimefees).reduce((sum, payment) => sum + parseFloat(payment.amount || 0), 0)) > 0)
                        ? `<button class="btn btn-outline-success btn-sm" onclick='initiatePayment("one_time", {
                                student_email: "${studentEmail}",
                                institute_id: "${instituteId}",
                                program_code: "${programCode}",
                                intake_type: "${fee.intake_type || "General"}",
                                intake_year: "${fee.intake_year || "2025"}",
                                fee_type: "${fee.fee_type || "gen"}",
                                fee_detail: JSON.stringify({amount: "${ oneTimeTotal - ((paid_onetimefees && typeof paid_onetimefees === 'object') ? Object.values(paid_onetimefees).reduce((sum, payment) => sum + parseFloat(payment.amount || 0), 0) : 0)}"})
                        })'><i class="fa-solid fa-credit-card"></i> Pay</button>`
                        : "" }
                  </div>
                </li>`;

        html += '</ul>';
      } else {
        html += '<p class="text-muted text-13">No one-time fees.</p>';
      }

      // Semester-Wise Fees Section (Merged with Other Fees)
      let semesters = new Set();
      for (let feeHead in semesterFees) {
        let semObj = semesterFees[feeHead];
        Object.keys(semObj).forEach((sem) => semesters.add(sem));
      }
      for (let sem in otherFees) {
        semesters.add(sem);
      }
      semesters = Array.from(semesters).sort();

      let overallMergedTotal = 0;
      let mergedHtml = "";
      if (semesters.length > 0) {
        mergedHtml += `<div class="accordion" id="mergedSemesterAccordion">`;
        semesters.forEach((sem, index) => {
          let semTotal = 0;
          let feeListHtml = '<ul class="list-group text-13">';
          
          // Regular semester fees for this semester
          for (let feeHead in semesterFees) {
            let semObj = semesterFees[feeHead];
            if (sem in semObj) {
              let amt = parseFloat(semObj[sem]) || 0;
              semTotal += amt;
              feeListHtml += `<li class="list-group-item d-flex justify-content-between align-items-center">
                                <span>${feeHead} (Regular)</span>
                                <span class="badge bg-success">₹ ${amt}</span>
                              </li>`;
            }
          }
          // Other fees for this semester (if any)
          if (otherFees[sem]) {
            let feeObj = otherFees[sem];
            for (let feeHead in feeObj) {
              let amt = parseFloat(feeObj[feeHead]) || 0;
              semTotal += amt;
              feeListHtml += `<li class="list-group-item d-flex justify-content-between align-items-center">
                                <span>${feeHead} (Other)</span>
                                <span class="badge bg-info">₹ ${amt}</span>
                              </li>`;
            }
          }
          
          // Build the semester fee list item
          let paidPayments = [];
          if (paid_semesterfees[sem] && typeof paid_semesterfees[sem] === 'object') {
            paidPayments = Object.values(paid_semesterfees[sem]);
          }
          let totalPaid = 0;
          let partPaymentHtml = "";
          paidPayments.forEach((payment, index) => {
            const paymentAmount = parseFloat(payment.fee_details.amount) || 0;
            totalPaid += paymentAmount;
            partPaymentHtml += `
              <div class="d-flex justify-content-end align-items-center mb-1">
                <span class="me-2">Payment ${index + 1}: <span class="text-primary">₹ ${paymentAmount.toFixed(2)}</span> | ${payment.fee_details.date}</span>
                <button class="btn btn-outline-primary btn-sm" onclick='showInvoice("semester", {
                    student_email: "${studentEmail}",
                    student_name: "${studentName}",
                    institute_id: "${instituteName}",
                    course_id: "${programName}",
                    intake_type: "${fee.intake_type || "General"}",
                    intake_year: "${fee.intake_year || "2025"}",
                    fee_type: "${fee.fee_type || "gen"}",
                    fee_head: "Semester ${sem}",
                    transactionId : "${payment.transaction_id}",
                    fee_detail: JSON.stringify({amount: "${paymentAmount}"})
                })'><i class="fa-solid fa-print"></i></button>
              </div>`;
          });

           // Apply discount on the semester total
           let semDiscountApplied = 0;
            let finalSemTotal = (semTotal).toFixed(2);
            rawSemTotal += parseFloat(semTotal.toFixed(2));
            if (scholarshipOverallDiscount && scholarshipOverallDiscount.trim() !== "") {
              let overAllDiscount = JSON.parse(scholarshipOverallDiscount);
              const discountValue = overAllDiscount[sem].toString();
              const result = computeDiscount(semTotal, discountValue);
              semDiscountApplied = (result.discountAmount).toFixed(2);
              semWiseDiscount += parseFloat(semDiscountApplied);
              semTotal = (result.finalTotal).toFixed(2);
            } else if (scholarshipSemWiseDiscount && scholarshipSemWiseDiscount.trim() !== "") {
              let semDiscountObj = {};
              try {
                semDiscountObj = JSON.parse(scholarshipSemWiseDiscount);
              } catch(e) {
                console.error("Error parsing semester-wise discount:", e);
              }
              if (semDiscountObj[sem] && semDiscountObj[sem].amount && semDiscountObj[sem].amount.trim() !== "") {
                const result = computeDiscount(semTotal, semDiscountObj[sem].amount);
                semDiscountApplied = (result.discountAmount).toFixed(2);
                semWiseDiscount += parseFloat(semDiscountApplied);
                semTotal = (result.finalTotal).toFixed(2);
              }
            }

          const remaining = semTotal - totalPaid;

          feeListHtml += `
            <li class="list-group-item">
              <div class="d-flex justify-content-end align-items-center">
                ${semDiscountApplied
                  ?
                  `<span>Total One-Time Fees: <del>₹ ${finalSemTotal}</del>  <span class="text-primary">-₹ ${semDiscountApplied}</span>  <strong>= ₹ ${semTotal}</strong></span>`
                  :`<span>Total One-Time Fees: <strong> ₹ ${semTotal}</strong></span>` 
                  }
              </div>
              ${ partPaymentHtml ? `<div class="mt-2">${partPaymentHtml}</div>` : "" }
              <div class="d-flex justify-content-end align-items-center mt-2">
                ${ remaining > 0 ? `
                  <span class="me-2">Remaining Fees: <strong>₹ ${remaining.toFixed(2)}</strong></span>
                  <button class="btn btn-outline-success btn-sm" onclick='initiatePayment("semester", {
                    student_email: "${studentEmail}",
                    institute_id: "${instituteId}",
                    program_code: "${programCode}",
                    intake_type: "${fee.intake_type || "General"}",
                    intake_year: "${fee.intake_year || "2025"}",
                    fee_type: "${fee.fee_type || "gen"}",
                    semester_head: "${sem}",
                    fee_detail: JSON.stringify({amount: "${remaining}"})
                  })'><i class="fa-solid fa-credit-card"></i> Pay</button>
                ` : `<span class="text-success"><i class="fa-solid fa-check"></i> Fully paid</span>` }
              </div>
            </li>
          `;

          feeListHtml += '</ul>';
          overallMergedTotal += semTotal;
          
          mergedHtml += `
            <div class="accordion-item">
              <h2 class="accordion-header" id="headingMerged${index}">
                <button class="accordion-button ${index === 0 ? '' : 'collapsed'} text-14" type="button" data-bs-toggle="collapse" data-bs-target="#collapseMerged${index}" aria-expanded="${index === 0 ? 'true' : 'false'}" aria-controls="collapseMerged${index}">
                  Semester ${sem}
                </button>
              </h2>
              <div id="collapseMerged${index}" class="accordion-collapse collapse ${index === 0 ? 'show' : ''}" aria-labelledby="headingMerged${index}" data-bs-parent="#mergedSemesterAccordion">
                <div class="accordion-body text-13">
                  ${feeListHtml}
                </div>
              </div>
            </div>
          `;
        });
        mergedHtml += `</div>`;
      } else {
        mergedHtml = '<p class="text-muted text-13">No semester-wise fees.</p>';
      }
      html += '<h6 class="mb-2 text-14"><i class="fa-solid fa-calendar-days me-1"></i> Semester-Wise Fees</h6>' + mergedHtml;

      // Overall Total Fees Section (One-Time + Semester)
      const overallTotal = parseFloat(rawOnetimeTotal) + parseFloat(rawSemTotal);
      const oneTimeDiscount = onetimeDiscount;
      const semDiscount = semWiseDiscount
      const TotalDiscount =  parseFloat(onetimeDiscount) + parseFloat(semWiseDiscount)
      const FinalTotal =  parseFloat(overallTotal) - parseFloat(TotalDiscount)
      

// Compute already paid amounts for one-time fees
let paidOneTimeTotal = 0;
if (paid_onetimefees && typeof paid_onetimefees === 'object') {
  paidOneTimeTotal = Object.values(paid_onetimefees).reduce((sum, payment) => {
    return sum + parseFloat(payment.amount || 0);
  }, 0);
}

// Compute already paid amounts for semester fees
let paidSemesterTotal = 0;
if (paid_semesterfees && typeof paid_semesterfees === 'object') {
  for (let sem in paid_semesterfees) {
    let payments = paid_semesterfees[sem];
    if (payments && typeof payments === 'object') {
      paidSemesterTotal += Object.values(payments).reduce((sum, payment) => {
        return sum + parseFloat(payment.fee_details.amount || 0);
      }, 0);
    }
  }
}


let totalPaid = paidOneTimeTotal + paidSemesterTotal;
let remainingTotal = FinalTotal - totalPaid;

html += `<div class="d-flex flex-column align-items-end mt-4">
          <span>Overall Total Fees: <del>₹ ${overallTotal.toFixed(2)}</del>  <span class="text-primary">-₹ ${TotalDiscount.toFixed(2)}</span>  <strong>= ₹ ${FinalTotal.toFixed(2)}</strong></span>
          <span>Overall Total Paid: <strong>= ₹ ${totalPaid.toFixed(2)}</strong></span>
          <span>Overall Remaining Fees: <strong>= ₹ ${remainingTotal.toFixed(2)}</strong></span>
        </div>`;

      
      // Update the payment details container in the UI
      const paymentDetailsEl = document.getElementById('paymentDetails');
      if (paymentDetailsEl) {
        paymentDetailsEl.classList.remove('d-none');
        paymentDetailsEl.innerHTML = html;
      }
    }

    function computeTotalFees(oneTime, semesterWise, other) {
      let total = 0;
      for (let key in oneTime) {
        total += parseFloat(oneTime[key]) || 0;
      }
      for (let feeHead in semesterWise) {
        const semObj = semesterWise[feeHead];
        for (let sem in semObj) {
          total += parseFloat(semObj[sem]) || 0;
        }
      }
      for (let sem in other) {
        const feeObj = other[sem];
        for (let feeHead in feeObj) {
          total += parseFloat(feeObj[feeHead]) || 0;
        }
      }
      return total;
    }

    function getFormattedDateTime() {
      const now = new Date();
      const year = now.getFullYear();
      const month = String(now.getMonth() + 1).padStart(2, '0'); // months are 0-indexed
      const day = String(now.getDate()).padStart(2, '0');
      const datePart = `${year}-${month}-${day}`;

      let hours = now.getHours();
      const minutes = String(now.getMinutes()).padStart(2, '0');
      const ampm = hours >= 12 ? 'PM' : 'AM';
      hours = hours % 12;
      hours = hours ? hours : 12; // the hour '0' should be '12'
      const timePart = `${hours}:${minutes} ${ampm}`;

      return `${datePart} : ${timePart}`;
    }

    function initiatePayment(paymentCategory, paymentData, btnElement) {
      // Disable the button and show a spinner
      if (btnElement) {
        btnElement.disabled = true;
        btnElement.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Processing...';
      }
      // Fire a SweetAlert2 modal to choose payment method.
      Swal.fire({
        title: 'Choose Payment Method',
        input: 'select',
        inputOptions: {
          'card': 'Card',
          'upi': 'UPI'
        },
        inputPlaceholder: 'Select a payment method',
        showCancelButton: true
      }).then((result) => {
        if(result.isConfirmed && result.value) {
          // Parse the fee detail from JSON.
          let feeFieldData = {};
          try {
            feeFieldData = JSON.parse(paymentData.fee_detail) || {};
          } catch(e) {
            feeFieldData = {};
          }
          feeFieldData.payment_method = result.value;
          feeFieldData.date = getFormattedDateTime();
          paymentData.fee_detail = JSON.stringify(feeFieldData);
          
          // Build the payload.
          let body = {
            student_uid: sessionStorage.getItem("student_uid"),
            institute_id: paymentData.institute_id,
            course_id: paymentData.course_id,
            program_code: paymentData.program_code,
            intake_type: paymentData.intake_type,
            intake_year: paymentData.intake_year,
            fee_type: paymentData.fee_type
          };
          if(paymentCategory === 'one_time') {
            body.one_time_fees = paymentData.fee_detail;
          } else if(paymentCategory === 'semester') {
            body.semester_head = paymentData.semester_head;
            body.semester_fees = paymentData.fee_detail;
          } else if(paymentCategory === 'all_semester') {
            body.all_semester_fees = paymentData.fee_detail;
          } else if(paymentCategory === 'overall_total') {
            body.overall_total_fees = paymentData.fee_detail;
          }
          
          console.log(body);

          // Call the API endpoint to process the payment.
          fetch('/api/pay-fees', {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json',
              'Accept': 'application/json',
              'Authorization': sessionStorage.getItem('token'),
              'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify(body)
          }).then(response => response.json())
          .then(data => {
             if(data.status === 'success') {
                    Swal.fire({
                          title: 'Success',
                          text: data.message,
                          icon: 'success'
                      }).then(() => {
                          // Instead of reloading the full page, refresh fee details
                          refreshFeeStructure();
                      });
             } else {
                Swal.fire('Error', data.message || 'Payment failed.', 'error');
                if (btnElement) {
                  btnElement.disabled = false;
                  btnElement.innerHTML = 'Pay';
                }
             }
          }).catch(error => {
             console.error('Error processing payment:', error);
             Swal.fire('Error', 'An error occurred while processing the payment.', 'error');
             if (btnElement) {
               btnElement.disabled = false;
               btnElement.innerHTML = 'Pay';
             }
          });
        } else {
          // If the modal is dismissed, re-enable the button.
          if (btnElement) {
            btnElement.disabled = false;
            btnElement.innerHTML = 'Pay';
          }
        }
      });
    }

    function showInvoice(paymentCategory, paymentData) {
      let feeDetail = {};
      try {
        feeDetail = paymentData.fee_detail ? JSON.parse(paymentData.fee_detail) : {};
      } catch (e) {
        console.error("Error parsing fee_detail:", e);
      }
      
      const invoiceHTML = `
      <html>
      <head>
        <title>Invoice</title>
        <style>
          body { font-family: Arial, sans-serif; margin: 20px; }
          .invoice-container { max-width: 800px; margin: auto; border: 1px solid #ccc; padding: 20px; }
          .invoice-header { text-align: center; margin-bottom: 20px; }
          .invoice-details { margin-bottom: 20px; }
          .invoice-footer { text-align: center; margin-top: 20px; }
          table { width: 100%; border-collapse: collapse; }
          table, th, td { border: 1px solid #ccc; }
          th, td { padding: 8px; text-align: left; }
          .print-btn { padding: 10px 20px; font-size: 14px; cursor: pointer; }
        </style>
      </head>
      <body>
        <div class="invoice-container">
          <div class="invoice-header">
            <h1>Invoice</h1>
            <p>Date: ${paymentData.paymentdate || new Date().toLocaleDateString()}</p>
          </div>
          <div class="invoice-details">
            <p><strong>Student Email:</strong> ${paymentData.student_email || 'N/A'}</p>
            <p><strong>Student Name:</strong> ${paymentData.student_name || 'N/A'}</p>
            <p><strong>Institution:</strong> ${paymentData.institute_id || 'N/A'}</p>
            <p><strong>Intake Type:</strong> ${paymentData.intake_type || 'N/A'}</p>
            <p><strong>Intake Year:</strong> ${paymentData.intake_year || 'N/A'}</p>
            <p><strong>Fee Type:</strong> ${paymentData.fee_type || 'N/A'}</p>
             <p><strong>Transection ID:</strong> ${paymentData.transactionId || 'N/A'}</p>
            ${ paymentData.semester_head ? `<p><strong>Semester:</strong> ${paymentData.semester_head}</p>` : '' }
          </div>
          <div class="invoice-table">
            <table>
              <thead>
                <tr>
                  <th>Description</th>
                  <th>Amount (₹)</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td>${paymentData.fee_head || 'N/A'}</td>
                  <td>${feeDetail.amount || '0.00'}</td>
                </tr>
              </tbody>
            </table>
          </div>
          <div class="invoice-footer">
            <button class="print-btn" onclick="window.print()">Print / Download Invoice</button>
          </div>
        </div>
      </body>
      </html>
      `;
      
      const invoiceWindow = window.open('', 'PrintInvoice', 'height=600,width=800');
      invoiceWindow.document.write(invoiceHTML);
      invoiceWindow.document.close();
      invoiceWindow.focus();
    }
  </script>
  <!-- Bootstrap Bundle with Popper -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

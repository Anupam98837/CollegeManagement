<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <meta http-equiv="X-UA-Compatible" content="ie=edge"/>
  <title>Student Dashboard</title>
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"/>
  <!-- Font Awesome -->
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet"/>
  <!-- SweetAlert2 -->
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <style>
    body { background: #f8f9fa; }
    .dashboard-card { 
      margin-bottom: 1.5rem; 
      min-height: 260px;
    }
    .profile-pic {
      width: 130px; height: 130px; object-fit: cover;
      border: 3px solid #0d6efd; border-radius: 50%;
      cursor: pointer;
    }
    .card-header {
      font-weight: 500;
      background: #fff;
      border-bottom: 2px solid #0d6efd;
    }
    .card .fa-lg { vertical-align: middle; }
    #loadingSpinner { position: absolute; top:50%; left:50%; transform: translate(-50%,-50%); }
    .badge-new {
      background: #dc3545;
      color: #fff;
      font-size: 0.75rem;
      margin-left: 0.5rem;
    }
    .pagination-controls button {
      margin: 0 0.25rem;
    }
  </style>
</head>
<body>
  <div class="d-flex">
    <!-- Sidebar -->
    <div>
      @include('users.student.components.sidebar')
    </div>
    <!-- Main -->
    <div class="flex-grow-1">
      @include('users.student.components.header')
      <div class="container py-4 position-relative" id="dashboardContainer">
        <div id="loadingSpinner">
          <div class="spinner-border text-primary" role="status"></div>
        </div>

        <!-- Dashboard Content -->
        <div id="dashboardContent" class="d-none">
          <div class="row">
            <!-- Basic Details -->
            <div class="col-md-4">
              <div class="card dashboard-card shadow-sm text-center">
                <div class="card-body d-flex flex-column align-items-center justify-content-center">
                  <img id="studentPhoto" 
                       src="{{ asset('assets/web_assets/default-profile.jpg') }}"
                       alt="Photo" 
                       class="profile-pic mb-3"
                       onclick="viewFullImage()"/>
                  <h5 id="studentName" class="fw-bold mb-1">–</h5>
                  <p id="studentEmail" class="mb-1">
                    <i class="fa-regular fa-envelope text-primary fa-lg me-1"></i>
                    – 
                  </p>
                  <p id="studentPhone">
                    <i class="fa-solid fa-phone text-success fa-lg me-1"></i>
                    –
                  </p>
                </div>
              </div>
            </div>

            <!-- Institution -->
            <div class="col-md-4">
              <div class="card dashboard-card shadow-sm">
                <div class="card-header">
                  <i class="fa-solid fa-building text-success fa-lg me-2"></i>
                  Institution
                </div>
                <div class="card-body">
                  <p><strong>Name:</strong> <span id="instName">–</span></p>
                  <p><strong>Type:</strong> <span id="instType">–</span></p>
                  <p><strong>Location:</strong> <span id="instLocation">–</span></p>
                </div>
              </div>
            </div>

            <!-- Course -->
            <div class="col-md-4">
              <div class="card dashboard-card shadow-sm">
                <div class="card-header">
                  <i class="fa-solid fa-graduation-cap text-warning fa-lg me-2"></i>
                  Course
                </div>
                <div class="card-body">
                  <p><strong>Name:</strong> <span id="courseName">–</span></p>
                  <p><strong>Type:</strong> <span id="courseType">–</span></p>
                  <p><strong>Duration:</strong> <span id="courseDuration">–</span> yr</p>
                  <p><strong>Intake:</strong> <span id="intakeInfo">–</span></p>
                </div>
              </div>
            </div>
          </div>

          <!-- Notices & Events -->
          <div class="row">
            <!-- Notices -->
            <div class="col-md-6">
              <div class="card dashboard-card shadow-sm d-flex flex-column">
                <div class="card-header">
                  <i class="fa-solid fa-bell text-danger fa-lg me-2"></i>
                  Notices
                </div>
                <div class="card-body flex-grow-1 p-0">
                  <ul id="noticeList" class="list-group list-group-flush"></ul>
                </div>
                <div class="card-footer">
                  <div id="noticePagination" class="pagination-controls text-center"></div>
                </div>
              </div>
            </div>

            <!-- Events -->
            <div class="col-md-6">
              <div class="card dashboard-card shadow-sm d-flex flex-column">
                <div class="card-header">
                  <i class="fa-solid fa-calendar-days text-info fa-lg me-2"></i>
                  Upcoming Events
                </div>
                <div class="card-body flex-grow-1 p-0">
                  <ul id="eventList" class="list-group list-group-flush"></ul>
                </div>
                <div class="card-footer">
                  <div id="eventPagination" class="pagination-controls text-center"></div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- No Profile Message -->
        <div id="noProfileMessage" class="text-center my-5 d-none">
          <img src="{{ asset('assets/web_assets/noData.png') }}" alt="No Data" class="img-fluid" style="max-width:200px;">
          <h4 class="mt-3">Complete Your Profile</h4>
          <p>Your profile isn’t active yet. Please contact your institute.</p>
        </div>
      </div>
    </div>
  </div>

  <script>
    document.addEventListener('DOMContentLoaded', () => {
      const token = sessionStorage.getItem('token');
      const uid   = sessionStorage.getItem('student_uid');
      if (!token || !uid) {
        return window.location.href = '/';
      }
      fetchDashboard(uid, token);
    });

    function viewFullImage() {
      Swal.fire({
        imageUrl: document.getElementById('studentPhoto').src,
        showConfirmButton: false,
        showCloseButton: true
      });
    }

    async function fetchDashboard(uid, token) {
      try {
        const res  = await fetch(`/api/student/dashboard/${uid}`, {
          headers: { 'Authorization': token, 'Accept': 'application/json' }
        });
        const json = await res.json();
        document.getElementById('loadingSpinner').classList.add('d-none');

        if (json.status !== 'success') throw new Error();
        populate(json);

      } catch {
        document.getElementById('loadingSpinner').classList.add('d-none');
        document.getElementById('noProfileMessage').classList.remove('d-none');
      }
    }

    function populate({ student, photo_url, institution, course, notices, events }) {
      // show dashboard
      document.getElementById('dashboardContent').classList.remove('d-none');

      // Basic
      document.getElementById('studentName').innerText  = student.name || '–';
      document.getElementById('studentEmail').innerHTML = 
        `<i class="fa-regular fa-envelope text-primary fa-lg me-1"></i>${student.email || '–'}`;
      document.getElementById('studentPhone').innerHTML = 
        `<i class="fa-solid fa-phone text-success fa-lg me-1"></i>${student.phone || '–'}`;
      if (photo_url) {
        document.getElementById('studentPhoto').src = photo_url;
      }

      // Institution
      document.getElementById('instName').innerText     = institution?.institution_name || '–';
      document.getElementById('instType').innerText     = institution?.type             || '–';
      document.getElementById('instLocation').innerText = 
        [institution?.city, institution?.state].filter(v => v).join(', ') || '–';

      // Course
      document.getElementById('courseName').innerText     = course?.program_name     || '–';
      document.getElementById('courseType').innerText     = course?.program_type     || '–';
      document.getElementById('courseDuration').innerText = course?.program_duration || '–';
      document.getElementById('intakeInfo').innerText     = 
        `${course?.intake_type || '–'} • ${course?.intake_year || '–'}`;

      paginateList('noticeList','noticePagination', notices, renderNoticeItem, 5);
      paginateList('eventList','eventPagination',  events, renderEventItem, 5);
    }

    function renderNoticeItem(n, idx, page, pageSize) {
      const isFirst = page === 1 && idx === 0;
      const date    = new Date(n.created_at).toLocaleDateString();
      return `
        <li class="list-group-item d-flex justify-content-between align-items-start">
          <div class="me-3 flex-grow-1">
            <div class="d-flex align-items-center">
              <strong>${n.title}</strong>
              ${isFirst ? '<span class="badge-new">NEW</span>' : ''}
            </div>
            <p class="mb-1 text-muted">${n.message}</p>
          </div>
          <small class="text-muted text-nowrap">${date}</small>
        </li>`;
    }

    function renderEventItem(e) {
      const date = new Date(e.event_date).toLocaleDateString();
      return `
        <li class="list-group-item">
          <div class="d-flex justify-content-between">
            <strong>${e.title}</strong>
            <small class="text-muted">${date}</small>
          </div>
          <p class="mb-0">${e.description}</p>
        </li>`;
    }

    function paginateList(listId, pagerId, items, renderFn, pageSize) {
      let page = 1, total = items.length, pages = Math.ceil(total/pageSize);
      const listEl  = document.getElementById(listId);
      const pagerEl = document.getElementById(pagerId);

      function render() {
        listEl.innerHTML = '';
        const start = (page-1)*pageSize, end = start+pageSize;
        items.slice(start, end).forEach((it,i) => {
          listEl.insertAdjacentHTML('beforeend', renderFn(it, i, page, pageSize));
        });
        pagerEl.innerHTML = `
          <button class="btn btn-sm btn-outline-primary" ${page===1?'disabled':''}
                  onclick="changePage('${listId}',${page-1})"><i class="fa-solid fa-angle-left"></i></button>
          <span class="px-2">Page ${page}/${pages}</span>
          <button class="btn btn-sm btn-outline-primary" ${page===pages?'disabled':''}
                  onclick="changePage('${listId}',${page+1})"><i class="fa-solid fa-chevron-right"></i></button>
        `;
      }

      window.changePage = (lid,newPage) => {
        if (lid !== listId) return;
        page = Math.min(Math.max(1,newPage), pages);
        render();
      };

      render();
    }
  </script>

  <!-- Bootstrap Bundle -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

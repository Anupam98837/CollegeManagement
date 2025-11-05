<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <meta http-equiv="X-UA-Compatible" content="ie=edge"/>
  <title>Agent Dashboard</title>

  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"/>

  <!-- Chart.js -->
  <script src="https://cdn.jsdelivr.net/npm/chart.js@4.2.1/dist/chart.umd.min.js"></script>

  <style>
    body { background: #f4f6f9; }
    #loadingSpinner {
      position: absolute;
      top: 50%; left: 50%;
      transform: translate(-50%, -50%);
    }
    .dashboard-card { margin-bottom: 1.5rem; }
    .summary-card {
      color: #fff;
      padding: 1rem;
      border-radius: .5rem;
    }
    .summary-card h4 { margin: .5rem 0 0; }
    .chart-container {
      background: #fff;
      padding: 1rem;
      border-radius: .5rem;
      box-shadow: 0 2px 6px rgba(0,0,0,.1);
      margin-bottom: 1.5rem;
    }
  </style>
</head>
<body>

  <div class="d-flex">
    <!-- Sidebar -->
    <div>
      @include('users.agent.components.sidebar')
    </div>

    <!-- Main -->
    <div class="w-100">

      <!-- Header -->
      @include('users.agent.components.header')

      <!-- Loading Spinner -->
      <div id="loadingSpinner">
        <div class="spinner-border text-primary" role="status"></div>
      </div>

      <!-- Dashboard Content -->
      <div id="dashboardContent" class="d-none container py-4">

        <!-- Agent Profile -->
        <div class="card dashboard-card">
          <div class="card-header"><strong>Your Profile</strong></div>
          <div class="card-body row">
            <div class="col-md-4"><strong>UID:</strong> <span id="agentUid">–</span></div>
            <div class="col-md-4"><strong>Name:</strong> <span id="agentName">–</span></div>
            <div class="col-md-4"><strong>Email:</strong> <span id="agentEmail">–</span></div>
            <div class="col-md-4 mt-2"><strong>Mobile:</strong> <span id="agentMobile">–</span></div>
            <div class="col-md-4 mt-2"><strong>Status:</strong> <span id="agentStatus">–</span></div>
          </div>
        </div>

        <!-- Summary -->
        <div class="row mb-4">
          <div class="col-md-4">
            <div class="summary-card bg-primary">
              <small>Total Students</small>
              <h4 id="totalCount">0</h4>
            </div>
          </div>
          <div class="col-md-4">
            <div class="summary-card bg-success">
              <small>Active Students</small>
              <h4 id="activeCount">0</h4>
            </div>
          </div>
          <div class="col-md-4">
            <div class="summary-card bg-danger">
              <small>Inactive Students</small>
              <h4 id="inactiveCount">0</h4>
            </div>
          </div>
        </div>

        <!-- Charts -->
        <div class="row">
          <div class="col-md-6">
            <div class="chart-container">
              <canvas id="statusChart" height="200"></canvas>
            </div>
          </div>
          <div class="col-md-6">
            <div class="chart-container">
              <canvas id="instChart" height="200"></canvas>
            </div>
          </div>
        </div>

        <!-- Registered Students List -->
        <div class="card dashboard-card">
          <div class="card-header"><strong>Your Registered Students</strong></div>
          <ul id="studentList" class="list-group list-group-flush"></ul>
          <div id="noStudents" class="p-3 text-center text-muted d-none">
            You haven’t registered any students yet.
          </div>
        </div>

      </div>
    </div>
  </div>

  <script>
    document.addEventListener("DOMContentLoaded", () => {
      const token = sessionStorage.getItem("token");
      const uid   = sessionStorage.getItem("agent_uid");
      if (!token || !uid) return window.location.href = "/";
      fetchAgentDashboard(uid, token);
    });

    async function fetchAgentDashboard(uid, token) {
      try {
        const res  = await fetch(`/api/agent/dashboard/${uid}`, {
          headers: { "Authorization": token, "Accept": "application/json" }
        });
        const json = await res.json();
        document.getElementById("loadingSpinner").classList.add("d-none");
        if (json.status !== "success") throw new Error();
        populateDashboard(json.data);
      } catch {
        document.getElementById("loadingSpinner").classList.add("d-none");
        alert("Could not load dashboard.");
      }
    }

    function populateDashboard({ agent, counts, students, by_institution }) {
      // show dashboard
      document.getElementById("dashboardContent").classList.remove("d-none");

      // Profile
      document.getElementById("agentUid").innerText    = agent.uid;
      document.getElementById("agentName").innerText   = agent.name;
      document.getElementById("agentEmail").innerText  = agent.email;
      document.getElementById("agentMobile").innerText = agent.mobile;
      document.getElementById("agentStatus").innerText = agent.status;

      // Summary cards
      document.getElementById("totalCount").innerText    = counts.total;
      document.getElementById("activeCount").innerText   = counts.active;
      document.getElementById("inactiveCount").innerText = counts.inactive;

      // Pie chart for Active vs Inactive
      new Chart(
        document.getElementById("statusChart"),
        {
          type: 'pie',
          data: {
            labels: ['Active','Inactive'],
            datasets: [{
              data: [counts.active, counts.inactive],
            }]
          }
        }
      );

      // Bar chart for students per institution
      const labels = by_institution.map(i => i.institution_name || 'Unknown');
      const data   = by_institution.map(i => i.count);
      new Chart(
        document.getElementById("instChart"),
        {
          type: 'bar',
          data: {
            labels,
            datasets: [{ label: 'Students', data }]
          },
          options: {
            scales: {
              y: { beginAtZero: true, ticks: { stepSize: 1 } }
            }
          }
        }
      );

      // Student list
      const listEl = document.getElementById("studentList");
      if (!students.length) {
        document.getElementById("noStudents").classList.remove("d-none");
      } else {
        students.forEach(s => {
          const li = document.createElement("li");
          li.className = "list-group-item";
          li.innerHTML = `
            <div class="d-flex justify-content-between">
              <div>
                <strong>${s.name}</strong><br/>
                <small>Role: ${s.role_number}</small>
              </div>
              <div class="text-end">
                <small>Status: <span class="${s.status==='Active'?'text-success':'text-danger'}">${s.status}</span></small><br/>
                <small>${new Date(s.created_at).toLocaleDateString()}</small>
              </div>
            </div>
          `;
          listEl.appendChild(li);
        });
      }
    }
  </script>

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge" />
    <title>Campus Dashboard</title>

    <!-- Bootstrap CSS -->
    <link
      href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css"
      rel="stylesheet"
    />
    <!-- Font Awesome -->
    <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"
    />

    <meta name="csrf-token" content="{{ csrf_token() }}" />

    <style>
      .search-wrapper {
        position: relative;
        margin-bottom: 1rem;
      }
      .search-wrapper .fa-search {
        position: absolute;
        top: 50%;
        left: 15px;
        transform: translateY(-50%);
        pointer-events: none;
      }
      .search-wrapper input {
        padding-left: 2.5rem;
      }
      /* Ensure accordion items take full width */
      .accordion-item {
        width: 100%;
      }
    </style>
  </head>
  <body>
        <!-- Dashboard Content -->
        <div class="container my-4">
          <!-- Top Row: 2 Cards for Total Campuses and Total Institutes -->
          <div class="row mb-4">
            <!-- Total Campus Card -->
            <div class="col-12 col-md-6 mb-3 mb-md-0">
              <div class="card h-100" style="background-color: #fff; border-left: 5px solid #007bff;">
                <div class="card-body d-flex flex-column justify-content-center text-center">
                  <h5 class="card-title text-primary">
                    <i class="fa-solid fa-building"></i> Total Campuses
                  </h5>
                  <p class="card-text" id="totalCampusCount">
                    <span class="spinner-border spinner-border-sm text-primary" role="status" aria-hidden="true"></span>
                  </p>
                </div>
              </div>
            </div>
            <!-- Total Institute Card -->
            <div class="col-12 col-md-6">
              <div class="card h-100" style="background-color: #fff; border-left: 5px solid #17a2b8;">
                <div class="card-body d-flex flex-column justify-content-center text-center">
                  <h5 class="card-title text-info">
                    <i class="fa-solid fa-university"></i> Total Institutes
                  </h5>
                  <p class="card-text" id="totalInstituteCount">
                    <span class="spinner-border spinner-border-sm text-info" role="status" aria-hidden="true"></span>
                  </p>
                </div>
              </div>
            </div>
          </div>

          <!-- White Background Container for Search & Accordion -->
          <div class="bg-white p-4 rounded mb-4">
            <!-- Search Box -->
            <div class="search-wrapper">
              <input
                type="text"
                id="accordionSearch"
                class="form-control"
                placeholder="Search campus by name..."
              />
              <i class="fa-solid fa-search"></i>
            </div>
            <!-- Accordion Container -->
            <div class="accordion" id="campusAccordion">
              <!-- Accordion items will be injected here -->
            </div>
          </div>
        </div>
    <!-- Bootstrap Bundle JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
      document.addEventListener("DOMContentLoaded", function () {
        if (!sessionStorage.getItem("token")) {
          window.location.href = "/";
        }
        fetchCampusData();
        fetchInstituteData();

        // Attach search event for the accordion
        document.getElementById("accordionSearch").addEventListener("input", filterAccordion);
      });

      const token = sessionStorage.getItem("token");
      let campuses = [];
      let institutes = [];

      // Fetch campus data from API
      function fetchCampusData() {
        fetch("/api/get-campuses", {
          method: "GET",
          headers: {
            Accept: "application/json",
            Authorization: token,
          },
        })
          .then((response) => {
            if (response.status === 401 || response.status === 403) {
              window.location.href = "/Unauthorised";
              throw new Error("Unauthorized Access");
            }
            return response.json();
          })
          .then((data) => {
            if (data.status === "success") {
              campuses = data.data;
              updateCampusCount();
              populateAccordion();
            } else {
              console.error("Error fetching campus data:", data.message);
            }
          })
          .catch((error) => {
            console.error("Error:", error);
          });
      }

      // Fetch institute data from API
      function fetchInstituteData() {
        fetch("/api/view-institutions", {
          method: "GET",
          headers: {
            Accept: "application/json",
            Authorization: token,
          },
        })
          .then((response) => {
            if (response.status === 401 || response.status === 403) {
              window.location.href = "/Unauthorised";
              throw new Error("Unauthorized Access");
            }
            return response.json();
          })
          .then((data) => {
            if (data.status === "success") {
              institutes = data.data;
              updateInstituteCount();
              populateAccordion(); // Update accordion now that institution data is available
            } else {
              console.error("Error fetching institute data:", data.message);
            }
          })
          .catch((error) => {
            console.error("Error:", error);
          });
      }

      // Update total campus count card
      function updateCampusCount() {
        document.getElementById("totalCampusCount").textContent = campuses.length;
      }

      // Update total institute count card
      function updateInstituteCount() {
        document.getElementById("totalInstituteCount").textContent = institutes.length;
      }

      // Populate the accordion with campus details and institution table
      function populateAccordion() {
        const accordion = document.getElementById("campusAccordion");
        accordion.innerHTML = "";

        if (campuses.length === 0) {
          accordion.innerHTML = '<div class="text-center">No campuses found.</div>';
          return;
        }

        campuses.forEach((campus, index) => {
          // Filter institutions for the current campus using campus_id
          const campusInstitutions = institutes.filter(
            (inst) => inst.campus_id === campus.campus_id
          );

          // Build table HTML for institutions
          let tableHTML = "";
          if (campusInstitutions.length > 0) {
            tableHTML += '<div class="table-responsive"><table class="table table-striped"><thead><tr>';
            tableHTML += '<th class="text-13 text-secondary">Institution Name</th>';
            tableHTML += '<th class="text-13 text-secondary">Short Code</th>';
            tableHTML += '<th class="text-13 text-secondary">Type</th>';
            tableHTML += '<th class="text-13 text-secondary">City</th>';
            tableHTML += '<th class="text-13 text-secondary">Status</th>';
            tableHTML += '<th class="text-13 text-secondary">Created At</th>';
            tableHTML += "</tr></thead><tbody>";

            campusInstitutions.forEach((inst) => {
              tableHTML += "<tr class='text-start'>";
              tableHTML += `<td class="text-13 align-middle">${inst.institution_name}</td>`;
              tableHTML += `<td class="text-13 align-middle">${inst.institution_short_code}</td>`;
              tableHTML += `<td class="text-13 align-middle">${inst.type}</td>`;
              tableHTML += `<td class="text-13 align-middle">${inst.city}</td>`;
              tableHTML += `<td class="text-13 align-middle">${inst.status}</td>`;
              tableHTML += `<td class="text-13 align-middle">${new Date(inst.created_at).toLocaleString()}</td>`;
              tableHTML += "</tr>";
            });

            tableHTML += "</tbody></table></div>";
          } else {
            tableHTML = '<div class="text-center">No institutions found for this campus.</div>';
          }

          // For the first item, expand by default.
          const isFirst = index === 0;
          accordion.innerHTML += `
            <div class="accordion-item" data-campus-name="${campus.campus_name.toLowerCase()}">
              <h2 class="accordion-header" id="heading${index}">
                <button class="accordion-button ${!isFirst ? "collapsed" : ""} text-13" type="button" data-bs-toggle="collapse" data-bs-target="#collapse${index}" aria-expanded="${isFirst ? "true" : "false"}" aria-controls="collapse${index}">
                  ${campus.campus_name} (ID: ${campus.campus_id})
                </button>
              </h2>
              <div id="collapse${index}" class="accordion-collapse collapse ${isFirst ? "show" : ""}" aria-labelledby="heading${index}" data-bs-parent="#campusAccordion">
                <div class="accordion-body">
                  ${tableHTML}
                </div>
              </div>
            </div>
          `;
        });
      }

      // Filter accordion items based on campus name
      function filterAccordion() {
        const query = document.getElementById("accordionSearch").value.toLowerCase();
        const items = document.querySelectorAll(".accordion-item");

        items.forEach((item) => {
          const campusName = item.getAttribute("data-campus-name");
          if (campusName.includes(query)) {
            item.style.display = "";
          } else {
            item.style.display = "none";
          }
        });
      }
    </script>
  </body>
</html>

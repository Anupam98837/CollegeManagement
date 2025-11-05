<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>
    <!-- Bulk CSV Upload Segment (button aligned right) -->
    <div class="bulk-upload-section">
        <button id="openBulkUpload" class="btn btn-outline-primary btn-sm text-13">
          <i class="fa-solid fa-users me-1"></i>Add Bulk Student
        </button>
      </div>
      <script>
        document
          .getElementById('openBulkUpload')
          .addEventListener('click', function () {
            // 1) Show a SweetAlert file chooser
            Swal.fire({
              title: 'Select your CSV file',
              text: "Only .csv allowed",
              icon: 'info',
              showCancelButton: true,
              confirmButtonText: 'Upload',
              cancelButtonText: 'Cancel',
              input: 'file',
              inputAttributes: {
                accept: '.csv',
                'aria-label': 'Upload your CSV file'
              },
              inputValidator: (file) => {
                return new Promise((resolve) => {
                  if (!file) {
                    resolve('You need to select a CSV file');
                  } else {
                    const ext = file.name.split('.').pop().toLowerCase();
                    if (ext !== 'csv') {
                      resolve('Only .csv files are allowed');
                    } else {
                      resolve();
                    }
                  }
                });
              }
            }).then((result) => {
              if (result.isConfirmed && result.value) {
                const csvFile = result.value;
                const formData = new FormData();
                formData.append('csv_file', csvFile);
                formData.append('_token', document.querySelector('input[name="_token"]').value);
    
                const token       = sessionStorage.getItem('token');
                const designation = sessionStorage.getItem('designation');
    
                // 2) Show a "Uploading..." loading Swal
                Swal.fire({
                  title: 'Uploading...',
                  text: 'Please wait while we process your CSV',
                  allowOutsideClick: false,
                  didOpen: () => {
                    Swal.showLoading();
                  }
                });
    
                // 3) Send the CSV via fetch
                fetch('/api/bulk-register-students', {
                  method: 'POST',
                  body: formData,
                  headers: {
                    'Accept': 'application/json',
                    'Authorization': token,
                    'Designation': designation
                  },
                })
                .then(response => {
                  if (response.status === 401 || response.status === 403) {
                    window.location.href = '/Unauthorised';
                    throw new Error('Unauthorized Access');
                  }
                  return response.json();
                })
                .then(data => {
                  // 4) Close the loading Swal
                  Swal.close();
    
                  if (data.status === 'partial' || data.status === 'success') {
                    // Build inserted rows string
                    const insertedStr = Array.isArray(data.inserted) && data.inserted.length
                      ? data.inserted.join(', ')
                      : 'None';
    
                    // Build failed rows string
                    let failedStr = 'None';
                    if (Array.isArray(data.failed) && data.failed.length) {
                      failedStr = data.failed.map(f => {
                        let index = (f.row - 1)
                        let msg = `Row ${index}`;
                        if (f.message) {
                          msg += ` (${f.message})`;
                        }
                        if (f.errors && Array.isArray(f.errors)) {
                          msg += ` [${f.errors.join('; ')}]`;
                        }
                        return msg;
                      }).join('<br>');
                    }
    
                    // 5) Show results in a new Swal
                    Swal.fire({
                      title: 'Bulk Upload Result',
                      icon: 'success',
                      html: `
                        <div style="text-align:left">
                          <strong>Inserted rows:</strong> ${insertedStr}<br><br>
                          <strong>Failed rows:</strong><br>${failedStr}
                        </div>
                      `,
                      width: 600
                    });
                  } else {
                    // If the server returned an error status
                    Swal.fire({
                      title: 'Upload Failed',
                      icon: 'error',
                      text: data.message || 'Bulk upload failed.'
                    });
                  }
                })
                .catch(err => {
                  console.error('Bulk CSV upload error:', err);
                  Swal.close();
                  Swal.fire({
                    title: 'Error',
                    icon: 'error',
                    text: 'An error occurred while uploading.'
                  });
                });
              }
            });
          });
      </script>
</body>
</html>
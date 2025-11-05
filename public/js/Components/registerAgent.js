document.addEventListener("DOMContentLoaded", function() {
    if (!sessionStorage.getItem("token")) {
      // Redirect to blank path or your preferred path if token is missing.
      window.location.href = "/";
    }
});
let currentStep = 1;

function changeStep(step) {
    const steps = document.querySelectorAll('.step');
    steps[currentStep - 1].classList.remove('active');
    currentStep += step;

    if (currentStep <= 1) {
        currentStep = 1;
        document.getElementById('prevBtn').style.display = 'none';
    } else {
        document.getElementById('prevBtn').style.display = 'block';
    }

    if (currentStep >= steps.length) {
        currentStep = steps.length;
        document.getElementById('nextBtn').style.display = 'none';
        document.getElementById('agent_ragister_submitBtn').style.display = 'block';
    } else {
        document.getElementById('nextBtn').style.display = 'block';
        document.getElementById('agent_ragister_submitBtn').style.display = 'none';
    }

    steps[currentStep - 1].classList.add('active');
    document.getElementById('progressBar').style.width = `${(currentStep / steps.length) * 100}%`;
}

// NEW: Helper function to change directly to a given step
function changeStepTo(stepNumber) {
    const steps = document.querySelectorAll('.step');
    steps.forEach((step, index) => {
        step.classList.remove('active');
        if (index === stepNumber - 1) {
            step.classList.add('active');
            currentStep = stepNumber;
        }
    });
    // Update navigation buttons based on the new step
    if (currentStep <= 1) {
        document.getElementById('prevBtn').style.display = 'none';
    } else {
        document.getElementById('prevBtn').style.display = 'block';
    }
    if (currentStep >= steps.length) {
        document.getElementById('nextBtn').style.display = 'none';
        document.getElementById('agent_ragister_submitBtn').style.display = 'block';
    } else {
        document.getElementById('nextBtn').style.display = 'block';
        document.getElementById('agent_ragister_submitBtn').style.display = 'none';
    }
    document.getElementById('progressBar').style.width = `${(currentStep / steps.length) * 100}%`;
}

function toggleWhatsAppNumber() {
    const mobileField = document.getElementById('mobile');
    const whatsappField = document.getElementById('whatsapp');
    const sameAsMobile = document.getElementById('sameAsMobile');

    if (sameAsMobile.checked) {
        whatsappField.value = mobileField.value;
        // whatsappField.disabled = true;
    } else {
        whatsappField.value = '';
        // whatsappField.disabled = false;
    }
}

document.getElementById('agentRegisterForm').addEventListener('submit', function (e) {
    e.preventDefault();
});

// Updated submit event with required field validation
document.getElementById('agentRegisterForm').addEventListener('submit', function (e) {
    e.preventDefault();

    // NEW: Remove any existing error messages
    document.querySelectorAll(".error-message").forEach(msg => msg.remove());

    // NEW: Validate required fields
    let missingFields = [];
    const requiredFields = this.querySelectorAll("[required]");
    requiredFields.forEach(field => {
        const fieldValue = field.value.trim();
        if (!fieldValue) {
            const label = document.querySelector(`label[for="${field.id}"]`);
            const labelText = label ? label.textContent.replace("*", "").trim() : field.name;
            missingFields.push({ field: field, labelText: labelText });
        }
    });

    if (missingFields.length > 0) {
        // NEW: Log missing field labels in console
        console.log("Missing fields:", missingFields.map(item => item.labelText));

        // NEW: Redirect to the step containing the first missing field
        const missingField = missingFields[0];
        let stepElement = missingField.field.closest(".step");
        if (stepElement && stepElement.id) {
            const stepNumber = parseInt(stepElement.id.replace("step", ""));
            changeStepTo(stepNumber);
        }

        // NEW: Append an error message below each missing field
        missingFields.forEach(item => {
            let parent = item.field.parentElement;
            if (!parent.querySelector(".error-message")) {
                const errorMessage = document.createElement("div");
                errorMessage.className = "error-message text-danger text-10 mt-1";
                errorMessage.textContent = "This field is required.";
                parent.appendChild(errorMessage);
            }
        });

        Swal.fire("Missing Fields", "Please fill in the required fields highlighted.", "error");
        return;
    }

    const submitBtn = document.getElementById('agent_ragister_submitBtn');
    submitBtn.disabled = true; // Disable the submit button
    submitBtn.innerHTML = `<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Submitting...`;

    const formData = new FormData(this);
    const token = sessionStorage.getItem('token');
    fetch('/api/register-agent', {
        method: 'POST',
        body: formData,
        headers: {
            'Accept': 'application/json',
            'Authorization': `${token}`
        },
    })
    .then(response => {
        if (response.status === 401 || response.status === 403) {
            // ✅ Redirect if unauthorized
            window.location.href = '/Unauthorised';
            throw new Error('Unauthorized Access');
        }
        return response.json();
    })
    .then(data => {
        if (data.status === 'success') {
            Swal.fire('Success', data.message, 'success');
            this.reset(); // Reset form after success
            changeStep(-currentStep + 1); // Go back to step 1
        } else {
            Swal.fire('Error', data.message || 'Validation failed', 'error');
        }
    })
    .catch(error => {
        Swal.fire('Error', 'Something went wrong', 'error');
        console.error('Error:', error);
    })
    .finally(() => {
        submitBtn.disabled = false; // Re-enable the submit button
        submitBtn.innerHTML = 'Submit'; // Reset button text
    });
});

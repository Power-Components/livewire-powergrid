document.getElementById('loginForm').addEventListener('input', function () {
    const name = document.getElementById('name').value;
    const sapid = document.getElementById('sapid').value;
    const course = document.getElementById('course').value;
    const branch = document.getElementById('branch').value;
    const batchNo = document.getElementById('batchNo').value;
    const studyYear = document.getElementById('studyYear').value;
    const email = document.getElementById('email').value;
    const button = document.getElementById('accessButton');

    // Enable button only if all fields are filled
    if (name && sapid && course && branch && batchNo && studyYear && email) {
        button.disabled = false;
    } else {
        button.disabled = true;
    }
});

function validateForm(event) {
    event.preventDefault();

    // Simulate loading state
    const buttonText = document.getElementById('buttonText');
    const spinner = document.getElementById('loadingSpinner');
    buttonText.style.display = 'none';
    spinner.style.display = 'block';

    setTimeout(() => {
        const name = document.getElementById('name').value;
        const email = document.getElementById('email').value;
        const successMessage = document.getElementById('successMessage');

        // Display success message
        successMessage.textContent = `Welcome, ${name}! Redirecting to the calculator...`;
        successMessage.style.display = 'block';

        // Reset form and button
        setTimeout(() => {
            document.getElementById('loginForm').reset();
            buttonText.style.display = 'block';
            spinner.style.display = 'none';
            successMessage.style.display = 'none';
        }, 2000);
    }, 1500);
}
document.addEventListener("DOMContentLoaded", () => {

  // Reset Password Modal Logic
  // Retrieve the reset form element from the DOM using its ID
  const resetForm = document.getElementById("resetForm");
  // Retrieve the success modal element from the DOM using its ID
  const resetSuccessModal = document.getElementById("resetSuccessModal");
  // Retrieve the "OK" button element inside the modal from the DOM using its ID
  const resetOkBtn = document.getElementById("resetOkBtn");

  // Check if all necessary elements exist on the current page to avoid errors on pages without them
  if (resetForm && resetSuccessModal && resetOkBtn) {
    // Add an event listener to the form that triggers when the user attempts to submit it
    resetForm.addEventListener("submit", (e) => {
      // Prevent the default form submission behavior (which would reload the page)
      e.preventDefault(); 
      
      // Get the value typed into the "new password" input field
      const newPassword = document.getElementById("newPassword").value;
      // Get the value typed into the "confirm password" input field
      const confirmPassword = document.getElementById("confirmPassword").value;
      
      // Check if the entered passwords do not match
      if (newPassword !== confirmPassword) {
        // Show an alert to the user if passwords don't match
        alert("Passwords do not match!");
        // Stop the function execution here so the modal isn't shown
        return;
      }
      
      // If passwords match, change the modal's display style to "flex" to make it visible
      resetSuccessModal.style.display = "flex";
    });

    // Add a click event listener to the "OK" button inside the success modal
    resetOkBtn.onclick = () => {
      // Hide the modal by changing its display style back to "none"
      resetSuccessModal.style.display = "none";
      // Redirect the user to the login page after successfully changing the password
      window.location.href = "login.html"; 
    };

    // Add a click event listener to the entire window
    window.addEventListener("click", (event) => {
      // Check if the area clicked by the user is the modal overlay background itself (not its contents)
      if (event.target === resetSuccessModal) {
        // Hide the modal if the user clicks outside the modal content box
        resetSuccessModal.style.display = "none";
      }
    });
  }
  // Forget Password Modal Logic
  // Get the form and modal elements
  const forgetForm = document.getElementById('forgetPasswordForm');
  const emailSentModal = document.getElementById('emailSentModal');
  const modalOkBtn = document.getElementById('modalOkBtn');

  // Check if we are on the forget password page before adding listeners
  if (forgetForm && emailSentModal && modalOkBtn) {
    // Event listener for form submission
    forgetForm.addEventListener('submit', function(e) {
      // Prevent the default HTML form submission which would reload the page
      e.preventDefault();
      // Display the modal by changing its display styling from 'none' to 'flex'
      emailSentModal.style.display = 'flex';
    });

    // Event listener for the modal's OK button click
    modalOkBtn.addEventListener('click', function() {
      // Redirect the user back to the login page
      window.location.href = 'login.html';
    });
  }

});
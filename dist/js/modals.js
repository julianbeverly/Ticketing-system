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
  const forgetForm = document.getElementById("forgetPasswordForm");
  const emailSentModal = document.getElementById("emailSentModal");
  const modalOkBtn = document.getElementById("modalOkBtn");

  // Check if we are on the forget password page before adding listeners
  if (forgetForm && emailSentModal && modalOkBtn) {
    // Event listener for form submission
    forgetForm.addEventListener("submit", function (e) {
      // Prevent the default HTML form submission which would reload the page
      e.preventDefault();
      // Display the modal by changing its display styling from 'none' to 'flex'
      emailSentModal.style.display = "flex";
    });

    // Event listener for the modal's OK button click
    modalOkBtn.addEventListener("click", function () {
      // Redirect the user back to the login page
      window.location.href = "login.html";
    });
  }

  // modal for adding users
  const addUserBtn = document.getElementById("addUserBtn");
  const modal = document.getElementById("modal");
  const closeBtn = document.getElementById("closeBtn");

  const roleSelect = document.getElementById("roleSelect");

  const adminBox = document.getElementById("adminBox");
  const techBox = document.getElementById("techBox");
  const employeeBox = document.getElementById("employeeBox");

  const submitBtn = document.getElementById("submitBtn");

  // Only attach user modal listeners if the required elements exist on this page
  if (addUserBtn && modal && closeBtn && roleSelect && submitBtn) {

    // OPEN MODAL
    addUserBtn.addEventListener("click", () => {
      modal.style.display = "flex";

      // RESET EVERYTHING WHEN MODAL OPENS
      roleSelect.value = "";

      adminBox.style.display = "none";
      techBox.style.display = "none";
      employeeBox.style.display = "none";

      submitBtn.style.display = "none";
      submitBtn.textContent = "Select Role First";
    });

    // CLOSE MODAL
    closeBtn.addEventListener("click", () => {
      modal.style.display = "none";
    });

    // ROLE CHANGE
    roleSelect.addEventListener("change", () => {
      // show correct section
      adminBox.style.display = "none";
      techBox.style.display = "none";
      employeeBox.style.display = "none";

      // SHOW BUTTON ONLY AFTER SELECTION
      submitBtn.style.display = "block";

      if (roleSelect.value === "admin") {
        adminBox.style.display = "block";
        submitBtn.textContent = "Create Admin";
      } else if (roleSelect.value === "technician") {
        techBox.style.display = "block";
        submitBtn.textContent = "Create Technician";
      } else if (roleSelect.value === "employee") {
        employeeBox.style.display = "block";
        submitBtn.textContent = "Create Employee";
      }
    });

    // SUBMIT ACTION
    submitBtn.addEventListener("click", () => {
      if (!roleSelect.value) return;

      alert(roleSelect.value + " created successfully!");

      modal.style.display = "none";
    });

  }

  // ================================================================
  // INCIDENT MANAGEMENT (dent.html) - Tab Switching & Modal Logic
  // ================================================================

  // --- Tab Switching Logic ---
  // Get references to the tab buttons
  const dentTabCategories = document.getElementById("dent-tab-categories");
  const dentTabTypes = document.getElementById("dent-tab-types");

  // Get references to the content views for each tab
  const dentTypesView = document.getElementById("dent-types-view");
  const dentCategoriesView = document.getElementById("dent-categories-view");

  // Get references to the action buttons that change per tab
  const dentAddTypeBtn = document.getElementById("dent-add-type-btn");
  const dentAddCategoryBtn = document.getElementById("dent-add-category-btn");

  // Get reference to the page title heading
  const dentPageTitle = document.getElementById("dent-page-title");

  // Only attach listeners if the tab elements exist (prevents errors on other pages)
  if (dentTabCategories && dentTabTypes) {

    // When the "Categories" tab is clicked
    dentTabCategories.addEventListener("click", () => {
      // Update active state on tabs
      dentTabCategories.classList.add("active");
      dentTabTypes.classList.remove("active");

      // Show Categories view, hide Types view
      dentCategoriesView.style.display = "block";
      dentTypesView.style.display = "none";

      // Show "Add New Category" button, hide "Add New Type" button
      dentAddCategoryBtn.style.display = "flex";
      dentAddTypeBtn.style.display = "none";

      // Update the page title to reflect the current view
      dentPageTitle.textContent = "Incident Configuration";
    });

    // When the "Types" tab is clicked
    dentTabTypes.addEventListener("click", () => {
      // Update active state on tabs
      dentTabTypes.classList.add("active");
      dentTabCategories.classList.remove("active");

      // Show Types view, hide Categories view
      dentTypesView.style.display = "block";
      dentCategoriesView.style.display = "none";

      // Show "Add New Type" button, hide "Add New Category" button
      dentAddTypeBtn.style.display = "flex";
      dentAddCategoryBtn.style.display = "none";

      // Update the page title to reflect the current view
      dentPageTitle.textContent = "Incident Management";
    });
  }

  // --- Add New Type Modal Logic ---
  // Get references to the Type modal and its control buttons
  const dentTypeModal = document.getElementById("dent-type-modal");
  const dentCloseTypeModal = document.getElementById("dent-close-type-modal");

  // Open the Type modal when "Add New Type" button is clicked
  if (dentAddTypeBtn && dentTypeModal) {
    dentAddTypeBtn.addEventListener("click", () => {
      dentTypeModal.classList.add("show");
    });
  }

  // Close the Type modal when "Cancel" button inside it is clicked
  if (dentCloseTypeModal && dentTypeModal) {
    dentCloseTypeModal.addEventListener("click", () => {
      dentTypeModal.classList.remove("show");
    });
  }

  // Close the Type modal when clicking outside the modal box (on the overlay)
  if (dentTypeModal) {
    dentTypeModal.addEventListener("click", (e) => {
      if (e.target === dentTypeModal) {
        dentTypeModal.classList.remove("show");
      }
    });
  }

  // --- Add New Category Modal Logic ---
  // Get references to the Category modal and its control buttons
  const dentCategoryModal = document.getElementById("dent-category-modal");
  const dentCloseCategoryModal = document.getElementById("dent-close-category-modal");

  // Open the Category modal when "Add New Category" button is clicked
  if (dentAddCategoryBtn && dentCategoryModal) {
    dentAddCategoryBtn.addEventListener("click", () => {
      dentCategoryModal.classList.add("show");
    });
  }

  // Close the Category modal when "Cancel" button inside it is clicked
  if (dentCloseCategoryModal && dentCategoryModal) {
    dentCloseCategoryModal.addEventListener("click", () => {
      dentCategoryModal.classList.remove("show");
    });
  }

  // Close the Category modal when clicking outside the modal box (on the overlay)
  if (dentCategoryModal) {
    dentCategoryModal.addEventListener("click", (e) => {
      if (e.target === dentCategoryModal) {
        dentCategoryModal.classList.remove("show");
      }
    });
  }




  // ================================================================
  // RESOLVE TICKET MODAL (techdetails.html)
  // ================================================================

  const resolveTicketBtn = document.getElementById("resolveTicketBtn");
  const resolveModal = document.getElementById("resolveModal");
  const closeResolveModal = document.getElementById("closeResolveModal");
  const cancelResolveBtn = document.getElementById("cancelResolveBtn");
  const resolveTicketForm = document.getElementById("resolveTicketForm");
  const completionTimestamp = document.getElementById("completionTimestamp");

  // Only attach listeners if the required elements exist on this page
  if (resolveTicketBtn && resolveModal) {

    // Function to format current date/time (YYYY-MM-DD HH:MM:SS)
    const formatCurrentTime = () => {
      const now = new Date();
      const year = now.getFullYear();
      const month = String(now.getMonth() + 1).padStart(2, '0');
      const day = String(now.getDate()).padStart(2, '0');
      const hours = String(now.getHours()).padStart(2, '0');
      const minutes = String(now.getMinutes()).padStart(2, '0');
      const seconds = String(now.getSeconds()).padStart(2, '0');
      return `${year}-${month}-${day} ${hours}:${minutes}:${seconds}`;
    };

    // OPEN MODAL
    resolveTicketBtn.addEventListener("click", (e) => {
      e.preventDefault();
      // Set the current timestamp when opening the modal
      if (completionTimestamp) {
        completionTimestamp.value = formatCurrentTime();
      }
      resolveModal.classList.add("show");
    });

    // CLOSE MODAL (X Button)
    if (closeResolveModal) {
      closeResolveModal.addEventListener("click", () => {
        resolveModal.classList.remove("show");
      });
    }

    // CLOSE MODAL (Cancel Button)
    if (cancelResolveBtn) {
      cancelResolveBtn.addEventListener("click", () => {
        resolveModal.classList.remove("show");
      });
    }

    // CLOSE MODAL (Click Overlay)
    resolveModal.addEventListener("click", (e) => {
      if (e.target === resolveModal) {
        resolveModal.classList.remove("show");
      }
    });

    // HANDLE FORM SUBMISSION
    if (resolveTicketForm) {
      resolveTicketForm.addEventListener("submit", (e) => {
        e.preventDefault();

        // Show success message (can be expanded to real backend call)
        alert("Ticket Successfully Marked as Resolved!");

        // Close modal and reset form
        resolveModal.classList.remove("show");
        resolveTicketForm.reset();
      });
    }
  }

});

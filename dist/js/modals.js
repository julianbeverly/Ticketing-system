document.addEventListener("DOMContentLoaded", () => {

  const sendLinkBtn = document.getElementById("sendLinkBtn");
  const successModal = document.getElementById("successModal");
  const okBtn = document.getElementById("okBtn");

  // Open modal only when Send Link is clicked
  sendLinkBtn.onclick = () => {
    // You can add form validation or email sending here
    successModal.style.display = "flex";
  };

  // Close modal when OK button is clicked
  okBtn.onclick = () => {
    successModal.style.display = "none";
  };

  // Close modal when clicking outside the modal content
  window.onclick = (event) => {
    if (event.target === successModal) {
      successModal.style.display = "none";
    }
  };
});
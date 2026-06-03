<!doctype html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>OTP Verification</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />
    <link rel="stylesheet" href="/dist/css/style.css" />
  </head>
  <body>
    <!-- <div class="otp-wrapper"> -->

    <div class="otpcontainer">
      <div class="otpsidebar">
        <img
          src="/dist/image/ChatGPT Image Apr 9, 2026, 01_54_08 PM.png"
          alt="sidepicture"
        />
      </div>
      <div class="otpside">
        <h2 class="otp-title">Resolve</h2>
        <div class="otp-card">
          <p class="veri">OTP Verification</p>
          <p class="info">Email the 6-digit code sent to you</p>
          <!-- <p class="label">Enter OTP</p> -->

          @if(session('error'))
    <p style="color:red;">{{ session('error') }}</p>
@endif

@if(session('success'))
    <p style="color:green;">{{ session('success') }}</p>
@endif

@if ($errors->any())
    @foreach ($errors->all() as $error)
        <p style="color:red;">{{ $error }}</p>
    @endforeach
@endif
         <form method="POST" action="{{ route('authentication.otp.verify') }}">
    @csrf

    <div class="otp-inputs">
        <input type="text" maxlength="1" class="digit">
        <input type="text" maxlength="1" class="digit">
        <input type="text" maxlength="1" class="digit">
        <input type="text" maxlength="1" class="digit">
        <input type="text" maxlength="1" class="digit">
        <input type="text" maxlength="1" class="digit">
    </div>

    <input type="hidden" name="otp" id="otp">

    <button class="btn"type="submit">Verify</button>
</form>
         <div class="resend-wrapper">
    <p class="resend-text">
        Didn't receive the code?
    </p>

    <form action="{{ route('authentication.otp.resend') }}" method="POST">
        @csrf
        <button type="submit" class="resend-btn">
            Resend OTP
        </button>
    </form>
</div>
          <div class="divider"></div>
          <div style="text-align: center; margin-top: 1rem;">
              <a href="{{ route('login') }}" style="font-size: 0.85rem; color: #2563eb; text-decoration: none; font-weight: 600;">
                  <i class="fa-solid fa-arrow-left" style="margin-right: 4px;"></i> Back to Login
              </a>
          </div>
        </div>
      </div>
    </div>

    <script>
// Select the main form and all digit input fields
const form = document.querySelector("form");
const digits = document.querySelectorAll(".digit");
const hiddenInput = document.getElementById("otp");

// Iterate through each digit input to add event listeners
digits.forEach((input, index) => {
    // Event: User types a character
    input.addEventListener("input", (e) => {
        const value = e.target.value;
        // If a digit is entered and there's a next box, move focus forward
        if (value.length === 1 && index < digits.length - 1) {
            digits[index + 1].focus();
        }
        updateHiddenInput(); // Update the hidden field for submission
    });

    // Event: User presses a key (handling navigation and backspace)
    input.addEventListener("keydown", (e) => {
        // If Backspace is pressed on an empty box, move focus to the previous box
        if (e.key === "Backspace" && !input.value && index > 0) {
            digits[index - 1].focus();
        } 
        // Allow arrow keys to navigate between boxes manually
        else if (e.key === "ArrowLeft" && index > 0) {
            digits[index - 1].focus();
        } else if (e.key === "ArrowRight" && index < digits.length - 1) {
            digits[index + 1].focus();
        }
    });

    // Event: User pastes a code into a box
    input.addEventListener("paste", (e) => {
        e.preventDefault();
        // Get the pasted text, trim whitespace, and limit to 6 characters
        const data = e.clipboardData.getData("text").trim().slice(0, digits.length);
        if (!data) return;

        // Split the pasted string into characters and distribute them across inputs
        const chars = data.split("");
        chars.forEach((char, i) => {
            if (digits[i]) {
                digits[i].value = char;
            }
        });

        // Sync the hidden input and focus the correct field (either next empty or last)
        updateHiddenInput();
        const nextFocus = Math.min(chars.length, digits.length - 1);
        digits[nextFocus].focus();
    });
});

/**
 * Combines all individual digit values into the single hidden 'otp' field
 */
function updateHiddenInput() {
    hiddenInput.value = Array.from(digits).map(input => input.value).join('');
}

// Ensure the code is complete before allowing form submission
form.addEventListener("submit", function (e) {
    updateHiddenInput();
    if (hiddenInput.value.length < digits.length) {
        e.preventDefault();
        alert("Please enter the complete " + digits.length + "-digit code.");
    }
});
</script>
    <!-- </div> -->
  </body>
</html>

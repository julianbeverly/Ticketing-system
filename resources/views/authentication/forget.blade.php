<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>forget password</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="/dist/css/style.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
    
    <div class="forgcontainer">
        <div class="forgsidebar">
         <img src="/dist/image/ChatGPT Image Apr 9, 2026, 01_54_08 PM.png" alt="sidepicture">
        </div>
            <div class="forgetpass">
                <h2 class="forget-title">Resolve</h2>
             <div class="forget-card">
                  <div class="logicon">
                   </div>
                   
                        <p class="forgt">Forgot Password?</p>
                        <!-- <i class="fa-solid fa-lock"></i> -->
                  
               
                  <!-- Form that handles sending the reset link -->
                  <form id="forgetPasswordForm" action="{{ route('password.email') }}" method="POST">
                      @csrf
                     <div class="input-email">
                          <label for="forget-email" style="display: block; font-size: 0.85rem; font-weight: 600; color: #374151; margin-bottom: 6px;">Enter your email</label>
                          <div style="position: relative; display: flex; align-items: center;">
                              <i class="fa-regular fa-envelope" style="position: absolute; left: 12px; color: #9ca3af;"></i>
                              <!-- Input field for the user's email, marked as required to prevent empty submissions -->
                              <input id="forget-email" type="email" name="email" placeholder="Email Address" required style="padding-left: 38px; width: 100%;">
                          </div>
                      </div>

                      <!-- Submit button to trigger the form submission -->
                      <button type="submit" class="sendlink-btn"><i class="fa-regular fa-paper-plane"></i> Send Link</button>
                 </form>
                 <div style="text-align: center; margin-top: 1rem;">
                     <a href="{{ route('login') }}" style="font-size: 0.85rem; color: #111111; text-decoration: none; font-weight: 600;">
                         <i class="fa-solid fa-arrow-left" style="margin-right: 4px;"></i> Back to Login
                     </a>
                 </div>
             </div>
           </div>
        
    </div>

    @if(session('success'))
    <script>
        Swal.fire({
            title: 'Email Sent!',
            text: "{{ session('success') }}",
            icon: 'success',
            confirmButtonText: 'OK',
            confirmButtonColor: '#FBBF24'
        });
    </script>
    @endif

    @if(session('error'))
    <script>
        Swal.fire({
            title: 'Error!',
            text: "{{ session('error') }}",
            icon: 'error',
            confirmButtonText: 'OK',
            confirmButtonColor: '#FBBF24'
        });
    </script>
    @endif

    @if($errors->any())
    <script>
        Swal.fire({
            title: 'Validation Error!',
            text: "{{ $errors->first() }}",
            icon: 'warning',
            confirmButtonText: 'OK',
            confirmButtonColor: '#FBBF24'
        });
    </script>
    @endif
   
</body>
</html>

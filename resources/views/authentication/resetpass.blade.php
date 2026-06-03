<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>reset password</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="/dist/css/style.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>


    <div class="resetcontainer">
         <div class="resetsidebar">
           <img src="/dist/image/ChatGPT Image Apr 9, 2026, 01_54_08 PM.png" alt="sidepicture">
        </div>
        <div class="resetside">
            <h2 class="reset-title">Resolve</h2>
            <div class="resetcard">
                <h2>Reset Your Password</h2>
                <form id="resetForm">
                    @csrf
                    <input type="hidden" name="token" value="{{ $token }}">
                    <input type="hidden" name="email" value="{{ $email }}">
                    <div class="input-password">
                        <div style="margin-bottom: 1rem;">
                            <label for="newPassword" style="display: block; font-size: 0.85rem; font-weight: 600; color: #374151; margin-bottom: 6px;">Enter New Password</label>
                            <input type="password" name="password" id="newPassword" placeholder="New password" required style="width: 100%;">
                        </div>
                        <div>
                            <label for="confirmPassword" style="display: block; font-size: 0.85rem; font-weight: 600; color: #374151; margin-bottom: 6px;">Confirm Password</label>
                            <input type="password" name="password_confirmation" id="confirmPassword" placeholder="Confirm password" required style="width: 100%;">
                        </div>
                    </div>
                    <button type="submit" class="reset-btn">Submit</button>
                </form>
                <div style="text-align: center; margin-top: 1rem;">
                    <a href="{{ route('login') }}" style="font-size: 0.85rem; color: #2563eb; text-decoration: none; font-weight: 600;">
                        <i class="fa-solid fa-arrow-left" style="margin-right: 4px;"></i> Back to Login
                    </a>
                </div>
            </div>
        </div>

    </div>
    
    <script>
        document.getElementById('resetForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            
            fetch("{{ route('password.update') }}", {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        title: 'Success!',
                        text: 'You have successfully reset your password.',
                        icon: 'success',
                        confirmButtonText: 'OK',
                        confirmButtonColor: '#0b57d0'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href = "{{ route('login') }}";
                        }
                    });
                } else {
                    Swal.fire({
                        title: 'Error!',
                        text: data.error || 'Something went wrong.',
                        icon: 'error',
                        confirmButtonText: 'OK',
                        confirmButtonColor: '#0b57d0'
                    });
                }
            })
            .catch(error => {
                Swal.fire({
                    title: 'Error!',
                    text: 'An unexpected error occurred.',
                    icon: 'error',
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#0b57d0'
                });
            });
        });
    </script>
</body>
</html>

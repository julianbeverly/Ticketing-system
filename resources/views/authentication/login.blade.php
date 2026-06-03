<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login page</title>
   <link rel="stylesheet" href="/dist/css/style.css" />
</head>

<body>

  <div class="container">
    <div class="sidebar">
      <img src="/dist/image/ChatGPT Image Apr 9, 2026, 01_54_08 PM.png" alt="sidepicture">

    </div>

    <div class="rightside">
      <h2 class="login-title">Resolve</h2>

      <div class="login-card">
        <h3>Sign in</h3>
       @if(session('error'))
    <p style="color:red;">
        {{ session('error') }}
    </p>
@endif
        <form method="POST" action="{{ route('authentication.login') }}">
            @csrf

          <div class="input-group">
            <label class="input-label" for="email">Enter Email</label>
            <input type="email" id="email" name="email" placeholder="Email Address" required>
          </div>

          <div class="input-group">
            <label class="input-label" for="password">Enter Password</label>
            <input type="password" id="password" name="password" placeholder="Password" required>
          </div>

          <button class="login-btn">Login</button>

          <a href="{{ route('password.request') }}">
            <p class="forgot">Forgot your password?</p>
          </a>
        </form>
      </div>


    </div>

  </div>



</body>

</html>

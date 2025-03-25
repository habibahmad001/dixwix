<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Forgot Password</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
  <link rel="stylesheet" href="<?=url('assets/css/style.css')?>">
  <script src="<?=url('assets/js/sweetalert2.min.js')?>"></script>
  <link rel="stylesheet" href="<?=url('assets/css/sweetalert2.min.css')?>">
</head>
<body>
  <div class="container-fluid container_bg">
    <div id="header">
      @include('common.wo_login.header')
    </div>
    <div id="content">
      <div class="form_container">
        <div class="form_image">
          <img src="<?=url('assets/media/account.png')?>" alt="Image Description">
        </div>
        <div class="form_inner">
          <div class="form_wrapper">
            @if (session('status'))
              <div class="alert alert-success">{{ session('status') }}</div>
            @endif
            @if ($errors->any())
              <div class="alert alert-danger">
                <ul>
                  @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                  @endforeach
                </ul>
              </div>
            @endif

            <form method="POST" action="{{ route('password.email') }}">
              @csrf

              <h2>Forgot Your Password?</h2>

              <div class="fieldset">
                <img src="<?=url('assets/media/email.png')?>">
                <input type="email" id="user_email" name="email" value="{{ old('email') }}" class="form-control" placeholder="Email" required>
              </div>

              <button type="submit" class="btn btn-primary" id="forgot_password_submit">Send Password Reset Link</button>
            </form>
          </div>
        </div>
      </div>
    </div>

    <div id="footer">
    </div>
  </div>

  <!-- Bootstrap JS and jQuery CDN -->
  <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
  <script src="<?=url('assets/js/scripts4.js')?>"></script>
</body>

</html>

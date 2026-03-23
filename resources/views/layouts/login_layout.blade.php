<!doctype html>
<html class=no-js lang=zxx>
<!DOCTYPE html>
<html lang="en-US" class="h-100">

<head>
  <meta charset=utf-8>
  <meta http-equiv=x-ua-compatible content="ie=edge">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Women And Child Development Department</title>
  <meta name=viewport content="width=device-width,initial-scale=1,shrink-to-fit=no">
  <!-- Favicons -->
  <link href="{{ asset('assets/img/cg-logo.png') }}" rel="icon">
  <link href="{{ asset('assets/img/cg-logo.png') }}" rel="apple-touch-icon">


  <link rel="stylesheet" href="{{ url('login-page-assets/css/loginstyle.css') }}">
  <link rel=stylesheet href="{{url('/login-page-assets/css/app.min.css')}}">
  <link rel=stylesheet href="{{url('/login-page-assets/css/site.css')}}">
  <link rel=stylesheet href="{{url('/login-page-assets/css/bootstrap.css')}}">
  <link rel="stylesheet" href="{{ url('assets/vendor/bootstrap-icons/bootstrap-icons.css') }}">
  <link rel="stylesheet" href="{{ url('assets/vendor/boxicons/css/boxicons.min.css') }}">
  <link rel="stylesheet" href="{{ url('assets/lib/sweetalert2/sweetalert2.min.css') }}">

  <style>
    /* Global Alert Banner for Throttle Messages */
    .global-alert {
      position: fixed;
      top: 20px;
      left: 50%;
      transform: translateX(-50%);
      background-color: #dc3545;
      color: white;
      padding: 15px 30px;
      border-radius: 5px;
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
      z-index: 9999;
      font-size: 16px;
      font-weight: 500;
      min-width: 300px;
      text-align: center;
      opacity: 1;
      transition: opacity 0.6s ease;
    }
  </style>

  @yield('styles')

  <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
</head>

<body>
  @if(session('alert_message'))
    <div id="global-alert" class="global-alert">
      {{ session('alert_message') }}
    </div>

    <script>
      setTimeout(() => {
        document.getElementById('global-alert').style.opacity = "0";
        setTimeout(() => document.getElementById('global-alert').remove(), 600);
      }, 10000); // 10 seconds display time
    </script>
  @endif


  @yield('main-content')
  {{--
  <script src="{{url('assets/js/main.js')}}"></script> --}}
  <script src="{{url('assets/js/jquery-3.5.1.js')}}"></script>
  <script src="{{url('assets/vendor/php-email-form/validate.js')}}"></script>
  <script src="{{ url('assets/lib/sweetalert2/sweetalert2.min.js') }}"></script>
  <script src="{{ url('assets/lib/select2/select2.min.js') }}"></script>
  <!-- Bootstrap JS (bundle with Popper) -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"
    crossorigin="anonymous"></script>
  <script>
    // --- Hide alert message after 2 seconds --- //
    $("div.alert").fadeTo(2000, 500).slideUp(500, function () {
      $("div.alert").slideUp(500);
    });
    // --- Hide alert message after 2 seconds --- //

    // Global AJAX Error Handler
    $(document).ajaxError(function (event, jqxhr, settings, thrownError) {
      // Handle 429 Throttle Errors (Rate Limiting)
      if (jqxhr.status === 429 && jqxhr.responseJSON && jqxhr.responseJSON.alert_message) {
        showGlobalAlert(jqxhr.responseJSON.alert_message);
        return;
      }

      // Handle 422 Validation Errors (File Upload Security, Form Validation, etc.)
      if (jqxhr.status === 422) {
        let errorMessage = 'कृपया सभी आवश्यक जानकारी भरें।';
        
        if (jqxhr.responseJSON) {
          // Check for direct message property
          if (jqxhr.responseJSON.message) {
            errorMessage = jqxhr.responseJSON.message;
          }
          // Check for Laravel validation errors
          else if (jqxhr.responseJSON.errors && typeof jqxhr.responseJSON.errors === 'object') {
            // Get first validation error (with null check)
            const errors = jqxhr.responseJSON.errors;
            const errorValues = Object.values(errors);
            if (errorValues && errorValues.length > 0) {
              const firstError = errorValues[0];
              errorMessage = Array.isArray(firstError) ? firstError[0] : firstError;
            }
          }
          // Check for plain error property
          else if (jqxhr.responseJSON.error) {
            errorMessage = jqxhr.responseJSON.error;
          }
        }

        // Show SweetAlert with error message
        if (typeof Swal !== 'undefined') {
          Swal.fire({
            icon: 'warning',
            title: 'ध्यान दें',
            text: errorMessage,
            confirmButtonText: 'ठीक है',
            confirmButtonColor: '#ffc107',
            allowOutsideClick: false,
            allowEscapeKey: false
          });
        } else {
          alert(errorMessage);
        }
        return;
      }

      // Note: 500 errors are handled by custom error page (resources/views/errors/500.blade.php)
    });

    // Global Alert Function (For Custom Alerts)
    function showGlobalAlert(message) {
      let alertDiv = document.createElement('div');
      alertDiv.className = 'global-alert';
      alertDiv.innerText = message;
      document.body.appendChild(alertDiv);

      setTimeout(() => {
        alertDiv.style.opacity = '0';
        setTimeout(() => alertDiv.remove(), 600);
      }, 10000); // 10 seconds display time
    }
  </script>
  @yield('scripts')
</body>

</html>
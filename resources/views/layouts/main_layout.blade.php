<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8" />
  <title>Women And Child Development Department</title>
  <meta content="width=device-width, initial-scale=1.0" name="viewport" />
  <meta content="Free HTML Templates" name="keywords" />
  <meta content="Free HTML Templates" name="description" />

  <!-- Favicons -->
  <link href="{{ asset('assets/img/cg-logo.png') }}" rel="icon">
  <link href="{{ asset('assets/img/cg-logo.png') }}" rel="apple-touch-icon">


  <!-- Google Web Fonts -->
  <link rel="preconnect" href="https://fonts.gstatic.com" />
  <link href="https://fonts.googleapis.com/css2?family=Handlee&family=Nunito&display=swap" rel="stylesheet" />

  <!-- Font Awesome -->
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css" rel="stylesheet" />

  <!-- Flaticon Font -->
  <link href="{{url('landing-page-assets/lib/flaticon/font/flaticon.css')}}" rel="stylesheet" />

  <!-- Libraries Stylesheet -->
  <link href="{{url('landing-page-assets/lib/owlcarousel/assets/owl.carousel.min.css')}}" rel="stylesheet" />
  <link href="{{url('landing-page-assets/lib/lightbox/css/lightbox.min.css')}}" rel="stylesheet" />

  <!-- Customized Bootstrap Stylesheet -->
  <link href="{{url('landing-page-assets/css/style.css')}}" rel="stylesheet" />
  
  <!-- SweetAlert2 CSS & JS -->
  <link rel="stylesheet" href="{{ url('assets/lib/sweetalert2/sweetalert2.min.css') }}">
  <script src="{{ url('assets/lib/sweetalert2/sweetalert2.min.js') }}"></script>
  
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>

<body class="d-flex flex-column min-vh-100">

  @yield('body-page')

  <!-- JavaScript Libraries -->
  <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.bundle.min.js"></script>
  <script src="{{url('landing-page-assets/lib/easing/easing.min.js')}}"></script>
  <script src="{{url('landing-page-assets/lib/owlcarousel/owl.carousel.min.js')}}"></script>
  <script src="{{url('landing-page-assets/lib/isotope/isotope.pkgd.min.js')}}"></script>
  <script src="{{url('landing-page-assets/lib/lightbox/js/lightbox.min.js')}}"></script>

  <!-- Contact Javascript File -->
  <!-- <script src="mail/jqBootstrapValidation.min.js"></script> -->
  <!-- <script src="mail/contact.js"></script> -->

  <!-- Template Javascript -->
  <script src="{{url('assets/js/main.js')}}"></script>
  @yield('scripts')
  <script>
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
      }, 4000);
    }
  </script>

</body>

</html>
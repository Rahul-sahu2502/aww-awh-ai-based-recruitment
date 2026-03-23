<!DOCTYPE html>
<html lang=en>

<head>
    <meta charset=UTF-8>
    <meta name=viewport content="width=device-width, initial-scale=1.0">
    <title>Anganwadi Workers and Supervisors Recruitment</title>
    <link rel=stylesheet href="{{ asset('assets/css/landingPage_style.css') }}">
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

</head>

<body>
    <div class="container">
        @include('partials.header')

        <section class="hindi-section head2">
            <div class=row>
                <div class="col" style="display: flex; align-items: center; justify-content: space-between;">
                    <div>
                        <h3 class="hindi-heading">संपर्क</h3>
                        {{-- <p class="hindi-subtext">अंतिम अपडेट : 29-04-2025</p> --}}
                    </div>
                </div>
                <div style=clear:both></div>
            </div>
        </section>
        <section class="mainContent full-width clearfix conactSection">
            <div class="container">

                <div class="col" style="width:50%;float:left;padding:15px">
                    <div class="col-lg-6 col-sm-6 col-xs-12">
                        <div class="media addressContent">
                            <span class="media-left bg-color-1" href="#">
                                <!-- <i class="fa fa-map-marker" aria-hidden="true"></i> -->
                            </span>
                            <div class="media-body">
                                <h3 class="steps-heading media-heading" style="font-size: 22px">
                                    <i class="bi bi-building"></i>&nbsp;कार्यालय :
                                </h3>
                                <p class="steps-subtext">महिला एवं बाल विकास विभाग, द्वितीय तल, ब्लॉक-बी, इन्द्रावती
                                    भवन, <br>नवा रायपुर अटल
                                    नगर, रायपुर, छत्तीसगढ़</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6 col-sm-3 col-xs-12">
                        <div class="media addressContent">
                            <span class="media-left bg-color-2" href="#">
                                <!-- <i class="fa fa-phone" aria-hidden="true"></i> -->
                            </span>
                            <div class="media-body">
                                <h3 class="steps-heading media-heading" style="font-size: 22px">
                                    <i class="bi bi-telephone-fill"></i>&nbsp;फोन :
                                </h3>
                                <a href="tel:0771-2234192" style="text-decoration: none">
                                    <p class="steps-subtext">0771-2234192</p>
                                </a>
                                <br>
                                <h3 class="steps-heading media-heading" style="font-size: 22px">
                                    <i class="bi bi-envelope-fill"></i>&nbsp;ईमेल :
                                </h3>
                                <a href="mailto:dirwcd.cg@gov.in" style="text-decoration: none">
                                    <p class="steps-subtext"> dirwcd.cg@gov.in </p>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col" style="width:50%;float:right;padding:15px">
                    <iframe
                        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3720.9027287441468!2d81.79468511445671!3d21.156268988790035!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3a28c695f3d7d5d1%3A0xe23796cb5bbee93f!2sIndravati+Bhawan!5e0!3m2!1sen!2sin!4v1547531166208"
                        width="100%" height="400px" frameborder="0" style="border:0" allowfullscreen></iframe>
                </div>
            </div>
        </section>
        <div style=clear:both></div>
        @include('partials.footer')
    </div>
    <script src="{{ asset('assets/js/script.js') }}"></script>
    <script src="{{ asset('assets/js/fontSizeController.js') }}"></script>
    <script src="{{ asset('assets/js/screenReader.js') }}"></script>
    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
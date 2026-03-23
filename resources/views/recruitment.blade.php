<!DOCTYPE html>
<html lang="en">
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
<meta name="format-detection" content="telephone=no" />
<meta name="description" content="">
<meta name="author" content="">
<link rel="apple-touch-icon" href="assets/images/favicon/apple-touch-icon.png">
<link rel="icon" href="assets/images/favicon/favicon.png">
<title>Department of Women and Child Development | Home</title>
<!-- Custom styles for this template -->
<link href="{{ asset('home_page/assets/css/base.css') }}" rel="stylesheet" media="all">
<link href="{{ asset('home_page/assets/css/base-responsive.css') }}" rel="stylesheet" media="all">
<link href="{{ asset('home_page/assets/css/grid.css') }}" rel="stylesheet" media="all">
<link href="{{ asset('home_page/assets/css/font.css') }}" rel="stylesheet" media="all">
<link href="{{ asset('home_page/assets/css/font-awesome.min.css') }}" rel="stylesheet" media="all">
<link href="{{ asset('home_page/assets/css/flexslider.css') }}" rel="stylesheet" media="all">
<link href="{{ asset('home_page/assets/css/megamenu.css') }}" rel="stylesheet" media="all" />
<link href="{{ asset('home_page/assets/css/print.css') }}" rel="stylesheet" media="print" />
<!-- Theme styles for this template -->
<link href="{{ asset('home_page/assets/css/megamenu.css') }}" rel="stylesheet" media="all" />
<link href="{{ asset('home_page/theme/css/site.css') }}" rel="stylesheet" media="all">
<link href="{{ asset('home_page/theme/css/site-responsive.css') }}" rel="stylesheet" media="all">
<link href="{{ asset('home_page/theme/css/ma5gallery.css') }}" rel="stylesheet" type="text/css">
<link href="{{ asset('home_page/theme/css/print.css') }}" rel="stylesheet" type="text/css" media="print">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/fancyapps/fancybox@3.5.6/dist/jquery.fancybox.min.css" />
<!-- HTML5 shiv and Respond.js IE8 support of HTML5 elements and media queries -->
<!--[if lt IE 9]>
   <script src="assets/js/html5shiv.js"></script>
   <script src="assets/js/respond.min.js"></script>
   <![endif]-->
<!-- Custom JS for this template -->
<!-- Custom Css for this template -->
<style>
    a {
        text-decoration: none;
    }
</style>
<!-- Bootstrap 5 CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<noscript>
    <link href="theme/css/no-js.css" type="text/css" rel="stylesheet">
</noscript>
</head>

<body>
    <div id="fb-root"></div>
    @include('new_header')
    <!--/.nav-wrapper-->
    <section class="wrapper banner-wrapper">
        <div id="flexSlider" class="flexslider">
            <ul class="slides">
                {{-- <li> <img src="{{ asset('home_page/theme/images/banner/slider-1.png') }}" alt="slide 1"></li> --}}
                <!-- <li> <img src="{{ asset('home_page/theme/images/banner/slider-13.jpg') }}" alt="slide 1"></li> -->
                <!-- <li> <img src="{{ asset('home_page/theme/images/banner/slider-1.png') }}" alt="slide 1"></li>
               <li> <img src="{{ asset('home_page/theme/images/banner/slider-2.jpg') }}" alt="slide 1"></li>  -->
            </ul>
        </div>
    </section>
    <div class="wrapper" id="skipCont"></div>
    <!--/#skipCont-->




    <section id="Recruitment" class="wrapper body-wrapper ">

        <div class="bg-wrapper top-bg-wrapper gray-bg padding-top-bott">
            <div class="container common-container four_content body-container top-body-container padding-top-bott2">

                <div class="row">

                    <style>
                        table {
                            font-family: arial, sans-serif;
                            border-collapse: collapse;
                            width: 100%;
                            margin: 40px;

                        }

                        thead tr td {
                            background-color: #D0F4FE;
                        }

                        td,
                        th {
                            border: 1px solid #dddddd;
                            text-align: left;
                            padding: 10px;
                        }

                        /* tr:nth-child(even) {
                            background-color: #dddddd;
                        } */
                        .bharti-head {
                            margin: 20px 0 0 20px;
                        }
                    </style>
                    <h2 class="bharti-head">भर्ती आवेदन</h2>
            
                    <table>
                        <thead>
                            <tr>
                                <th width="5%" scope="col">क्रम</th>
                                <th width="20%" scope="col">विज्ञापन</th>
                                <th width="20%" scope="col">शीर्षक</th>
                                <th width="30%" scope="col">विवरण</th>
                                <th width="15%" scope="col">तारीख से</th>
                                <th width="15%" scope="col">तारीख तक</th>
                                <th width="20%" scope="col">फ़ाइल</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($data as $item)
                                <tr>
                                    <td data-th="क्रम">
                                        <span class="bt-content">{{ $loop->iteration }}</span>
                                    </td>
                                    <td data-th="विज्ञापन">
                                        <span class="bt-content">{{ $item->Advertisement_Title }}</span>
                                    </td>
                                    <td data-th="शीर्षक">
                                        <span class="bt-content">{{ $item->Post_Title }}</span>
                                    </td>
                                    <td data-th="विवरण">
                                        <span class="bt-content">
                                            <p>{{ $item->Post_Title }}</p>
                                        </span>
                                    </td>
                                    <td data-th="तारीख से">
                                        <span
                                            class="bt-content">{{ \Carbon\Carbon::parse($item->Start_date)->format('d/m/Y') }}</span>
                                    </td>
                                    <td data-th="तारीख तक">
                                        <span
                                            class="bt-content">{{ \Carbon\Carbon::parse($item->End_date)->format('d/m/Y') }}</span>
                                    </td>
                                    <td data-th="फ़ाइल">
                                        <span class="bt-content">
                                            @if ($item->File_Path)
                                                @php
                                                    $post_file_path = asset('uploads/' . $item->File_Path);
                                                    if (config('app.env') === 'production') {
                                                        $post_file_path =
                                                            config('custom.file_point') . $item->File_Path;
                                                    }
                                                @endphp

                                                <span class="pdf-downloads">
                                                    <a href="#" target="_blank"
                                                        data-file="{{ $post_file_path }}" class="existingFile"
                                                        style="cursor: pointer;" title="देखने के लिए यहां क्लिक करें"
                                                        rel="noopener noreferrer">
                                                        देखें <span class="icon-pdf pdf-icon"></span>
                                                    </a>
                                                </span>
                                            @else
                                                -
                                            @endif
                                        </span>
                                    </td>

                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                </div>




            </div>
        </div>
    </section>
    <!-- Bootstrap Modal -->
    <div class="modal fade" id="docModal" tabindex="-1" aria-labelledby="modalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalLabel">Selected File</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                    <img id="imagePreview" class="img-fluid d-none" alt="Selected File">
                    <iframe id="docViewer" class="w-100 d-none" style="height: 500px; border: none;"></iframe>
                </div>
            </div>
        </div>
    </div>
    <footer class="wrapper footer-wrapper" style="position: absolute;  bottom:0;  width:100%;">
        <div class="footer-top-wrapper">
            <div class="container common-container four_content footer-top-container">
                <ul>
                    <li><a href="#">Website Policies</a></li>
                    <li><a href="#">Help</a></li>
                    <li><a href="#">Contact Us</a></li>
                    <li><a href="#">Terms and Conditions </a></li>
                    <li><a href="#">Feedback</a></li>
                    <li><a href="#">Web Information Manager</a></li>
                    <li><a href="#">Visitor Analytics</a></li>
                    <li><a href="#">FAQ</a></li>
                    <li><a href="#">Disclaimer</a></li>
                </ul>
            </div>
        </div>
        <div class="footer-bottom-wrapper">
            <div class="container common-container four_content footer-bottom-container">
                <div class="footer-content clearfix">
                    <div class="copyright-content"> Website Content Managed by <strong>Department of Women & Child
                            Development, Government of Chhattisgarh</strong> </div>
                </div>
            </div>
        </div>
    </footer>
    <!--/.footer-wrapper-->
    <!-- jQuery v1.11.1 -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/1.11.1/jquery.min.js"
        integrity="sha512-nhY06wKras39lb9lRO76J4397CH1XpRSLfLJSftTeo3+q2vP7PaebILH9TqH+GRpnOhfAGjuYMVmVTOZJ+682w=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <!-- jQuery Migration v1.4.1 -->
    <script src="https://code.jquery.com/jquery-migrate-1.4.1.min.js"></script>
    <!-- jQuery v3.6.0 -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"
        integrity="sha512-894YE6QWD5I59HgZOGReFYm4dnWc1Qt5NtvYSaNcOP+u1T9qYdvdihz0PPSiiqn/+/3e7Jo4EaG7TubfWGUrMQ=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <!-- jQuery Migration v3.4.0 -->
    <script src="https://code.jquery.com/jquery-migrate-3.4.0.min.js"></script>
    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>


    <script src="{{ asset('home_page/assets/js/jquery-accessibleMegaMenu.js') }}"></script>
    <script src="{{ asset('home_page/assets/js/framework.js') }}"></script>
    <script src="{{ asset('home_page/assets/js/jquery.flexslider.js') }}"></script>
    <script src="{{ asset('home_page/assets/js/font-size.js') }}"></script>
    <script src="{{ asset('home_page/assets/js/swithcer.js') }}"></script>
    <script src="{{ asset('home_page/theme/js/ma5gallery.js') }}"></script>
    <script src="{{ asset('home_page/assets/js/megamenu.js') }}"></script>
    <script src="{{ asset('home_page/theme/js/easyResponsiveTabs.js') }}"></script>
    <script src="{{ asset('home_page/theme/js/custom.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/gh/fancyapps/fancybox@3.5.6/dist/jquery.fancybox.min.js"></script>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            let docViewer = document.getElementById("docViewer");
            let imagePreview = document.getElementById("imagePreview");
            let modalElement = document.getElementById("docModal");


            // Function to Show File in Modal
            function showFileInModal(fileURL, isImage) {
                if (isImage) {
                    imagePreview.src = fileURL;
                    imagePreview.classList.remove("d-none");
                    docViewer.classList.add("d-none");
                } else {
                    docViewer.src = fileURL;
                    docViewer.classList.remove("d-none");
                    imagePreview.classList.add("d-none");
                }
            }

            //  Handle click on all elements with class `.existingFile`
            document.querySelectorAll(".existingFile").forEach(function(existingFileLink) {
                existingFileLink.addEventListener("click", function(e) {
                    e.preventDefault();
                    let fileURL = this.getAttribute("data-file");
                    let isImage = fileURL.match(/\.(jpeg|jpg|png|gif)$/i); // Check if it's an image

                    showFileInModal(fileURL, isImage);

                    // Show Bootstrap Modal
                    let modal = new bootstrap.Modal(modalElement);
                    modal.show();
                });
            });

            //  Clear iframe and image on modal close (optional cleanup)
            modalElement.addEventListener('hidden.bs.modal', function() {
                docViewer.src = '';
                imagePreview.src = '';
            });
        });
    </script>

</body>

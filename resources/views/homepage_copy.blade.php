@extends('layouts.main_layout')

@section('styles')

@endsection

@section('main-content')

@include('header')

<body>
    <!-- Header Start -->
    <div class="container-fluid bg-primary px-0 px-md-5 mb-5">
        <div class="row align-items-center px-3">
            <div class="col-lg-6 text-center text-lg-left"><br><br>
                <h1 style="color: white;">
                    महिला एवं बाल विकास विभाग
                    </h4>
                    <p class="text-white mb-4">
                        महिला एवं बाल विकास विभाग ने महिलाओं और बच्चों के संपूर्ण विकास के लिए सामाजिक एवं आर्थिक सहायता प्रदान करने का कार्य किया है।
                        महिला एवं बाल विकास विभाग ने महिलाओं के सशक्तिकरण और बच्चों के अधिकारों की सुरक्षा के लिए कई योजनाएं और कार्यक्रम शुरू किए हैं।
                        महिला एवं बाल विकास विभाग ने समाज में महिलाओं के प्रति सम्मान और उनकी सामाजिक आर्थिक स्थिति में सुधार के लिए कई पहल की है।
                    </p>
                    <a href="{{url('/login')}}" class="btn btn-secondary mt-1 py-3 px-5">ऑनलाइन आवेदन</a><br><br>
            </div>
            <div class="col-lg-4 text-center text-lg-right">
                <img class="img-fluid" style="border-radius: 50%;width:50%;" src="{{url('landing-page-assets/img/pmmvy_new.png')}}" alt="" />
            </div>
            <div class="col-lg-2 text-center text-lg-right">
                <img class="img-fluid" style="border-radius: 50%;width:100%;" src="{{url('landing-page-assets/img/PMMVY_Hindi-02.png')}}" alt="" />

            </div>
        </div>
    </div>
    <!-- Header End -->

    <!-- About Start -->
    <div class="container-fluid py-5">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-5">
                    <img class="img-fluid rounded mb-5 mb-lg-0" src="{{url('landing-page-assets/img/tutor.jpg')}}" alt="" />
                </div>
                <div class="col-lg-7">
                    <p class="section-title pr-5">
                        <span class="pr-2">आवेदन कैसे करें</span>
                    </p>
                    <h1 class="mb-4">आवेदन भरने हेतु दिशा निर्देश</h1>
                    <p>
                        ऑनलाइन आवेदन करने के लिए निम्नलिखित चरणों का पालन करें:
                    </p>
                    <div class="row pt-2 pb-4">
                        <div class="col-10 col-md-12">
                            <ul class="list-inline m-0">
                                <li class="py-2 border-top border-bottom">
                                    <i class="fa fa-check text-primary mr-3"></i>ऑनलाइन आवेदन करने हेतु सर्वप्रथम signup करें |
                                </li>
                                <li class="py-2 border-bottom">
                                    <i class="fa fa-check text-primary mr-3"></i>इसके उपरांत लॉग इन करने के लिए अपना यूजर आई डी एवं पासवर्ड दर्ज करें |
                                </li>
                                <li class="py-2 border-bottom">
                                    <i class="fa fa-check text-primary mr-3"></i>लॉग इन करने के उपरान्त Apply Now पर क्लिक करें |
                                </li>
                                <li class="py-2 border-bottom">
                                    <i class="fa fa-check text-primary mr-3"></i>आपके समक्ष विज्ञापन की सूची आ जावेगी |
                                </li>
                                <li class="py-2 border-bottom">
                                    <i class="fa fa-check text-primary mr-3"></i>आपको जिस विज्ञप्ति के लिए आवेदन करनी है वहा पर दिए गए Proceed बटन पर क्लिक कर आवेदन भरें |
                                </li>
                            </ul>
                        </div>
                    </div>
                    <a href="{{url('/login')}}" class="btn btn-primary mt-2 py-2 px-4">ऑनलाइन आवेदन</a>
                </div>
            </div>
        </div>
    </div>
    <!-- About End -->
</body>
@include('footer')
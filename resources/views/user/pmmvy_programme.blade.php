@extends('layouts.main_layout')

@section('styles')

@endsection

@section('main-content')

@include('header')

<body>
  <!--=== option Switcher ===-->
  <div class="main-wrapper"><br><br><br>
    <section class="mainContent full-width clearfix aboutSection">
      <div class="container">
        <div class="row">
          <div class="col-sm-6 col-sm-pull-6 col-xs-12">
            <div class="schoolInfo">
              <h2 style="color:#0571b2;">PMMVY के उद्देश्य:</h2>
              <p style="text-align: justify;">
                > पहले जीवित बच्चे के जन्म के पूर्व और पश्चात महिला को योग्य आराम लेने के लिए नकद प्रोत्साहन में भागीदारी प्रदान करना।
              </p>

              <p style="text-align: justify;">
                > प्रदान किया गया नकद प्रोत्साहन गर्भवती महिलाओं और स्तनपान कराने वाली माताओं (PW&LM) के बीच सुधारी हेल्थ सीकिंग व्यवहार के लिए ले जाएगा।
              </p>

            </div>
          </div>
          <div class="col-sm-6 col-sm-push-6 col-xs-12">
            <img src="{{url('landing-page-assets/img/what.jpg')}}" alt="image" class="img-responsive img-rounded">
          </div>
        </div>
        <div class="row">
          <div class="col-sm-12 col-xs-12">
            <div class="schoolInfo">
              <p style="color:#0571b2;">लक्षित लाभार्थी</p>
              <ul class="list-unstyled para-list">
                <li style="text-align: justify;"><i class="fa fa-check" aria-hidden="true"></i>
                  सभी गर्भवती महिलाएं और स्तनपान कराने वाली माताएं, जो केंद्र सरकार या राज्य सरकारों या PSU के साथ नियमित रोजगार में हैं या जो किसी भी विधि के तहत समान लाभ प्राप्त कर रही हैं।
                </li>
                <li style="text-align: justify;"><i class="fa fa-check" aria-hidden="true"></i>
                  सभी पात्र गर्भवती महिलाएं और स्तनपान कराने वाली माताएं, जिनका गर्भधारण 01.01.2017 को या उसके बाद हुआ हो, परिवार में पहले बच्चे के लिए।
                </li>
                <li style="text-align: justify;"><i class="fa fa-check" aria-hidden="true"></i>
                  लाभार्थी की गर्भावस्था और चरण की तारीख को उसके MCP कार्ड में उल्लिखित LMP तिथि के संदर्भ में गिना जाएगा।
                </li>
                <li style="text-align: justify;"><i class="fa fa-check" aria-hidden="true"></i>
                  गर्भपात / अजीवन जन्म के मामले: योजना के तहत लाभ प्राप्त करने के लिए लाभार्थी केवल एक बार पात्र हैं।
                </li>
                <li style="text-align: justify;"><i class="fa fa-check" aria-hidden="true"></i>
                  गर्भपात / अजीवन जन्म के मामले में, लाभार्थी को किसी भविष्य के गर्भावस्था के घटना में शेष किस्तों का दावा करने का अधिकार होगा।
                </li>
                <li style="text-align: justify;"><i class="fa fa-check" aria-hidden="true"></i>
                  इस प्रकार, पहली किस्त प्राप्त करने के बाद, यदि लाभार्थी का गर्भपात होता है, तो उसे केवल भविष्य के गर्भावस्था की घटना में 2वीं और 3वीं किस्त प्राप्त करने का अधिकार होगा, यदि पात्रता मानदंड और योजना की शर्तों को पूरा किया जाता है। इसी तरह, यदि पात्र लाभार्थी को पहली और दूसरी किस्त प्राप्त होने के बाद गर्भपात होता है, तो वह केवल तीसरी किस्त प्राप्त करने का अधिकार होगा, यदि पात्रता मानदंड और योजना की शर्तों को पूरा किया जाता है।
                </li>
                <li style="text-align: justify;"><i class="fa fa-check" aria-hidden="true"></i>
                  शिशु मृत्यु के मामले: लाभार्थी केवल एक बार योजना के अंतर्गत लाभ प्राप्त करने के पात्र हैं। अर्थात, शिशु मृत्यु के मामले में, यदि उसने पहले ही PMMVY के अंतर्गत मातृत्व लाभ की सभी किस्तें प्राप्त की हैं, तो उसे योजना के अंतर्गत लाभ का दावा करने का अधिकार नहीं होगा।
                </li>
                <li style="text-align: justify;"><i class="fa fa-check" aria-hidden="true"></i>
                  गर्भवती और स्तनपान कराने वाली AWWs / AWHs / ASHA भी योजना की शर्तों को पूरा करने पर PMMVY के लाभ प्राप्त कर सकती हैं।
                </li>
              </ul>
              <!-- <p style="color:#0571b2;">
                Benefits under PMMVY
              </p> -->
              <!-- <ul class="list-unstyled para-list">
                <li style="text-align: justify;"><i class="fa fa-check" aria-hidden="true"></i>
                  Cash incentives in three instalments i.e. first instalment of ₹ 1000/- on early registration of pregnancy
                  at the Anganwadi Centre (AWC)/ approved Health facility as may be identified by the respective
                  administering State/ UT, second instalment of ₹ 2000/- after six months of pregnancy on receiving at
                  least one ante-natal check-up (ANC) and third instalment of ₹ 2000/- after child birth is registered and
                  the child has received the first cycle of BCG, OPV, DPT and Hepatitis-B, or its equivalent/ substitute.
                </li>
                <li style="text-align: justify;"><i class="fa fa-check" aria-hidden="true"></i>
                  The eligible beneficiaries would receive the incentive given under the Janani Suraksha Yojana (JSY) for
                  Institutional delivery and the incentive received under JSY would be accounted towards maternity
                  benefits so that on an average a woman gets ₹ 6000/-.
                </li>

              </ul> -->
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-sm-12 col-xs-12">
            <div class="container">
              <div class="row">
                <div class="col-sm-6 col-xs-12">
                  <img src="{{url('landing-page-assets/img/condn.PNG')}}" style="width: 100%;" alt="image" class="img-responsive img-rounded">
                </div>
                <div class="col-sm-6 col-xs-12">
                  <div class="schoolInfo">
                    <p style="color:#0571b2;">शर्तें और किस्तें</p>
                    <p style="text-align: justify;">PW&LM को निम्नलिखित स्तरों पर ₹ 5000/- का नगद लाभ मिलेगा, जैसा कि निम्नलिखित सारणी में निर्दिष्ट किया गया है।</p>
                    <p style="text-align: justify;">
                      पात्र लाभार्थियों को संस्थागत डिलीवरी के बाद JSY के तहत मातृत्व लाभ के मान्यता प्राप्त नियमों के अनुसार शेष नकद प्रोत्साहन मिलेगा, ताकि औसतन, एक महिला को ₹ 6000 मिलेगा।
                    </p>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
  </div>
</body>
@include('footer')
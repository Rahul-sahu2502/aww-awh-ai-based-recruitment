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
          <div class="col-sm-6 col-sm-push-6 col-xs-12">
            <img src="{{url('landing-page-assets/img//who.png')}}" alt="image" class="img-responsive img-rounded">
          </div>
          <div class="col-sm-6 col-sm-pull-6 col-xs-12">
            <div class="schoolInfo">
              <h2 style="color:#0571b2;">MSK Introduction</h2>
              <p style="text-align: justify;">वित्त मंत्री का बजट भाषण (2017-18) में घोषणा की गई “महिला शक्ति केंद्र” की स्थापना का उद्देश्य “ग्रामीण महिलाओं को कौशल विकास, रोजगार, डिजिटल साक्षरता, स्वास्थ्य और पोषण के अवसर प्रदान करने के लिए एक स्थान पर संघटित समर्थन सेवाएं प्रदान करना है।” इसी तरह, धारा मनमानी सहयोग योजना (PMMSY) के उपयुक्त रूप से, 2017-18 से 2019-20 तक के दौरान कार्यान्वयन के लिए महिला शक्ति केंद्र (MSK) नामक एक नई उप-योजना को मंजूरी दी गई है। यह योजना ग्रामीण महिलाओं के लिए सरकार को अपनी अधिकारों का उपयोग करने के लिए एक माध्यम प्रदान करेगी और उन्हें जागरूकता, प्रशिक्षण और क्षमता निर्माण के माध्यम से सशक्त करने के लिए। छात्र स्वयंसेवक यहां स्वैच्छिक समुदाय सेवा और लैंगिक समानता की भावना को बढ़ावा देंगे। ये छात्र स्वयंसेवक "परिवर्तन के एजेंट" के रूप में काम करेंगे और अपने समुदायों और राष्ट्र पर दीर्घकालिक प्रभाव डालेंगे।</p>
            </div>
          </div>
        </div>
      </div>
    </section>
</body>
@include('footer')
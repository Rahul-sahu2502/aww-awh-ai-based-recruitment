<!DOCTYPE html>
<html lang=en>

<head>
    <meta charset=UTF-8>
    <meta name=viewport content="width=device-width, initial-scale=1.0">
    <title>Anganwadi Workers and Helpers Recruitment</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <!-- Bootstrap Icons for Screen Reader -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel=stylesheet href="{{ asset('assets/css/landingPage_style.css') }}">

    <style>
        /* Officials Section Styling with Rectangle Images and Wave Border */
        .officials-section {
            display: flex;
            gap: 20px;
            margin-top: 20px;
        }

        .official-card {
            flex: 1;
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
        }

        .official-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.12);
        }

        .image-container {
            position: relative;
            width: 100%;
            height: auto;
            overflow: hidden;
            background: #f5f5f5;
        }

        .image-container img {
            width: 100%;
            height: auto;
            object-fit: contain;
            display: block;
        }

        /* Wave Border between Image and Text */
        .image-container::after {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 0;
            width: 100%;
            height: 30px;
            background: white;
            clip-path: path('M0,10 Q25,0 50,10 T100,10 T150,10 T200,10 T250,10 T300,10 T350,10 T400,10 L400,30 L0,30 Z');
        }

        /* Alternative wave using SVG pattern for better responsiveness */
        .wave-separator {
            position: relative;
            width: 100%;
            height: 30px;
            margin-top: -1px;
        }

        .wave-separator svg {
            display: block;
            width: 100%;
            height: 100%;
        }

        .official-info {
            padding: 10px 5px;
            text-align: center;
            background: white;
        }

        .official-name {
            font-size: 1.4rem;
            font-weight: 600;
            color: rgb(26, 31, 54);
            margin-bottom: 8px;
        }

        .official-title {
            font-size: 1rem;
            color: #666;
            margin-bottom: 5px;
        }

        .official-department {
            font-size: 0.95rem;
            color: #888;
        }

        .official-department .highlight {
            color: #1976d2;
            font-weight: 500;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .officials-section {
                flex-direction: column;
            }

            .image-container {
                height: auto;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        @include('partials.header')
        <section class=hero>
            <div class=hero-content>
                <h2>समर्पण से सशक्तिकरण की ओर </h2>
                <h3>– अब भर्ती प्रक्रिया डिजिटल</h3>
                <p>महिला एवं बाल विकास विभाग की सभी भर्तियों के लिए एकीकृत डिजिटल पोर्टल।
                    चाहे कार्यकर्ता हों, सहायक हों या अन्य पद, अब आवेदन करें सरल, पारदर्शी और सुरक्षित तरीके से
                    —
                    एक ही मंच पर।</p>
                <a href={{ url('/bharti') }} class=primary-btn>भर्ती देखें और आवेदन करें</a>
            </div>
            <div class=hero-image>
                <img src="{{ asset('assets/img/landingpage_img/banner-img.png') }}" alt="Anganwadi Workers"
                    class=anganwadi-building>
                <div class=apply-now></div>
            </div>
        </section>
        <section class=hindi-section>
            <div class=row>
                <div class=col style=width:50%;float:left>
                    <h3 class=hindi-heading>परिचय</h3>
                    <p class=hindi-subtext>महिला एवं बाल विकास विभाग द्वारा राज्यभर में महिलाओं, बच्चों एवं परिवारों के
                        समग्र विकास और सशक्तिकरण हेतु
                        आंगनबाड़ी सेवाओं से संबंधित विभिन्न परियोजनाओं, योजनाओं एवं कार्यक्रमों का संचालन किया जाता है।
                        इन योजनाओं के प्रभावी क्रियान्वयन के लिए विभाग द्वारा समय-समय पर आंगनबाड़ी कार्यकर्ता, सहायिका
                        एवं अन्य
                        पदों पर भर्तियाँ आयोजित की जाती हैं।
                    <p class=hindi-subtext>
                        इस समर्पित पोर्टल के माध्यम से अब राज्य में निकलने वाली सभी आंगनबाड़ी भर्तियों से संबंधित
                        अधिसूचनाएँ, पात्रता मानदंड, आवेदन प्रक्रिया, आवश्यक दस्तावेज तथा चयन प्रक्रिया की संपूर्ण
                        जानकारी
                        एक ही स्थान पर पारदर्शी एवं सुव्यवस्थित रूप में उपलब्ध कराई जा रही है।
                    <p class=hindi-subtext>
                        आंगनबाड़ी सेवाओं के माध्यम से मातृ एवं शिशु स्वास्थ्य, पोषण, प्रारंभिक शिक्षा तथा महिला
                        सशक्तिकरण
                        जैसे महत्वपूर्ण क्षेत्रों में राज्य स्तर पर प्रभावी कार्य किया जाता है।
                        इन सेवाओं का उद्देश्य समाज के कमजोर वर्गों तक सरकारी योजनाओं का लाभ सुनिश्चित करना तथा
                        सामाजिक एवं मानवीय विकास को सुदृढ़ बनाना है।
                    <p class=hindi-subtext>
                        यह पहल वर्तमान आवश्यकताओं के साथ-साथ भविष्य की पीढ़ियों के लिए
                        एक स्वस्थ, सशक्त और समावेशी समाज के निर्माण में सहायक है।</p>
                    {{-- <p class=hindi-subtext>महिला एवं बाल विकास विभाग द्वारा राज्यभर में महिलाओं, बच्चों और परिवारों
                            के
                            सशक्तिकरण हेतु विभिन्न परियोजनाओं और योजनाओं के संचालन के लिए समय-समय पर भर्तियाँ आयोजित की
                            जाती
                            हैं।
                            इस पोर्टल के माध्यम से, अब सभी रिक्तियों की जानकारी, आवेदन प्रक्रिया और चयन प्रक्रिया एक ही
                            स्थान पर उपलब्ध हैं।
                            आपका योगदान आने वाली पीढ़ियों के लिए सकारात्मक बदलाव लाएगा।</p> --}}
                </div>
                <div class=col style=width:45%;float:right>
                    <div class=officials-section>
                        <div class=official-card>
                            <div class=image-container>
                                <img src="{{ asset('assets/img/landingpage_img/cm.jpg') }}" alt="श्री विष्णु देव साय">
                            </div>

                            <div class=official-info>
                                <h2 class=official-name>श्री विष्णु देव साय</h2>
                                <p class=official-title>माननीय मुख्यमंत्री</p>
                                <p class=official-department><span class=highlight>छत्तीसगढ़ शासन</span></p>
                            </div>
                        </div>
                        <div class=official-card>
                            <div class=image-container>
                                <img src="{{ asset('assets/img/landingpage_img/hm.jpg') }}"
                                    alt="श्रीमती लक्ष्मी राजवाड़े">
                            </div>

                            <div class=official-info>
                                <h2 class=official-name>श्रीमती लक्ष्मी राजवाड़े</h2>
                                <p class=official-title>माननीय मंत्री</p>
                                <p class=official-department><span class=highlight>महिला एवं बाल विकास विभाग</span>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                <div style=clear:both></div>
            </div>
        </section>
        <div class="feature-section">
            <div class=col style=width:65%;float:left>
                <h2 class=steps-heading>भर्ती प्रक्रिया के चरण</h2>
                <p class=steps-subtext>पोर्टल की प्रमुख विशेषताएँ एवं सामान्य चरण</p>
                <section class=icon-grid>
                    <div class=icon-box>
                        <div class=icon><img src="{{ asset('assets/img/landingpage_img/ic-1.png') }}" alt></div>
                        <div class=icon-title>अधिसूचना एवं पदों की जानकारी एवं पात्रता शर्तें पढ़ें</div>
                    </div>
                    <div class=icon-box>
                        <div class=icon><img src="{{ asset('assets/img/landingpage_img/ic-2.png') }}" alt></div>
                        <div class=icon-title>पोर्टल पर पंजीयन एवं वांछित विवरण भरें</div>
                    </div>
                    <div class=icon-box>
                        <div class=icon><img src="{{ asset('assets/img/landingpage_img/ic-3.png') }}" alt></div>
                        <div class=icon-title>आवेदन पत्र भरें और दस्तावेज़ अपलोड करें</div>
                    </div>
                </section>
            </div>
            <div class=col style=width:30%;float:right>
                <div class=notice-board>
                    <div class=notice-header>
                        सूचना पट्ट
                    </div>
                    <div class=notice-content id=noticeContent>

                        @foreach ($data as $index => $item)
                            @php
                                $startDate = \Carbon\Carbon::parse($item->Start_date)->format('d/m/Y');
                                $endDate = \Carbon\Carbon::parse($item->End_date)->format('d/m/Y');
                            @endphp
                            <div class="notice-item ">
                                <div class=notice-item-title>
                                    <span>»</span> {{ $item->Advertisement_Title }} - {{ $item->Description }}
                                </div>
                                <div class=notice-date>{{ $startDate }}</div>
                                <a href="{{ url('/advertiesments') }}" class=notice-link>अधिक+</a>
                            </div>
                        @endforeach
                        {{-- <div class="notice-item new-notice">
                            <div class=notice-item-title>
                                <span>»</span> पंचवर्षीय संवर्ग की दिनांक 01.04.2024 की स्थिति दर्शाने वाली अनंतिम
                                वरिष्ठता सूची
                            </div>
                            <div class=notice-date>17/04/2025</div>
                            <a href=# class=notice-link>अधिक+</a>
                        </div> --}}


                    </div>
                </div>
            </div>
        </div>
        <div style=clear:both></div>
        <div class=steps-section>
            <div class=col style=width:45%;float:left>
                <h2 class=steps-heading>ऑनलाइन आवेदन कैसे करें</h2>
                <p class=steps-subtext>ऑनलाइन आवेदन भरने संबंधी दिशा निर्देश:</p>
                <div class=steps-visual>
                    <div class=step-item>
                        <div class=step-number>1</div>
                        <div class=step-details>
                            <div class=step-title>पंजीकरण </div>
                            <div class=step-description>सर्वप्रथम पोर्टल पर अपना नाम , ईमेल एवं मोबाइल दर्ज कर
                                पंजीयन
                                करें</div>
                        </div>
                    </div>
                    <div class=step-item>
                        <div class=step-number>2</div>
                        <div class=step-details>
                            <div class=step-title>लॉगिन</div>
                            <div class=step-description>ओटीपी द्वारा सत्यापन होने के पश्चात अपने अपने मोबाइल नंबर से
                                लॉगिन करें</div>
                        </div>
                    </div>
                    <div class=step-item>
                        <div class=step-number>3</div>
                        <div class=step-details>
                            <div class=step-title>पद का चयन </div>
                            <div class=step-description>खुली भर्तियों से उपयुक्त पद का चयन कर योग्यतानुसार आवेदन
                                पत्र
                                में वांछित जानकारी भरें</div>
                        </div>
                    </div>
                    <div class=step-item>
                        <div class=step-number>4</div>
                        <div class=step-details>
                            <div class=step-title> आवेदन की पुष्टि</div>
                            <div class=step-description>आवेदन पत्र भरने के पश्चात आवश्यक दस्तावेज़ अपलोड कर आवेदन की
                                पुष्टि प्राप्त करें और स्थिति ऑनलाइन देखें</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class=col style=width:50%;float:right>
                <div class=faq-section>
                    <h2 class=steps-heading>सामान्य प्रश्नावली</h2>
                    <p class=steps-subtext>भर्ती एवं आवेदन संबंधी पूछे जाने वाले सामान्य प्रश्न एवं उनके उत्तर :</p>
                    <ul class=accordion id=faqAccordion>
                        <li class=accordion-item>
                            <div class=accordion-header>
                                <span>आवेदन करने के लिए पात्रता कैसे जांचें?</span>
                                <div class=accordion-icon></div>
                            </div>
                            <div class=accordion-content>
                                <p>प्रत्येक पद के लिए पात्रता मानदंड (जैसे शैक्षणिक योग्यता, आयु सीमा, अनुभव आदि)
                                    संबंधित भर्ती विज्ञप्ति में विस्तार से दिया गया है। कृपया विज्ञप्ति पढ़ें और
                                    पात्रता
                                    की पुष्टि करें।</p>
                            </div>
                        </li>
                        <li class=accordion-item>
                            <div class=accordion-header>
                                <span>पंजीयन हेतु किन दस्तावेज़ों की आवश्यकता होगी?</span>
                                <div class=accordion-icon></div>
                            </div>
                            <div class=accordion-content>
                                <p>आम तौर पर निम्न दस्तावेज़ों की आवश्यकता होती है:,<br>
                                    शैक्षणिक योग्यता प्रमाण पत्र,<br>
                                    पहचान पत्र (आधार कार्ड/मतदाता पहचान पत्र),<br>
                                    जाति प्रमाण पत्र (यदि लागू हो),<br>
                                    अन्य पद विशेष दस्तावेज़ (अनुभव प्रमाण पत्र आदि)</p>
                            </div>
                        </li>
                        <li class=accordion-item>
                            <div class=accordion-header>
                                <span>क्या एक से अधिक पदों के लिए आवेदन कर सकता/सकती हूँ?</span>
                                <div class=accordion-icon></div>
                            </div>
                            <div class=accordion-content>
                                <p>हाँ, यदि आप पात्रता मानदंड पूरे करते हैं तो एक से अधिक पदों के लिए अलग-अलग आवेदन
                                    कर
                                    सकते हैं। प्रत्येक पद के लिए अलग से आवेदन और शुल्क लागू हो सकता है।</p>
                            </div>
                        </li>
                        <li class=accordion-item>
                            <div class=accordion-header>
                                <span>मैंने आवेदन पत्र में गलती कर दी है, अब क्या करूँ?</span>
                                <div class=accordion-icon></div>
                            </div>
                            <div class=accordion-content>
                                <p>यदि सुधार विंडो (Correction Window) प्रदान की जाती है, तो आप उसमें सुधार कर सकते
                                    हैं।
                                    यदि नहीं, तो विभाग द्वारा तय निर्देशों का पालन करें। सुधार की प्रक्रिया हर भर्ती
                                    में
                                    अलग हो सकती है।</p>
                            </div>
                        </li>
                        <li class=accordion-item>
                            <div class=accordion-header>
                                <span>आवेदन की स्थिति कैसे जांचें?</span>
                                <div class=accordion-icon></div>
                            </div>
                            <div class=accordion-content>
                                <p>अपने खाते में लॉगिन करें।
                                    "मेरा आवेदन" या "Application Status" सेक्शन में जाकर आवेदन की वर्तमान स्थिति
                                    (Submitted, Under Review, Shortlisted आदि) देखें।</p>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        @include('partials.footer')
    </div>
    <script src="{{ asset('assets/js/script.js') }}"></script>
    <script src="{{ asset('assets/js/fontSizeController.js') }}"></script>
    <script src="{{ asset('assets/js/screenReader.js') }}"></script>
</body>

</html>

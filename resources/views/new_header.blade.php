<header>
    <div class="region region-header-top">
        <div id="block-cmf-content-header-region-block" class="block block-cmf-content first last odd">
            <div class="wrapper common-wrapper">
                <div class="container common-container four_content top-header">
                    <div class="common-left clearfix">
                        <ul>
                            <li class="gov-india"><span class="responsive_go_hindi" lang="hi"><a target="_blank"
                                        href="https://cgstate.gov.in/"
                                        title="छत्तीसगढ़ शासन( बाहरी वेबसाइट जो एक नई विंडो में खुलती है)"
                                        role="link">छत्तीसगढ़ शासन</a></span> </li>
                            <li class="ministry"><span class="li_eng responsive_go_eng"><a target="_blank"
                                        href="https://cgstate.gov.in/"
                                        title="Government of Chhattisgarh,External Link that opens in a new window"
                                        role="link">Government of Chhattisgarh</a></span></li>
                        </ul>
                    </div>
                    <div class="common-right clearfix">
                        <ul id="header-nav">
                            <li class="ico-skip cf"><a href="#skipCont" title="">Skip to main content</a>
                            </li>
                            <li class="ico-accessibility cf">
                                <!-- <a href="javascript:void(0);" id="toggleAccessibility" title="Accessibility Dropdown" role="link">
                                        <img class="top" src="{{ asset('home_page/assets/images/ico-accessibility.png') }}" alt="Accessibility Dropdown" />
                                    </a> -->
                                <!-- <ul style="visibility: hidden;">
                                        <li> <a onClick="set_font_size(&#39;increase&#39;)" title="Increase font size" href="javascript:void(0);" role="link">A<sup>+</sup>
                                            </a>
                                        </li>
                                        <li> <a onClick="set_font_size()" title="Reset font size" href="javascript:void(0);" role="link">A<sup>&nbsp;</sup></a> </li>
                                        <li> <a onClick="set_font_size(&#39;decrease&#39;)" title="Decrease font size" href="javascript:void(0);" role="link">A<sup>-</sup></a> </li>
                                        <li> <a href="javascript:void(0);" class="high-contrast dark" title="High Contrast" role="link">A</a> </li>
                                        <li> <a href="javascript:void(0);" class="high-contrast light" title="Normal Contrast" style="display: none;" role="link">A</a>
                                        </li>
                                    </ul> -->
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <p id="scroll" style="display: none;"><span></span></p>
    </div>
    <!--Top-Header Section end-->
    <section class="wrapper header-wrapper">
        <div class="container common-container four_content header-container">
            <h1 class="logo">
                <a href="#" title="Home" rel="home" class="header__logo" id="logo">
                    <img src="{{ url('userassets/img/logo.webp') }}" alt="Chhattisgarh Government">
                    <p>महिला एवं बाल विकास विभाग </p>
                    <span>WCD RECRUITMENT</span>
                </a>
            </h1>
            <div class="header-right clearfix">
                <div class="right-content clearfix">
                    <div class="float-element">
                        <img src="{{ asset('home_page/assets/images/wcd.png') }}" alt="Chhattisgarh Government"
                            style="height: 90px;">
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!--/.header-wrapper-->
    <section class="wrapper megamenu-wraper">
        <div class="container common-container four_content">
            <p class="showhide"><em></em><em></em><em></em></p>
            <nav class="main-menu clearfix" id="main_menu">
                <ul class="nav-menu">
                    <li class="nav-item">
                        <a href="{{ url('/') }}" class="{{ Request::is('/') ? 'home' : '' }}">
                            <span style="display: none;">home</span><i class="fa fa-home"></i></a>
                    </li>

                    <li class="nav-item"><a href="{{ url('/notices') }}"
                            class="{{ Request::is('notices') ? 'home' : '' }}">Notices</a></li>

                    <li class="nav-item"><a href="{{ url('/recruitment') }}"
                            class="{{ Request::is('recruitment') ? 'home' : '' }}">Recruitments</a></li>

                    <li class="nav-item"><a href="{{ url('/user-manual') }}"
                            class="{{ Request::is('user-manual') ? 'home' : '' }}">User Manual</a></li>

                    <li class="nav-item"><a href="{{ url('/contact') }}"
                            class="{{ Request::is('contact') ? 'home' : '' }}">Contact</a></li>

                    <li class="nav-item"><a href="{{ url('login') }}"
                            class="{{ Request::is('login') ? 'home' : '' }}">Login</a></li>
                </ul>
            </nav>
            <nav class="main-menu clearfix" id="overflow_menu">
                <ul class="nav-menu clearfix">
                </ul>
            </nav>
        </div>
        <style type="text/css">
            body~.sub-nav {
                right: 0
            }

            a {
                text-decoration: none;
            }
        </style>
    </section>
</header>
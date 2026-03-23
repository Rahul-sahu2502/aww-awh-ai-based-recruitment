<div class="top-bar">
    <div class="top-bar-inner">
        <a href="mailto:info@wcd.cg.gov.in">📧 dirwcd.cg@gov.in</a>

        <div class="top-bar-right">


            <!-- Screen Reader Accessibility -->
            <div class="screen-reader-controls">
                <button id="toggleScreenReader" class="screen-reader-btn" title="स्क्रीन रीडर चालू/बंद करें"
                    aria-label="Toggle Screen Reader">
                    <i class="bi bi-volume-up-fill"></i>
                </button>
                <select id="voiceSpeed" class="voice-speed-select" title="आवाज की गति" aria-label="Voice Speed">
                    <option value="0.5">धीमी</option>
                    <option value="1" selected>सामान्य</option>
                    <option value="1.5">तेज</option>
                </select>
            </div>

            <!-- Accessibility Font Size Control -->
            <div class="accessibility-controls">
                <select id="fontSizeControl" class="font-size-select" title="फ़ॉन्ट आकार"
                    aria-label="Font Size Control">
                    <option value="small">A-</option>
                    <option value="normal" selected>A</option>
                    <option value="large">A+</option>
                    <option value="xlarge">A++</option>
                </select>
            </div>

            <a href="tel:0771-2234192"> ☎️ 0771-2234192</a>
        </div>
    </div>
</div>
<header>
    <div class=logo>
        <div class=logo-icon><img src="{{ asset('assets/img/landingpage_img/cg-logo.svg') }}"></div>
        <div class=logo-title>
            <h1>ई-भर्ती</h1>
            <p>महिला एवं बाल विकास विभाग</p>
        </div>
    </div>
    <div class=nav-container>
        <button class=hamburger aria-label="Toggle menu">
            <span></span>
            <span></span>
            <span></span>
        </button>
        <nav>
            <ul class=main-nav>
                <li><a href={{ url('/') }}>होम</a></li>
                <li><a href={{ url('/advertiesments') }}>विज्ञापन</a></li>
                <li><a href={{ url('/bharti') }}>भर्ती</a></li>
                <li><a href="{{ url('dava-aapati-suchna') }}">दावा आपत्ति सूचना</a></li>
                <li><a href={{ url('/user-manual') }}>उपयोगकर्ता मैनुअल</a></li>
                <li><a href={{ url('/contact') }}>संपर्क</a></li>
            </ul>
        </nav>


        <a href="{{ url('/login') }}"> <button class=open-positions-btn>लॉगिन</button></a>
    </div>
    <ul class=mobile-nav>
        <li><a href={{ url('/') }}>होम</a></li>
        <li><a href={{ url('/advertiesments') }}>विज्ञापन</a></li>
        <li><a href={{ url('/bharti') }}>भर्ती</a></li>
        <li><a href="{{ url('dava-aapati-suchna') }}">दावा आपत्ति सूचना</a></li>
        <li><a href={{ url('/user-manual') }}>उपयोगकर्ता मैनुअल</a></li>
        <li><a href={{ url('/contact') }}>संपर्क</a></li>
    </ul>
</header>

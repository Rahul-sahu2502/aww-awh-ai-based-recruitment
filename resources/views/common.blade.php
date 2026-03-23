<!DOCTYPE html>
<html lang=en>

<head>
    <meta charset=UTF-8>
    <meta name=viewport content="width=device-width, initial-scale=1.0">
    <title>Anganwadi Workers and Supervisors Recruitment</title>
    <link rel=stylesheet href="{{ asset('assets/css/landingPage_style.css') }}">

</head>

<body>
    <div class=particles-background></div>
    <div class="container intro-section">
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
                        <li><a href={{ url('/contact') }}>संपर्क</a></li>
                    </ul>
                </nav>
                <a href="{{ url('/login') }}"> <button class=open-positions-btn>लॉगिन</button></a>
            </div>
            <ul class=mobile-nav>
                <li><a href={{ url('/') }}>होम</a></li>
                <li><a href={{ url('/advertiesments') }}>विज्ञापन</a></li>
                <li><a href={{ url('/bharti') }}>भर्ती</a></li>
                <li><a href={{ url('/contact') }}>संपर्क</a></li>
            </ul>
        </header>

        <section class="hindi-section head2">
            <div class=row>
                <div class="col" style="display: flex; align-items: center; justify-content: space-between;">
                    <div>
                        <h3 class="hindi-heading">नवीनतम विज्ञापन</h3>
                        <p class="hindi-subtext">अंतिम अपडेट : @php echo date('d-m-Y') @endphp</p>
                    </div>
                </div>
                <div style=clear:both></div>
            </div>
        </section>
        <div class="feature-section mt-10">
            <table>
                <thead>
                    <tr>
                        <th>क्र.</th>
                        <th>शीर्षक</th>
                        <th>विवरण</th>
                        <th>तारीख से</th>
                        <th>तारीख तक</th>
                        <th>फाइल</th>
                        <th>भर्ती पद</th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="row-animate">
                        <td>1</td>
                        <td class="title-cell">छत्तीसगढ़ पुलिस भर्ती विज्ञापन 2025-26<span
                                class="priority-marker"></span></td>
                        <td class="description-cell">छत्तीसगढ़ पुलिस भर्ती विज्ञापन 2025-26</td>
                        <td class="date-cell">29/04/2025</td>
                        <td class="date-cell">09/05/2025</td>
                        <td><a href="#" class="view-btn">देखें</a></td>
                        <td><a href="#" class="view-btn">देखें</a></td>
                    </tr>
                    <tr class="row-animate">
                        <td>2</td>
                        <td class="title-cell">छत्तीसगढ़ शिक्षक भर्ती अधिसूचना 2025</td>
                        <td class="description-cell">छत्तीसगढ़ शिक्षक भर्ती अधिसूचना 2025</td>
                        <td class="date-cell">25/04/2025</td>
                        <td class="date-cell">18/05/2025</td>
                        <td><a href="#" class="view-btn">देखें</a></td>
                        <td><a href="#" class="view-btn">देखें</a></td>
                    </tr>
                    <tr class="row-animate">
                        <td>3</td>
                        <td class="title-cell">छत्तीसगढ़ स्वास्थ्य विभाग भर्ती 2025<span class="priority-marker"></span>
                        </td>
                        <td class="description-cell">छत्तीसगढ़ स्वास्थ्य विभाग भर्ती 2025</td>
                        <td class="date-cell">25/04/2025</td>
                        <td class="date-cell">24/05/2025</td>
                        <td><a href="#" class="view-btn">देखें</a></td>
                        <td><a href="#" class="view-btn">देखें</a></td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div style=clear:both></div>
        <footer class=footer>
            <div class=footer-logo>
                <img src="{{ asset('assets/img/landingpage_img/cg-logo.svg') }}" class=footer-logo-img>
            </div>
            <h2 class=footer-title>महिला एवं बाल विकास विभाग</h2>
            <p>छत्तीसगढ़ शासन</p>
            {{-- <nav class=footer-nav>
                <a href=#>About</a>
                <a href=#>Recruitment</a>
                <a href=#>Guidelines</a>
                <a href=#>Contact</a>
            </nav> --}}
            <div class=footer-copyright>
                <p>© 2025 महिला एवं बाल विकास विभाग, छत्तीसगढ़। इस पोर्टल की सभी सामग्री महिला एवं बाल विकास विभाग की
                    स्वामित्वाधीन है</p>
                <p class=pt-10>Designed and Developed by
                    <a href=https://vectre.in>
                        <img src="{{ asset('assets/img/landingpage_img/vectre.svg') }}" alt=Vectre
                            class=footer-vectre-logo>
                    </a>
                </p>
            </div>
        </footer>
    </div>
    <script src="{{ asset('assets/js/script.js') }}"></script>
</body>

</html>

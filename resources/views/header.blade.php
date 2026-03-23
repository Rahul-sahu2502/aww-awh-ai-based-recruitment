 <!-- Navbar Start -->
 <div class="container-fluid bg-light position-relative shadow">
     <nav class="navbar navbar-expand-lg bg-light navbar-light py-3 py-lg-0 px-0 px-lg-5">
         <a href="" class="navbar-brand font-weight-bold text-secondary" style="font-size: 50px">
             <a href="/"><img src="{{ url('assets/img/dpo.png')}}" alt="logo" style="width: 60px;"></a>
             <img src="{{url('assets/img/logolong.png')}}" alt="" style="width: 470PX;">
         </a>
         <button type="button" class="navbar-toggler" data-toggle="collapse" data-target="#navbarCollapse">
             <span class="navbar-toggler-icon"></span>
         </button>
         <div class="collapse navbar-collapse justify-content-between" id="navbarCollapse">
             <div class="navbar-nav font-weight-bold mx-auto py-0">
                 <a href="{{url('/')}}" class="nav-item nav-link active">Home</a>
                 <div class="nav-item dropdown">
                     <a href="#" class="nav-link dropdown-toggle" data-toggle="dropdown">PMMVY</a>
                     <div class="dropdown-menu rounded-0 m-0">
                         <a href="{{url('/pmmvy-intro')}}" class="dropdown-item">PMMVY Introduction</a>
                         <a href="{{url('/PMMVY-Closure-of-Old-Benefit')}}" class="dropdown-item">Closure of old Benefit</a>
                         <!-- <a href="single.html" class="dropdown-item">PMMVY Recruitment</a> -->
                         <a href="{{url('/pmmvy-programme')}}" class="dropdown-item">PMMVY Programme</a>

                     </div>
                 </div>
                 <div class="nav-item dropdown">
                     <a href="#" class="nav-link dropdown-toggle" data-toggle="dropdown">MSK</a>
                     <div class="dropdown-menu rounded-0 m-0">
                         <a href="{{url('/msk-intro')}}" class="dropdown-item">MSK Introduction</a>
                         <!-- <a href="single.html" class="dropdown-item">MSK Guidelines</a> -->
                     </div>
                 </div>
                 <a href="{{url('/contact')}}" class="nav-item nav-link"><i class="fa fa-pencil-square-o bg-color-6"></i> &nbsp;Contact</a>
             </div>
             <a href="{{url('login')}}" class="btn btn-primary px-4"><i class="fa fa-lock bg-color-5" aria-hidden="true"></i>&nbsp;लॉग इन करें</a>
             <!-- &nbsp;&nbsp;<img src="{{url('assets/img/pmmvy.png')}}" alt="" style="width: 12%;"> -->
            </div>
     </nav>
 </div>
 <!-- Navbar End -->
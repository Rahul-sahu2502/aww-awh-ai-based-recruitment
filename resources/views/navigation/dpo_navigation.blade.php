<style>

a{
  text-decoration-line: none;
}
</style>


<!-- ======= Header ======= -->
<header id="header" class="header fixed-top d-flex align-items-center">

  <div class="d-flex align-items-center justify-content-between">
    <a href="index.html" class="logo d-flex align-items-center">
      {{-- <img src="{{url('assets/img/dpo.png')}}" alt="" style="width: 45px;"> --}}
      <img src="{{url('assets/img/logonew.png')}}" alt="" style="width: 220px;">
      {{-- <span class="d-none d-lg-block">PWD</span> --}}
    </a>
    <i class="bi bi-list toggle-sidebar-btn"></i>
  </div><!-- End Logo -->

  <nav class="header-nav ms-auto">
    <ul class="d-flex align-items-center">

      <li class="nav-item dropdown pe-3">
        <a class="nav-link nav-profile d-flex align-items-center pe-0" href="#" data-bs-toggle="dropdown">
          <img src="{{url('assets/img/user_profile.jfif')}}" alt="Profile" class="rounded-circle" style="height: 25px;">
          <span class="d-none d-md-block dropdown-toggle ps-2">{{session()->get('sess_fname')}}</span>
        </a><!-- End Profile Iamge Icon -->

        <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow profile">
          <li>
            <a class="dropdown-item d-flex align-items-center" href="{{ url('/logout') }}">
              <i class="bi bi-box-arrow-right"></i>
              <span>Sign Out</span>
            </a>
          </li>

        </ul><!-- End Profile Dropdown Items -->
      </li><!-- End Profile Nav -->

    </ul>
  </nav><!-- End Icons Navigation -->

</header><!-- End Header -->

<!-- ======= Sidebar ======= -->
<aside id="sidebar" class="sidebar">

  <ul class="sidebar-nav" id="sidebar-nav">

    <li class="nav-item">
      <a class="nav-link " href="{{ url('/admin/admin-dashboard') }}">
        <i class="bi bi-grid"></i>
        <span>Dashboard</span>
      </a>
    </li><!-- End Dashboard Nav -->

    <li class="nav-item">
      <a class="nav-link collapsed" data-bs-target="#forms-nav" data-bs-toggle="collapse" href="#">
        <i class="bi bi-list"></i><span>Application Verification</span><i class="bi bi-chevron-down ms-auto"></i>
      </a>
      <ul id="forms-nav" class="nav-content collapse " data-bs-parent="#sidebar-nav">
        <li>
          <a href="{{url('/admin/application-list')}}">
            <i class="bi bi-circle"></i><span>Aganbaadi Recruitment</span>
          </a>
        </li>
      </ul>
    </li>

    <li class="nav-item">
      <a class="nav-link collapsed" data-bs-target="#forms-nav1" data-bs-toggle="collapse" href="#">
        <i class="bi bi-journal"></i><span>Marks Entry</span><i class="bi bi-chevron-down ms-auto"></i>
      </a>
      <ul id="forms-nav1" class="nav-content collapse " data-bs-parent="#sidebar-nav">
        <li>
          <a href="{{url('/admin/marks-entry')}}">
            <i class="bi bi-circle"></i><span>Marks Entry</span>
          </a>
        </li>
        <li>
          <a href="{{url('/admin/merit-list')}}">
            <i class="bi bi-circle"></i><span>Score Card</span>
          </a>
        </li>
      </ul>
    </li>

    <li class="nav-item">
      <a class="nav-link collapsed" data-bs-target="#forms-nav3" data-bs-toggle="collapse" href="#">
        <i class="bi bi-file-earmark-text"></i><span>Reports</span><i class="bi bi-chevron-down ms-auto"></i>
      </a>
      <ul id="forms-nav3" class="nav-content collapse " data-bs-parent="#sidebar-nav">
        <li>
          <a href="{{url('/admin/verified-list')}}">
            <i class="bi bi-circle"></i><span>Eligible Candidates</span>
          </a>
        </li>
        <li>
          <a href="{{ url('/admin/rejected-list') }}">
            <i class="bi bi-circle"></i><span>Not Eligible Candidates</span>
          </a>
        </li>
        <li>
          <a href="{{url('/admin/application-list')}}">
            <i class="bi bi-circle"></i><span>All Applications</span>
          </a>
        </li>
      </ul>
    </li>
    <!--End Forms Nav -->
  </ul>

</aside><!-- End Sidebar-->
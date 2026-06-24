<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{$company->title}}- @yield('title')</title>
    @include("layouts.style")
</head>

<body>
    <header id="header" class="header fixed-top d-flex align-items-center">

        <div class="d-flex align-items-center justify-content-between">
            <a href="/" class="logo d-flex align-items-center w-100">
                <img src="{{asset($company->logo ? $company->logo : 'noImage.jpg')}}" alt="{{$company->title}}">
                <span class="d-none d-lg-block">{{$company->title}}</span>
            </a>
            <i class="bi bi-list toggle-sidebar-btn"></i>

            <a class="btn btn-warning btn-sm text-white ms-3" href="/pos">Order</a>
        </div>

        <nav class="header-nav ms-auto">
            <ul class="d-flex align-items-center">
                <li class="nav-item dropdown pe-3">
                    <a class="nav-link nav-profile d-flex align-items-center pe-0" href="#" data-bs-toggle="dropdown">
                        <img src="{{asset(Auth::user()->image ? Auth::user()->image : 'nouser.png')}}" alt="Profile" class="rounded-circle">
                        <span class="d-none d-md-block dropdown-toggle ps-2">{{Auth::user()->name}}</span>
                    </a>

                    <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow profile">
                        <li class="dropdown-header">
                            <h6>{{Auth::user()->name}}</h6>
                            <span>{{Auth::user()->username}}</span>
                        </li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>

                        <li>
                            <a class="dropdown-item d-flex align-items-center" href="/user-profile">
                                <i class="bi bi-person"></i>
                                <span>My Profile</span>
                            </a>
                        </li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>

                        <li>
                            <a class="dropdown-item d-flex align-items-center" href="{{url('logout')}}">
                                <i class="bi bi-box-arrow-right"></i>
                                <span>Sign Out</span>
                            </a>
                        </li>

                    </ul><!-- End Profile Dropdown Items -->
                </li>

            </ul>
        </nav>

    </header>

    @include('layouts.sidebar')

    <main id="main" class="main">
        <div class="pagetitle">
            <!-- <h1>Dashboard</h1> -->
            <nav class="d-flex justify-content-between">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/">Home</a></li>
                    <li class="breadcrumb-item">@yield('breadcrumb')</li>
                </ol>
                @if(session('branch')->id == 1 || auth()->user()->id == 1)
                <ol>
                    <select class="form-select form-select-sm ms-3 py-2" onchange="window.location.href= '/branchset/'+this.value">
                        @foreach ($branches as $branch)
                        <option value="{{$branch->id}}" {{session('branch')->id == $branch->id ? 'selected' : ''}}>
                            {{$branch->name}}
                        </option>
                        @endforeach
                    </select>
                </ol>
                @endif
                <ol class="breadcrumb d-none d-md-block" style="margin-top: -16px; display: block; text-align: center; border: 4px solid #c1c1c1; border-bottom-left-radius: 40px;border-bottom-right-radius: 40px; margin-bottom: 0; padding-left: 15px; padding-right: 15px; padding-top: 2px;">
                    <li>
                        Today, {{date("d M Y")}} || {{dateBangla()}}
                    </li>
                    <li id="time" style="font-size: 18px; font-weight: 700; color: #6e6e6e;">
                        {{date('h:m:s')}}
                    </li>
                </ol>
            </nav>
        </div>
        <section class="section dashboard mt-3" style="position: relative;">
            <!-- Preloader -->
            <div class="preloader">
                <div class="spinner"></div>
            </div>
            <div class="contentDisplay d-none">
                @yield('content')
            </div>

        </section>
    </main>

    <!-- <footer id="footer" class="footer">
        <div class="copyright">
            &copy; Copyright <strong><span>NiceAdmin</span></strong>. All Rights Reserved
        </div>
        <div class="credits">
            Designed by <a href="#">BootstrapMade</a>
        </div>
    </footer> -->

    <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

    <!-- Vendor JS Files -->
    <script src="{{asset('backend')}}/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <!-- Template Main JS File -->
    <script src="{{asset('backend')}}/js/jquery.min.js"></script>
    <script src="{{asset('backend')}}/js/main.js"></script>
    <script src="{{asset('backend')}}/js/vue/vue.min.js"></script>
    <script src="{{asset('backend')}}/js/vue/axios.min.js"></script>
    <script src="{{asset('backend')}}/js/vue/moment.js"></script>
    <script src="{{asset('backend')}}/js/vue/lodash.min.js"></script>
    <script src="{{asset('backend')}}/js/vue/vue-good-table.min.js"></script>
    <script src="{{asset('backend')}}/js/vue/vue-select.js"></script>
    <script src="{{asset('backend')}}/js/toastr.min.js"></script>
    <script>
        Vue.component('v-select', VueSelect.VueSelect);
        toastr.options = {
            "closeButton": true,
            "debug": false,
            "progressBar": true,
            "positionClass": "toast-top-right",
            "showDuration": "300",
            "hideDuration": "1000",
            "timeOut": "5000",
            "extendedTimeOut": "1000",
            "showEasing": "swing",
            "hideEasing": "linear",
            "showMethod": "fadeIn",
            "hideMethod": "fadeOut"
        };
        window.addEventListener('load', function() {
            $(".spinner").addClass('d-none');
            $(".contentDisplay").removeClass('d-none');
        });

        function dateTime() {
            d = new Date().toDateString();
            time = new Date().toLocaleTimeString();
            document.getElementById("time").innerText = time
            setTimeout(() => {
                dateTime()
            }, 1000)
        }
        dateTime()
    </script>
    @stack('js')

</body>

</html>
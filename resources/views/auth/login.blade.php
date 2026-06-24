<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>E‑Inventory – Login</title>
    <!-- Bootstrap 5 -->
    <link href="{{'auth'}}/styles.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('auth/style.css') }}">
    <link href="{{asset('backend')}}/css/material_icon.css" rel="stylesheet" />
    <link rel="stylesheet" href="{{asset('backend')}}/css/toastr.min.css">
</head>

<body>
    <div class="container page-wrap py-5">
        <div class="row g-4 align-items-center">
            <div class="col-lg-7 d-none d-md-block">
                <div class="illus-wrap pt-4 text-center">
                    <img src="{{asset('auth')}}/img/login_banner.png"
                        alt="POS Illustration / Device" class="img-fluid rounded-4">
                </div>
            </div>
            <div class="col-lg-5">
                <header class="mb-4 ps-2">
                    <h1 class="brand-title display-6 mb-1">E‑Inventory</h1>
                    <p class="muted mb-0">Online inventory management system</p>
                </header>

                <div class="card login-card p-4 p-md-4">
                    <div class="card-body">
                        <h2 class="h3 fw-bold mb-3">Login</h2>

                        <form onsubmit="userLogin(event)">
                            <div class="mb-3">
                                <label for="username" class="form-label">Email/Username</label>
                                <div class="input-group">
                                    <span class="input-group-text py-1 px-2">
                                        <i class="material-icons">person</i>
                                    </span>
                                    <input type="text" class="form-control shadow-none" name="username" id="username"
                                        placeholder="name@example.com" autocomplete="off" autofocus required>
                                </div>
                               <p class="error-username" style="font-style:italic;font-size:12px;color:red;"></p>
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <div class="input-group">
                                    <span class="input-group-text py-1 px-2">
                                        <i class="material-icons">lock</i>
                                    </span>
                                    <input type="password" name="password" class="form-control shadow-none" id="password"
                                        placeholder="••••••••" autocomplete="off" required>
                                </div>
                                <p class="error-password" style="font-style:italic;font-size:12px;color:red;"></p>
                            </div>

                            <div class="d-flex align-items-center gap-2 mb-2">
                                <a href="" class="text-decoration-none text-primary mb-0">Forget Password?</a>
                            </div>

                            <button type="submit" class="btn btn-lg btn-gradient text-white w-100 d-flex justify-content-center">Log In</button>
                        </form>
                        <p class="text-2 mb-0 mt-4">Design & Develop By <a target="_blank" href="https://bdsofttechnology.com/">BD Soft Technology</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="{{asset('backend')}}/js/bootstrap.bundle.min.js"></script>
    <script src="{{asset('backend')}}/js/jquery.min.js"></script>
    <script src="{{asset('backend')}}/js/toastr.min.js"></script>
    <script>
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

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        function userLogin(event) {
            event.preventDefault();
            $("#login").prop("disabled", true);
            var formdata = new FormData(event.target)
            $.ajax({
                url: "/login",
                method: "POST",
                data: formdata,
                contentType: false,
                processData: false,
                beforeSend: () => {
                    $(".error-username").text('').removeClass("text-danger")
                    $(".error-password").text('').removeClass("text-danger")
                },
                success: res => {
                    location.href = "/panel/dashboard"
                },
                error: err => {
                    $("#login").prop("disabled", false);
                    toastr.error(err.responseJSON.message);
                    if (typeof err.responseJSON.errors == 'object') {
                        $.each(err.responseJSON.errors, (index, value) => {
                            $(".error-" + index).text(value).addClass("text-danger")
                        })
                        return
                    }
                    console.log(err.responseJSON.errors);
                }
            })
        }
        // show password
        function passwordShow(event) {
            let password = $(".password").find('input').prop('type');
            if (password == 'password') {
                $(".password").find('.passwordeye').removeProp('class').prop('class', 'fa fa-eye-slash passwordeye')
                $(".password").find('input').removeProp('type').prop('type', 'text');
            } else {
                $(".password").find('.passwordeye').removeProp('class').prop('class', 'fa fa-eye passwordeye')
                $(".password").find('input').removeProp('type').prop('type', 'password');
            }
        }
    </script>
</body>

</html>
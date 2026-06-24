<!-- Favicons -->
<link href="{{asset($company->favicon ? $company->favicon : 'noImage.jpg')}}" rel="icon">
<link href="{{asset('backend')}}/img/apple-touch-icon.png" rel="apple-touch-icon">

<!-- Google Fonts -->
<link href="https://fonts.gstatic.com" rel="preconnect">
<link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Nunito:300,300i,400,400i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i" rel="stylesheet">

<!-- Vendor CSS Files -->
<link href="{{asset('backend')}}/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
<link href="{{asset('backend')}}/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
<!-- Template Main CSS File -->
<link href="{{asset('backend')}}/css/style.css" rel="stylesheet">
<link href="{{asset('backend')}}/css/custom.css" rel="stylesheet">
<link rel="stylesheet" href="{{asset('backend')}}/css/vue-good-table.css">
<link rel="stylesheet" href="{{asset('backend')}}/css/toastr.min.css">
@stack('style')
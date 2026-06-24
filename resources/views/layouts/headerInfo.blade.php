<link href="{{asset('backend')}}/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
<link href="{{asset('backend')}}/css/custom.css" rel="stylesheet">
<div class="container-fluid mb-2">
    <div class="row ms-0 me-0 py-1 px-1" style="border: 1px solid gray;border-radius: 8px;">
        <div class="col-2 ps-0">
            <img src="{{asset($company->logo ? $company->logo : 'noImage.jpg')}}" class="w-100 h-100" style="box-shadow:1px 1px 1px 1px #d9d9d9;border-radius:5px;">
        </div>
        <div class="col-10 pe-0">
            <h4 class="m-0">{{$company->title}}</h4>
            <address class="m-0"><strong>Mobile: </strong>{{ $company->phone }}</address>
            <address class="m-0">{!! $company->address !!}</address>
        </div>
    </div>
</div>
@extends('master')

@section('title', 'Company Profile')
@section('breadcrumb', 'Update Company Profile')
@push('style')
<link href="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.18/summernote-lite.min.css" rel="stylesheet">
@endpush
@section('content')
<div class="row" id="companyProfile">
    <div class="col-md-4">
        <div class="card mb-1 mb-md-0">
            <div class="card-body" style="padding: 20px;">
                <div class="form-group ImageBackground">
                    <span class="text-danger">(150 X 150)PX</span>
                    <div class="imageContainer">
                        <img :src="logoSrc" class="imageShow" />
                        <button type="button" class="close-btn" @click="removeLogo">X</button>
                    </div>
                    <label for="logo">Upload Logo</label>
                    <input type="file" id="logo" class="form-control shadow-none" @change="logoUrl" />
                </div>

                <div class="form-group ImageBackground">
                    <span class="text-danger">(100 X 100)PX</span>
                    <div class="imageContainer">
                        <img :src="faviconSrc" class="imageShow" />
                        <button type="button" class="close-btn" @click="removeFavicon">X</button>
                    </div>
                    <label for="favicon">Upload Favicon</label>
                    <input type="file" id="favicon" class="form-control shadow-none" @change="faviconUrl" />
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-8">
        <div class="card mb-0">
            <div class="card-body">
                <h5 class="card-title">Company Information</h5>
                <form @submit.prevent="updateCompanyProfile($event)">
                    <div class="row mb-2">
                        <label for="name" class="col-3 col-form-label">Company Name</label>
                        <div class="col-9">
                            <input type="text" name="name" id="name" v-model="company.name" class="form-control" autocomplete="off" />
                        </div>
                    </div>
                    <div class="row mb-2">
                        <label for="title" class="col-3 col-form-label">Company Title</label>
                        <div class="col-9">
                            <input type="text" name="title" id="title" v-model="company.title" class="form-control" autocomplete="off" />
                        </div>
                    </div>
                    <div class="row mb-2">
                        <label for="phone" class="col-3 col-form-label">Mobile</label>
                        <div class="col-9">
                            <input type="text" name="phone" id="phone" v-model="company.phone" class="form-control" autocomplete="off" />
                        </div>
                    </div>
                    <div class="row mb-2">
                        <label for="email" class="col-3 col-form-label">Email</label>
                        <div class="col-9">
                            <input type="email" name="email" id="email" v-model="company.email" class="form-control" autocomplete="off" />
                        </div>
                    </div>
                    <div class="row mb-2">
                        <label for="address" class="col-3 col-form-label">Address</label>
                        <div class="col-9">
                            <textarea rows="4" name="address" id="address" v-html="company.address" class="form-control" autocomplete="off"></textarea>
                        </div>
                    </div>

                    <div class="row mb-2">
                        <div class="col-12 text-end">
                            <button type="submit" class="btn btn-primary">Update Profile</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('js')
<script src="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.18/summernote-lite.min.js"></script>
<script>
    $(document).ready(function() {
        $('#address').summernote({
            height: 150,
        });
    });
    new Vue({
        el: '#companyProfile',
        data: {
            company: @json(company()),
            logoSrc: "/noImage.jpg",
            faviconSrc: "/noImage.jpg",
        },
        created() {
            this.logoSrc = this.company.logo ? this.company.logo : '/noImage.jpg';
            this.faviconSrc = this.company.favicon ? this.company.favicon : '/noImage.jpg';
        },
        methods: {
            updateCompanyProfile(event) {
                let formdata = new FormData(event.target);
                formdata.append('logo', this.company.logo);
                formdata.append('favicon', this.company.favicon);
                formdata.append('address', $('#address').summernote('code'));
                axios.post('/update-companyProfile', formdata)
                    .then(res => {
                        if (res.data.status) {
                            toastr.success(res.data.message);
                        }
                    })
                    .catch(error => {
                        if (error.response.status == 422) {
                            let errMsg = error.response.data.errors;
                            $.each(errMsg, (index, item) => {
                                $.each(item, (ind, val) => {
                                    toastr.error(val);
                                })
                            })

                        }

                    })
            },

            logoUrl(event) {
                const WIDTH = 150;
                const HEIGHT = 150;
                if (event.target.files[0]) {
                    let reader = new FileReader();
                    reader.readAsDataURL(event.target.files[0]);
                    reader.onload = (ev) => {
                        let img = new Image();
                        img.src = ev.target.result;
                        img.onload = async e => {
                            let canvas = document.createElement('canvas');
                            canvas.width = WIDTH;
                            canvas.height = HEIGHT;
                            const context = canvas.getContext("2d");
                            context.drawImage(img, 0, 0, canvas.width, canvas.height);
                            let new_img_url = context.canvas.toDataURL(event.target.files[0].type);
                            this.logoSrc = new_img_url;
                            const resizedImage = await new Promise(rs => canvas.toBlob(rs, 'image/jpeg', 1))
                            this.company.logo = new File([resizedImage], event.target.files[0].name, {
                                type: resizedImage.type
                            });
                        }
                    }
                } else {
                    event.target.value = '';
                }
            },
            faviconUrl(event) {
                const WIDTH = 100;
                const HEIGHT = 100;
                if (event.target.files[0]) {
                    let reader = new FileReader();
                    reader.readAsDataURL(event.target.files[0]);
                    reader.onload = (ev) => {
                        let img = new Image();
                        img.src = ev.target.result;
                        img.onload = async e => {
                            let canvas = document.createElement('canvas');
                            canvas.width = WIDTH;
                            canvas.height = HEIGHT;
                            const context = canvas.getContext("2d");
                            context.drawImage(img, 0, 0, canvas.width, canvas.height);
                            let new_img_url = context.canvas.toDataURL(event.target.files[0].type);
                            this.faviconSrc = new_img_url;
                            const resizedImage = await new Promise(rs => canvas.toBlob(rs, 'image/jpeg', 1))
                            this.company.favicon = new File([resizedImage], event.target.files[0].name, {
                                type: resizedImage.type
                            });
                        }
                    }
                } else {
                    event.target.value = '';
                }
            },

            removeLogo() {
                this.logoSrc = "/noImage.jpg";
                this.company.logo = null;
            },
            removeFavicon() {
                this.faviconSrc = "/noImage.jpg";
                this.company.favicon = null;
            }
        },
    })
</script>
@endpush
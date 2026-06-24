@extends('master')

@section('title', 'User Profile')
@section('breadcrumb', 'User Profile')
@section('content')
<div class="row" id="user">
    <div class="col-12 col-md-8 offset-md-2">
        <div class="card mb-0">
            <div class="card-body pt-5">
                <form @submit.prevent="updateUser($event)">
                    <div class="row">
                        <div class="col-12 col-md-4" style="overflow: hidden;">
                            <div class="text-center p-3" style="box-shadow: rgba(0, 0, 0, 0.24) 0px 3px 8px;">
                                <img :src="imageSrc" alt="Profile" class="rounded-circle mb-2" style="width:120px;height:120px;object-fit:cover;">
                                <div>
                                    <input type="file" style="cursor: pointer;" accept="image/*" @change="imageUrl($event)" />
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-md-8">
                            <div class="mb-1 row" style="border-bottom: 1px solid gray;">
                                <label class="form-label col-4 col-md-3" for="name"><strong>Name:</strong></label>
                                <div class="col-8 col-md-9">
                                    <input type="hidden" name="id" v-model="user.id" />
                                    <input type="hidden" name="name" v-model="user.name" />
                                    <input type="hidden" name="username" v-model="user.username" />
                                    <input type="hidden" name="email" v-model="user.email" />
                                    <input type="hidden" name="role" v-model="user.role" />
                                    <input type="hidden" name="phone" v-model="user.phone" />

                                    <span style="font-size: 14px;" v-text="user.name"></span>
                                </div>
                            </div>
                            <div class="mb-1 row" style="border-bottom: 1px solid gray;">
                                <label class="form-label col-4 col-md-3" for="name"><strong>Username:</strong></label>
                                <div class="col-8 col-md-9">
                                    <span style="font-size: 14px;" v-text="user.username"></span>
                                </div>
                            </div>
                            <div class="mb-1 row" style="border-bottom: 1px solid gray;">
                                <label class="form-label col-4 col-md-3" for="email"><strong>Email:</strong></label>
                                <div class="col-8 col-md-9">
                                    <span style="font-size: 14px;" v-text="user.email"></span>
                                </div>
                            </div>
                            <div class="mb-1 row mt-3">
                                <label class="form-label col-4 col-md-3" for="password">Password:</label>
                                <div class="col-8 col-md-9">
                                    <input type="password" class="form-control" autocomplete="off" id="password" name="password" v-model="user.password" />
                                </div>
                            </div>
                            <div class="mt-1 text-end">
                                <button class="btn btn-primary" type="submit" :disabled="onProgress">
                                    <span>Update Profile</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push("js")
<script>
    new Vue({
        el: "#user",
        data() {
            return {
                user: @json(auth()->user()),
                imageSrc: "{{ auth()->user()->image ? auth()->user()->image : asset('nouser.png') }}",
                onProgress: false,
            }
        },

        methods: {
            updateUser(event) {
                this.onProgress = true;
                const formData = new FormData(event.target);
                formData.append('image', this.user.image);
                axios.post('/update-user', formData)
                    .then(response => {
                        this.onProgress = false;
                        if (response.data.status == true) {
                            toastr.success(response.data.message);
                            this.user.password = '';
                        }
                    })
            },
            imageUrl(event) {
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
                            this.imageSrc = new_img_url;
                            const resizedImage = await new Promise(rs => canvas.toBlob(rs, 'image/jpeg', 1))
                            this.user.image = new File([resizedImage], event.target.files[0].name, {
                                type: resizedImage.type
                            });
                        }
                    }
                } else {
                    event.target.value = '';
                }
            }
        },
    })
</script>
@endpush
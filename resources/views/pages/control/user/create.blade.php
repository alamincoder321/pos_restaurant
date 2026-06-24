@extends('master')

@section('title', 'User Entry')
@section('breadcrumb', 'User Entry')
@section('content')
<div class="row" id="user">
    <div class="col-12 col-md-12">
        <div class="card mb-0">
            <div class="card-body">
                <h5 class="card-title">User Entry Form</h5>
                <form @submit.prevent="{{ buttonAction('entry') || buttonAction('update') ? 'saveData($event)' : null }}">
                    <div class="row">
                        <div class="col-12 col-md-5">
                            <div class="mb-1 row">
                                <label class="form-label col-4 col-md-3" for="name">Name:</label>
                                <div class="col-8 col-md-9">
                                    <input type="text" class="form-control" autocomplete="off" id="name" name="name" v-model="user.name" />
                                </div>
                            </div>
                            <div class="mb-1 row">
                                <label class="form-label col-4 col-md-3" for="email">Email:</label>
                                <div class="col-8 col-md-9">
                                    <input type="email" class="form-control" autocomplete="off" id="email" name="email" v-model="user.email" />
                                </div>
                            </div>
                            <div class="mb-1 row">
                                <label class="form-label col-4 col-md-3" for="phone">Mobile:</label>
                                <div class="col-8 col-md-9">
                                    <input type="text" class="form-control" autocomplete="off" id="phone" name="phone" v-model="user.phone" />
                                </div>
                            </div>
                            @if(session('branch')->id == 1)
                            <div class="mb-1 row">
                                <label class="form-label col-4 col-md-3" for="branch_id">Branch:</label>
                                <div class="col-8 col-md-9">
                                    <select class="form-select" id="branch_id" name="branch_id" v-model="user.branch_id">
                                        @foreach($branches as $branch)
                                        <option value="{{$branch->id}}">{{$branch->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            @endif
                        </div>
                        <div class="col-12 col-md-5">
                            <div class="mb-1 row">
                                <label class="form-label col-4 col-md-3" for="role">Role:</label>
                                <div class="col-8 col-md-9">
                                    <select class="form-select" id="role" name="role" v-model="user.role">
                                        <option value="" v-if="user.role != 'Superadmin'">Select Role</option>
                                        <option value="Superadmin" v-if="user.role == 'Superadmin'">Super Admin</option>
                                        <option value="admin" v-if="user.role != 'Superadmin'">Admin</option>
                                        <option value="manager" v-if="user.role != 'Superadmin'">Manager</option>
                                        <option value="user" v-if="user.role != 'Superadmin'">User</option>
                                    </select>
                                </div>
                            </div>
                            <div class="mb-1 row">
                                <label class="form-label col-4 col-md-3 pe-0" for="username">Username:</label>
                                <div class="col-8 col-md-9">
                                    <input type="text" class="form-control" autocomplete="off" id="username" name="username" v-model="user.username" />
                                </div>
                            </div>
                            <div class="mb-1 row">
                                <label class="form-label col-4 col-md-3" for="password">Password:</label>
                                <div class="col-8 col-md-9 password position-relative">
                                    <input type="password" class="form-control" autocomplete="off" id="password" name="password" v-model="user.password" />
                                    <i class="bi bi-eye" style="position: absolute;top: 9%;right: 23px;cursor:pointer;" @click="passwordShow($event)"></i>
                                </div>
                            </div>
                            <div class="mt-1 row">
                                <label class="col-md-3 col-12"></label>
                                <div class="col-md-3 col-12">
                                    <label for="status">
                                        <input type="checkbox" name="status" id="status" :false-value="'p'" :true-value="'a'" v-model="user.status" />
                                        IsActive
                                    </label>
                                </div>
                                <div class="col-md-6 col-12 text-end">
                                    @if(buttonAction('entry') || buttonAction('update'))
                                    <button class="btn btn-danger" type="button">Reset</button>
                                    <button class="btn btn-primary" type="submit" :disabled="onProgress">
                                        <span v-if="user.id == ''">Save</span>
                                        <span v-if="user.id != ''">Update</span>
                                    </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-md-2 mt-2 mt-md-0">
                            <div class="form-group ImageBackground">
                                <span class="text-danger">(150 X 150)PX</span>
                                <img :src="imageSrc" class="imageShow" />
                                <label for="image">Upload Image</label>
                                <input type="file" id="image" class="form-control shadow-none" @change="imageUrl" />
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-12 col-md-12 mt-1">
        <vue-good-table :columns="columns" :rows="users" :fixed-header="false" :pagination-options="{
                enabled: true,
                perPage: 100,
            }" :search-options="{ enabled: true }" :line-numbers="true" styleClass="vgt-table condensed" max-height="550px">
            <template #table-row="props">
                <span class="d-flex gap-2 justify-content-end" v-if="props.column.field == 'before'">
                    <a v-if="props.row.role == 'user' || props.row.role == 'manager'" :href="`/userAccess/${props.row.id}`" target="_blank" title="User Access">
                        <i class="bi bi-people text-warning" style="font-size: 14px;"></i>
                    </a>
                    @if(buttonAction('update'))
                    <a href="" title="edit" @click.prevent="editData(props.row)">
                        <i class="bi bi-pen text-info" style="font-size: 14px;"></i>
                    </a>
                    @endif
                    @if(buttonAction('delete'))
                    <a v-if="props.row.role != 'Superadmin'" href="" title="delete" @click.prevent="deleteData(props.row.id)">
                        <i class="bi bi-trash text-danger" style="font-size: 14px;"></i>
                    </a>
                    @endif
                </span>
            </template>
        </vue-good-table>
    </div>
</div>
@endsection

@push("js")
<script>
    new Vue({
        el: "#user",
        data() {
            return {
                columns: [{
                        label: "Code",
                        field: 'code'
                    },
                    {
                        label: "Name",
                        field: 'name'
                    },
                    {
                        label: "Username",
                        field: 'username'
                    },
                    {
                        label: "Email",
                        field: 'email'
                    },
                    {
                        label: "Mobile",
                        field: 'phone'
                    },
                    {
                        label: "Role",
                        field: 'role'
                    },
                    {
                        label: "Branch",
                        field: 'branch.name'
                    },
                    {
                        label: "Status",
                        field: 'statusTxt',
                        html: true,
                    },
                    {
                        label: "Action",
                        field: "before"
                    }
                ],
                user: {
                    id: '',
                    name: '',
                    email: '',
                    phone: '',
                    role: '',
                    username: '',
                    password: '',
                    status: 'a',
                    image: '',
                    branch_id: "{{session('branch')->id}}",
                },
                users: [],

                imageSrc: "/noImage.jpg",
                onProgress: false,
            }
        },

        created() {
            this.getUser();
        },

        methods: {
            getUser() {
                axios.post('/get-user')
                    .then(res => {
                        this.users = res.data.map((item, index) => {
                            item.statusTxt = item.status == 'a' ? "<span class='badge bg-success'>Active</span>" : "<span class='badge bg-warning'>Deactive</span>";
                            return item;
                        });
                    })
            },
            saveData(event) {
                let formdata = new FormData(event.target);
                formdata.append('id', this.user.id);
                formdata.append('image', this.user.image);
                formdata.append('status', this.user.status);
                let url = this.user.id != '' ? '/update-user' : '/user'
                this.onProgress = true;
                axios.post(url, formdata)
                    .then(res => {
                        toastr.success(res.data.message);
                        this.clearData();
                        this.getUser();
                    })
                    .catch(err => {
                        this.onProgress = false
                        var r = JSON.parse(err.request.response);
                        if (err.request.status == '422' && r.errors != undefined && typeof r.errors == 'object') {
                            $.each(r.errors, (index, value) => {
                                $.each(value, (ind, val) => {
                                    toastr.error(val)
                                })
                            })
                        } else {
                            if (r.errors != undefined) {
                                console.log(r.errors);
                            }
                            toastr.error(r.message)
                        }
                    })

            },

            editData(row) {
                let keys = Object.keys(this.user);
                keys.forEach(item => {
                    this.user[item] = row[item];
                })
                this.imageSrc = row.image ? '/' + row.image : "/noImage.jpg";
            },

            deleteData(rowId) {
                if (!confirm('Are you sure ?')) {
                    return;
                }
                axios.post('/delete-user', {
                        id: rowId
                    })
                    .then(res => {
                        if (res.data.status) {
                            toastr.success(res.data.message);
                            this.getUser();
                        }
                    })
            },

            clearData() {
                this.user = {
                    id: '',
                    name: '',
                    email: '',
                    phone: '',
                    role: '',
                    username: '',
                    password: '',
                    status: 'a',
                    image: '',
                    branch_id: "{{session('branch')->id}}"
                }
                this.imageSrc = "/noImage.jpg";
                this.onProgress = false;
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
            },
            passwordShow(event) {
                let password = $(".password").find('input').prop('type');
                if (password == 'password') {
                    $(".password").find('i').removeProp('class').prop('class', 'bi bi-eye-slash')
                    $(".password").find('input').removeProp('type').prop('type', 'text');
                } else {
                    $(".password").find('i').removeProp('class').prop('class', 'bi bi-eye')
                    $(".password").find('input').removeProp('type').prop('type', 'password');
                }
            }
        },
    })
</script>
@endpush
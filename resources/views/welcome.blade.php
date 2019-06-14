<!DOCTYPE html>
<html lang="{{ config('app.locale') }}">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">

<title>Vue.js CRUD - Just Laravel</title>

<!-- Fonts -->
    <link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
<!-- Styles -->
<style>
    body{
        font-size: 16px;
    }
    .form-control{
        font-size: 14px!important;
    }

.modal-mask {
  position: fixed;
  z-index: 9998;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background-color: rgba(0, 0, 0, .5);
  display: table;
  transition: opacity .3s ease;
}

.modal-wrapper {
  display: table-cell;
  vertical-align: middle;
}

.modal-container {
  width: 50%;
  margin: 0px auto;
  padding: 20px 30px;
  background-color: #fff;
  border-radius: 2px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, .33);
  transition: all .3s ease;
  font-family: Helvetica, Arial, sans-serif;
}

.modal-header h3 {
  margin-top: 0;
  color: #42b983;
}

.modal-body {
  margin: 20px 0;
}
</style>
</head>
<body>
<div class="container" style="margin-top: 30px">
    <div class="flex-center position-ref full-height">
        <div id="vue-wrapper">
            <div class="row">
                <div class="col-md-12">
                    <h1 class="text-center">Laravel With Vue Js CRUD</h1>
                    <hr>
                </div>
                <div class="col-md-6">
                    <p class="text-center alert alert-danger" v-bind:class="{ hidden: hasError }">Please fill all fields!</p>
                    <p class="text-center alert alert-danger" v-bind:class="{ hidden: hasAgeError }">Please enter a valid age!</p>
                    {{ csrf_field() }}
                    <p class="text-center alert alert-success" v-bind:class="{ hidden: hasSave }">Item Save Successfully!</p>

                    <div class="form-group">
                        <label for="name">Name:</label>
                        <input type="text" class="form-control" id="name" name="name" required v-model="newItem.name" placeholder=" Enter some name">
                    </div>

                    <div class="form-group">
                        <label for="age">Age:</label>
                        <input type="number" class="form-control" id="age" name="age" required v-model="newItem.age" placeholder=" Enter your age">
                    </div>
                    <div class="form-group">
                        <label for="profession">Profession:</label>
                        <input type="text" class="form-control" id="profession" name="profession" required v-model="newItem.profession" placeholder=" Enter your profession">
                    </div>

                    <button class="btn btn-primary" @click.prevent="createItem()" id="name" name="name">
                        <span class="glyphicon glyphicon-plus"></span> ADD
                    </button>
                </div>
                <div class="col-md-6">
                    <p class="text-center alert alert-success" v-bind:class="{ hidden: hasDeleted }">Deleted Successfully!</p>
                    <p class="text-center alert alert-success" v-bind:class="{ hidden: hasUpdate }">Item Update Successfully!</p>
                    <div class="table table-borderless" id="table">
                        <table class="table table-borderless" id="table">
                            <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Age</th>
                                <th>Profession</th>
                                <th>Actions</th>
                            </tr>
                            </thead>
                            <tr v-for="item in items">
                                <td>@{{ item.id }}</td>
                                <td>@{{ item.name }}</td>
                                <td>@{{ item.age }}</td>
                                <td>@{{ item.profession }}</td>

                                <td id="show-modal" @click="showModal=true; setVal(item.id, item.name, item.age, item.profession)"  class="btn btn-info" ><span
                                            class="glyphicon glyphicon-pencil"></span></td>
                                <td @click.prevent="deleteItem(item)" class="btn btn-danger"><span
                                            class="glyphicon glyphicon-trash"></span></td>
                            </tr>
                        </table>
                    </div>
                    <modal v-if="showModal" @close="showModal=false">
                        <h3 slot="header">Edit Item</h3>
                        <div slot="body">

                            <input type="hidden" disabled class="form-control" id="e_id" name="id" required  :value="this.e_id">
                            Name: <input type="text" class="form-control" id="e_name" name="name" required  :value="this.e_name">
                            Age: <input type="number" class="form-control" id="e_age" name="age" required  :value="this.e_age">
                            Profession: <input type="text" class="form-control" id="e_profession" name="profession" required  :value="this.e_profession">
                        </div>
                        <div slot="footer">
                            <button class="btn btn-default" @click="showModal = false">
                                Cancel
                            </button>

                            <button class="btn btn-info" @click="editItem()">
                                Update
                            </button>
                        </div>
                    </modal>
                </div>
            </div>

        </div>
    </div>
</div>

    <script type="text/javascript" src="{{ asset('js/app.js') }}"></script>
    <script>
        var app = new Vue({
            el: '#vue-wrapper',

            data: {
                items: [],
                hasError: true,
                hasDeleted: true,
                hasSave: true,
                hasUpdate: true,
                hasAgeError: true,
                showModal: false,
                e_name: '',
                e_age: '',
                e_id: '',
                e_profession: '',
                newItem: { 'name': '', 'age': '', 'profession': '' }
            },
            mounted: function mounted() {
                this.getVueItems();
            },
            methods: {
                getVueItems: function getVueItems() {
                    var _this = this;

                    axios.get("{!! url('vueitems') !!}").then(function (response) {
                        _this.items = response.data;
                    });
                },
                setVal: function setVal(val_id, val_name, val_age, val_profession) {
                    this.e_id = val_id;
                    this.e_name = val_name;
                    this.e_age = val_age;
                    this.e_profession = val_profession;
                },


                createItem: function createItem() {
                    var _this = this;
                    var input = this.newItem;

                    if (input['name'] == '' || input['age'] == '' || input['profession'] == '') {
                        this.hasError = false;
                    } else {
                        this.hasError = true;
                        axios.post("{!! url('/vueitems') !!}", input).then(function (response) {
                            _this.newItem = { 'name': '', 'age': '', 'profession': '' };
                            _this.getVueItems();
                        });
                        this.hasSave = false;
                        this.hasDeleted = true;
                    }
                },
                editItem: function editItem() {
                    var _this2 = this;

                    var i_val_1 = document.getElementById('e_id');
                    var n_val_1 = document.getElementById('e_name');
                    var a_val_1 = document.getElementById('e_age');
                    var p_val_1 = document.getElementById('e_profession');

                    axios.post("{!! url("edititems") !!}/" + i_val_1.value, { val_1: n_val_1.value, val_2: a_val_1.value, val_3: p_val_1.value }).then(function (response) {
                        _this2.getVueItems();
                        _this2.showModal = false;

                    });
                    this.hasUpdate = false;
                    this.hasDeleted = true;
                },
                deleteItem: function deleteItem(item) {
                    var _this = this;
                    axios.post("{!! url('vueitems') !!}/" + item.id).then(function (response) {
                        _this.getVueItems();
                        _this.hasError = true, _this.hasDeleted = false;
                    });
                }
            }
        });
    </script>
    <script type="text/x-template" id="modal-template">
      <transition name="modal">
        <div class="modal-mask">
          <div class="modal-wrapper">
            <div class="modal-container">

              <div class="modal-header">
                <slot name="header">
                  default header
                </slot>
              </div>

              <div class="modal-body">
                <slot name="body">

                </slot>
              </div>

              <div class="modal-footer">
                <slot name="footer">


                </slot>
              </div>
            </div>
          </div>
        </div>
      </transition>
    </script>


</body>
</html>
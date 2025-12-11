<?php
// app/Controllers/User.php
namespace App\Controllers;
use App\Models\UserModel;

class User extends BaseController {
    public function index() {
        $model = new UserModel();
        $data['users'] = $model->findAll();
        return view('user_list', $data);
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redis;

class UserController extends Controller
{
    function index(Request $request) {
        //get all users data with pagination
        $users = DB::table('users')
        ->when($request->input('name'), function($query, $name){
            $query->where('name', 'like', '%' . $name. '%')
            ->orWhere('email', 'like','%'. $name . '%');
        })
        ->paginate(10);
        return view('pages.users.index', compact('users'));
    }

    function edit($userId) {
        $user = User::findOrFail($userId);
        return view('pages.users.edit', compact('user'));
    }

    function create() {
        return view('pages.users.create');
    }

    function store(Request $request) {

        //validate
        $request->validate([
            'name' => 'required',
            'email' => 'required|email:unique',
            'password' => 'required|min:8',
            'role' => 'required|in:admin,staff,user',
        ]);

        // store the request
        $user = new User;
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = Hash::make($request->password);
        $user->role = $request->role;
        $user->save();

        return redirect()->route('users.index')->with('success', 'User created succesfully');

    }

    function show()  {
        return view('pages.users.show');
    }

    function update(Request $request, $userId) {
        //validate
        $request->validate([
            'name' => 'required',
            'email' => 'required',
            'role' => 'required|in:admin,staff,user'
        ]);

        // update the request
        $user = User::find($userId);
        $user->name = $request->name;
        $user->email = $request->email;
        $user->role = $request->role;
        $user->save();

        //if password is not empty
        if($request->password){
            $user->password = Hash::make($request->password);
            $user->save();
        }

        return redirect()->route('users.index')->with('success', 'User updated successfully');
    }

    function destroy($userId){
        //delete the request
        $user = User::find($userId);
        $user->delete();

        return redirect()->route('users.index')->with('success', 'User deleted successfully');
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use App\Http\Requests\UpdateUserRequest;
use App\Http\Requests\CreateUserRequest;
use App\Model\User;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $columns = [
            'id',
            'email',
            'fullname',
            'birthday',
            'address',
            'image',
            'is_admin',
        ];
        $users = User::select($columns)->paginate(User::ROW_LIMIT);
        return view('backend.users.index', compact('users'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $user object of user
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(User $user)
    {
        return view('backend.users.update', compact('user'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\UpdateRequest $request request to update
     * @param int                            $user    object of user
     *
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateUserRequest $request, User $user)
    {
        $input = $request->all();
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $fileName = config('image.name_prefix') . "-" . $file->hashName();
            $file->move(config('image.users.path_upload'), $fileName);
            $input['image'] = $fileName;
        }
        if ($user->update($input)) {
            flash(__('Update successful!'))->success();
            return redirect()->route('users.index');
        } else {
            flash(__('Update failed!'))->error();
            return redirect()->back()->withInput();
        }
    }

    /**
     * Update role of user.
     *
     * @param int $user object of user
     *
     * @return \Illuminate\Http\Response
     */
    public function updateRole(User $user)
    {
        if ($user->is_admin == User::ROLE_ADMIN) {
            $user->update(['is_admin' => User::ROLE_USER]);
        } else {
            $user->update(['is_admin' => User::ROLE_ADMIN]);
        }
        return redirect()->route('users.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('backend.users.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request request
     *
     * @return \Illuminate\Http\Response
     */
    public function store(CreateUserRequest $request)
    {
        $users = new User($request->all());
        if ($request->hasFile('image')) {
            $users->image = config('image.name_prefix') .'-'. $request->image->hashName();
            $request->file('image')->move(config('image.users.path_upload'), $users->image);
        }
        if ($users->save()) {
            flash(__('Creation successful!'))->success();
        } else {
            flash(__('Creation failed!'))->error();
        }
        return redirect()->route('users.index');
    }
}

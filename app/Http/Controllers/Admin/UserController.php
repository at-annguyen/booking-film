<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
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
        $users = User::search()
                     ->select($columns)->paginate(User::ROW_LIMIT);
        $users->appends(['search' => request('search')]);
        return view('backend.users.index', compact('users'));
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
            $request->file('image')->move(config('image.users.path'), $users->image);
        }
        if ($users->save()) {
            flash(__('Creation successful!'))->success();
        } else {
            flash(__('Creation failed!'))->error();
        }
        return redirect()->route('users.index');
    }
}

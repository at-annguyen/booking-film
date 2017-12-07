<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateFilmRequest;
use App\Model\Film;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FilmController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
         $films = Film::select()
                      ->paginate(Film::ROW_LIMIT);
         return view('backend.films.index', compact('films'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $film object of film
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(Film $film)
    {
        if ($film->delete()) {
            if ($film->image != null && file_exists(base_path('public/images/film/'.$film->image))) {
                unlink(base_path('public/images/film/'.$film->image));
            }
            flash(__('Deletion successful!'))->success();
        } else {
            flash(__('Deletion failed!'))->error();
        }
        return redirect()->route('films.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $technologies = Film::$technologies;
        $rated = Film::$rated;
        $actived = Film::STATUS_ACTIVED;
        $disabled = Film::STATUS_DISABLED;
        return view(
            'backend.films.create',
            compact(
                'technologies',
                'rated',
                'actived',
                'disabled'
            )
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request request
     *
     * @return \Illuminate\Http\Response
     */
    public function store(CreateFilmRequest $request)
    {
        $films = new Film($request->all());
        if ($request->hasFile('image')) {
            $films->image = config('image.name_prefix') .'-'. $request->image->hashName();
            $request->file('image')->move(config('image.films.path_upload'), $films->image);
        }
        if ($films->save()) {
            flash(__('Creation successful!'))->success();
        } else {
            flash(__('Creation failed!'))->error();
        }
        return redirect()->route('films.index');
    }
}

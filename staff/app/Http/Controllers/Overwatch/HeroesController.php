<?php
namespace App\Http\Controllers\Overwatch;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\Http\Controllers\Controller;
use App\Models\Overwatch\OverwatchHero;
use App\Http\Requests\OverwatchHeroRequest;

class HeroesController extends Controller
{
    public function index()
    {
        $heroes = OverwatchHero::all();
        $roles = OverwatchHero::OW_ROLES;
        return view('overwatch.heroes.list', compact('heroes', 'roles'));
    }

    public function create()
    {
        $roles = OverwatchHero::OW_ROLES;
        return view('overwatch.heroes.edit', compact('roles'));
    }

    public function store(OverwatchHeroRequest $request)
    {
        $slug = $this->getAvailableSlug($request->get('name'));
        $portrait = $request->file('image');

        $s3 = \Storage::disk('s3');
        $filePath = 'ow-heroes/' . $slug . time() . '.' . $portrait->getClientOriginalExtension();
        $s3->put($filePath, file_get_contents($portrait), 'public');

        OverwatchHero::create([
            'name' => $request->get('name'),
            'image' => $filePath,
            'info' => $request->get('info'),
            'slug_name' => $slug,
            'role' => $request->get('role'),
            'active' => $request->get('active')
        ]);

        return redirect()->back();
    }

    public function show($id)
    {
        $hero = OverwatchHero::findOrFail($id);
        $roles = OverwatchHero::OW_ROLES;
        return view('overwatch.heroes.show', compact('roles'));
    }

    public function edit($id)
    {
        $hero = OverwatchHero::findOrFail($id);
        $roles = OverwatchHero::OW_ROLES;
        return view('overwatch.heroes.edit', compact('hero', 'roles'));
    }

    public function update(OverwatchHeroRequest $request, $id)
    {
        $hero = OverwatchHero::findOrFail($id);

        $hero->fill([
            'name' => $request->get('name'),
            'info' => $request->get('info'),
            'role' => $request->get('role'),
            'active' => $request->get('active')
        ]);

        if($portrait = $request->file('image')) {
            $s3 = \Storage::disk('s3');
            $s3->delete($hero->image);
            $filePath = 'ow-heroes/' . $hero->slug_name . time() . '.' . $portrait->getClientOriginalExtension();
            $s3->put($filePath, file_get_contents($portrait), 'public');
            $hero->image = $filePath;
        }

        $hero->save();

        return redirect()->back();
    }

    public function delete($id)
    {
        if(Auth::user()->hasRole('ow_heroes')) {
            OverwatchHero::destroy($id);
            return redirect()->back();
        }
        return abort('403');
    }


    private function getAvailableSlug($name)
    {
        $slug = Str::slug($name);
        $count = OverwatchHero::whereRaw("slug_name RLIKE '^{$slug}(-[0-9]+)?$'")->count();
        return $count ? "{$slug}-{$count}" : $slug;
    }
}
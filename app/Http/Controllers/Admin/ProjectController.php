<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\Type;
use App\Http\Requests\StoreProjectRequest;
use App\Http\Requests\UpdateProjectRequest;
use App\Models\Technology;
use Illuminate\Support\Facades\Storage;

class ProjectController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $projects = Project::all();
        return view('admin.projects.index', compact('projects'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $types = Type::all();
        $technologies = Technology::all();
        return view('admin.projects.create', compact ('types', 'technologies'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreProjectRequest $request)
    {
        // $form_data = $request->validated();
        // $slug = Project::generateSlug($form_data['title']);
        // $form_data['slug'] = $slug;
        // $project = Project::create($form_data);
        // $project->technologies()->attach($request->technologies);

        // if ($request->hasFile('cover_image')){
        // //     $img_path = Storage::put('public', $form_data['cover_image']);
        // //     $form_data['cover_image'] = $img_path;
        //     $img_path = Storage::disk('public')->put('project_images', $request->cover_image);
        //     $form_data['cover_image'] = $img_path;
        // }

        $form_data = $request->validated();
        $slug = Project::generateSlug($request->title);
        $form_data['slug'] = $slug;
        $project = new Project();
        $project = Project::create($form_data);
        $project->technologies()->attach($request->technologies);
        if ($request->hasFile('cover_image')) {
            $path = Storage::disk('public')->put('project_images', $request->cover_image);

            $form_data['cover_image'] = $path;
        }

        $project->fill($form_data);
        $project->save();

        return redirect()->route('admin.projects.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Project  $project
     * @return \Illuminate\Http\Response
     */
    public function show(Project $project)
    {
        return view('admin.projects.show', compact('project'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Project  $project
     * @return \Illuminate\Http\Response
     */
    public function edit(Project $project)
    {
        $types = Type::all();
        $technologies = Technology::all();
        return view('admin.projects.edit', compact('project', 'types', 'technologies'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Project  $project
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateProjectRequest $request, Project $project)
    {
        $form_data = $request->validated();
        $slug = Project::generateSlug($form_data['title']);
        $form_data['slug'] = $slug;
        if($request->has('cover_image')) {
            if($project->cover_image) {
                Storage::delete($project->cover_image);;
            }
        }
        $project->technologies()->sync($request->technologies);
        $project->update($form_data);
        return redirect()->route('admin.projects.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Project  $project
     * @return \Illuminate\Http\Response
     */
    public function destroy(Project $project)
    {
        $project->delete();
        $project->technologies()->sync([]);
        return redirect()->route('admin.projects.index');
    }
}

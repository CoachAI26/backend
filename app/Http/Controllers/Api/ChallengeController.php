<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResource;
use App\Http\Resources\ChallengeResource;
use App\Http\Resources\LevelResource;
use App\Models\Category;
use App\Models\Challenge;
use App\Models\Level;
use Illuminate\Http\Request;

class ChallengeController extends Controller
{
    public function categories(): \Illuminate\Http\Resources\Json\AnonymousResourceCollection
    {
        return CategoryResource::collection(Category::orderBy('order')->get());
    }

    public function levels(): \Illuminate\Http\Resources\Json\AnonymousResourceCollection
    {
        return LevelResource::collection(Level::orderBy('order')->get());
    }

    public function index(Request $request): \Illuminate\Http\Resources\Json\AnonymousResourceCollection
    {
        $query = Challenge::with(['category', 'level']);

        if ($request->has('category')) {
            $category = Category::where('slug', $request->category)->firstOrFail();
            $query->where('category_id', $category->id);
        }

        if ($request->has('level')) {
            $level = Level::where('slug', $request->level)->firstOrFail();
            $query->where('level_id', $level->id);
        }

        return ChallengeResource::collection($query->get());
    }

    public function show(Challenge $challenge): ChallengeResource
    {
        $challenge->load(['category', 'level']);
        return new ChallengeResource($challenge);
    }
}

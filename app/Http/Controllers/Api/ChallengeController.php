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
use OpenApi\Attributes as OA;

class ChallengeController extends Controller
{
    #[OA\Get(
        path: '/categories',
        summary: 'List all categories',
        description: 'Returns all challenge categories ordered by their display order.',
        tags: ['Challenges'],
    )]
    #[OA\Response(
        response: 200,
        description: 'List of categories',
        content: new OA\JsonContent(
            type: 'object',
            properties: [
                new OA\Property(property: 'data', type: 'array', items: new OA\Items(
                    properties: [
                        new OA\Property(property: 'id', type: 'integer', example: 1),
                        new OA\Property(property: 'slug', type: 'string', example: 'public-speaking'),
                        new OA\Property(property: 'name', type: 'string', example: 'Public Speaking'),
                        new OA\Property(property: 'description', type: 'string', example: 'Practice your public speaking skills'),
                        new OA\Property(property: 'icon', type: 'string', example: 'microphone'),
                        new OA\Property(property: 'order', type: 'integer', example: 1),
                    ],
                )),
            ],
        ),
    )]
    public function categories(): \Illuminate\Http\Resources\Json\AnonymousResourceCollection
    {
        return CategoryResource::collection(Category::orderBy('order')->get());
    }

    #[OA\Get(
        path: '/levels',
        summary: 'List all levels',
        description: 'Returns all difficulty levels ordered by their display order.',
        tags: ['Challenges'],
    )]
    #[OA\Response(
        response: 200,
        description: 'List of levels',
        content: new OA\JsonContent(
            type: 'object',
            properties: [
                new OA\Property(property: 'data', type: 'array', items: new OA\Items(
                    properties: [
                        new OA\Property(property: 'id', type: 'integer', example: 1),
                        new OA\Property(property: 'slug', type: 'string', example: 'beginner'),
                        new OA\Property(property: 'name', type: 'string', example: 'Beginner'),
                        new OA\Property(property: 'description', type: 'string', example: 'For those just starting out'),
                        new OA\Property(property: 'color', type: 'string', example: '#4CAF50'),
                        new OA\Property(property: 'min_score', type: 'number', example: 0),
                        new OA\Property(property: 'order', type: 'integer', example: 1),
                    ],
                )),
            ],
        ),
    )]
    public function levels(): \Illuminate\Http\Resources\Json\AnonymousResourceCollection
    {
        return LevelResource::collection(Level::orderBy('order')->get());
    }

    #[OA\Get(
        path: '/challenges',
        summary: 'List challenges',
        description: 'Returns challenges with optional category and level filtering.',
        tags: ['Challenges'],
    )]
    #[OA\Parameter(name: 'category', in: 'query', required: false, description: 'Filter by category slug', schema: new OA\Schema(type: 'string'), example: 'public-speaking')]
    #[OA\Parameter(name: 'level', in: 'query', required: false, description: 'Filter by level slug', schema: new OA\Schema(type: 'string'), example: 'beginner')]
    #[OA\Response(
        response: 200,
        description: 'List of challenges',
        content: new OA\JsonContent(
            type: 'object',
            properties: [
                new OA\Property(property: 'data', type: 'array', items: new OA\Items(ref: '#/components/schemas/Challenge')),
            ],
        ),
    )]
    #[OA\Response(response: 404, description: 'Category or level not found')]
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

    #[OA\Get(
        path: '/challenges/{id}',
        summary: 'Get a single challenge',
        description: 'Returns a specific challenge by ID, including its category and level.',
        tags: ['Challenges'],
    )]
    #[OA\Parameter(name: 'id', in: 'path', required: true, description: 'Challenge ID', schema: new OA\Schema(type: 'integer'), example: 1)]
    #[OA\Response(
        response: 200,
        description: 'Challenge details',
        content: new OA\JsonContent(
            type: 'object',
            properties: [
                new OA\Property(property: 'data', ref: '#/components/schemas/Challenge'),
            ],
        ),
    )]
    #[OA\Response(response: 404, description: 'Challenge not found')]
    public function show(Challenge $challenge): ChallengeResource
    {
        $challenge->load(['category', 'level']);
        return new ChallengeResource($challenge);
    }
}

<?php

namespace App\Http\Controllers;

use OpenApi\Attributes as OA;

#[OA\Info(
    version: '1.0.0',
    title: 'SeanAI Assistance API',
    description: 'Speech practice and AI-powered analysis API for SpeechFlow. Provides authentication, challenge browsing, practice session management, and AI-driven speech recording analysis.',
    contact: new OA\Contact(name: 'API Support', email: 'support@speechflow.app'),
)]
#[OA\Server(url: '/api/v1', description: 'API v1')]
#[OA\SecurityScheme(
    securityScheme: 'sanctum',
    type: 'http',
    scheme: 'bearer',
    bearerFormat: 'Sanctum Token',
    description: 'Enter the Bearer token obtained from /auth/login or /auth/register',
)]
#[OA\Tag(name: 'Authentication', description: 'Register, login, logout & current user')]
#[OA\Tag(name: 'Password', description: 'Forgot & reset password')]
#[OA\Tag(name: 'Challenges', description: 'Browse categories, levels & challenges')]
#[OA\Tag(name: 'Practice Sessions', description: 'Start and view practice sessions')]
#[OA\Tag(name: 'Recordings', description: 'Upload audio recordings for AI speech analysis')]
#[OA\Tag(name: 'Profile', description: 'User profile, statistics, history & account management')]
abstract class Controller
{
    //
}

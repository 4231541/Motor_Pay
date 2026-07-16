<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;

/**
 * @OA\Info(
 *     version="1.0.0",
 *     title="Car Marketplace API",
 *     description="Production-ready Laravel API for a Car Marketplace & Financing Platform",
 *     @OA\Contact(
 *         email="admin@example.com"
 *     )
 * )
 *
 * @OA\Server(
 *     url=L5_SWAGGER_CONST_HOST,
 *     description="API Server"
 * )
 *
 * @OA\SecurityScheme(
 *     type="http",
 *     scheme="bearer",
 *     securityScheme="sanctum",
 *     bearerFormat="JWT"
 * )
 */
class ApiController extends Controller
{
    // Base controller just for Swagger Global Annotations
}

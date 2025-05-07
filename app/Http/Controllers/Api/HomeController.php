<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

/**
 * @group Home
 * @groupDescription Core endpoints for application health and status monitoring
 */
class HomeController extends Controller
{
    /**
     * Health Check
     * 
     * Returns the current health status of the server
     * 
     * @response 200 {
     *   "code": 200,
     *   "status": "success",
     *   "message": "Server is running"
     * }
     * 
     * @responseField status string The status of the server
     * @responseField message string A descriptive message about the server status
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function healthCheck()
    {
        return response()->json([
            'code' => 200,
            'status' => 'success',
            'message' => 'Server is running'
        ]);
    }
}

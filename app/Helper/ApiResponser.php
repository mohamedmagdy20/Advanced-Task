<?php 
namespace App\Helper; 

class ApiResponser
{

    public static function successResponse($message = null , $status = 200 , $data = null )
    {
        return response()->json([
            'message'=>$message,
            'status'=>$status,
            'data'=>$data
        ],$status);
    }

    /**
    * Handle Erorr Response 
    * @return \Illuminate\Http\JsonResponse
    */
    public static function errorResponse($message = null , $status = 400,$data = null )
    {
        return response()->json([
            'message'=>$message,
            'status'=>$status,
            'data'=>$data
        ],$status);
    }

    /**
     * Return a JSON response for unauthorized access.
     *
     * @param  string  $message
     * @return \Illuminate\Http\JsonResponse
     */
    public static function unauthorizedResponse(string $message = 'Unauthorized')
    {
        return self::errorResponse($message, 401);
    }
    /**
     * Return a JSON response for not found.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public static function notFoundResponse(string $message = 'Resource not found')
    {
        return self::errorResponse($message, 404);
    }

    /**
     * Return a JSON response for validation errors.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public static function validationErrorResponse($errors, string $message = 'Validation failed')
    {
        return self::errorResponse($message, 422, $errors);
    }

    /**
     * Return a JSON response for server error.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public static function serverErrorsResponse(string $message = 'Internal server error')
    {
        return self::errorResponse($message, 500);
    }
}
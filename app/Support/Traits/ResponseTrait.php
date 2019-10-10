<?php

namespace App\Support\Traits;

use Symfony\Component\HttpFoundation\Response as FoundationResponse;

trait ResponseTrait
{
    use ResponseStatusTrait;

    /*
    |--------------------------------------------------------------------------
    | api 响应
    |--------------------------------------------------------------------------
    |api 响应格式说明
    |{
    |   "status":"",   必须返回，只能为 success 或者 error
    |   "code":"",     必须返回，http状态码
    |   "message":"",  可选返回，作为请求成功 或者 失败的一个备注说明
    |   "data":"",     可选返回，请求成功需要返回数据时返回
    |   "errors":""    可选返回，请求失败返回的具体错误信息说明
    |}
    |
    */

    /**
     * @param int $code
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function success($code = FoundationResponse::HTTP_OK)
    {
        return response()->json([
            'status' => self::$successStatus,
            'code' => $code,
        ], $code);
    }

    /**
     * @param string $message
     * @param int $code
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function successWithMessage($message, $code = FoundationResponse::HTTP_OK)
    {
        return response()->json([
            'status' => self::$successStatus,
            'code' => $code,
            'message' => $message,
        ], $code);
    }

    /**
     * @param array $data
     * @param int $code
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function successWithData($data, $code = FoundationResponse::HTTP_OK)
    {
        return response()->json([
            'status' => self::$successStatus,
            'code' => $code,
            'data' => $data,
        ], $code);
    }

    /**
     * @param int $code
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function failed($code = FoundationResponse::HTTP_BAD_REQUEST)
    {
        return response()->json([
            'status' => self::$errorStatus,
            'code' => $code,
        ], $code);
    }

    /**
     * @param        $message
     * @param int $code
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function failedWithMessage($message, $code = FoundationResponse::HTTP_BAD_REQUEST)
    {
        return response()->json([
            'status' => self::$errorStatus,
            'code' => $code,
            'message' => $message,
        ], $code);
    }

    /**
     * @param array $errors
     * @param string $message
     * @param int $code
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function failedWithMessageAndErrors($errors, $message, $code = FoundationResponse::HTTP_BAD_REQUEST)
    {
        return response()->json([
            'status' => self::$errorStatus,
            'code' => $code,
            'message' => $message,
            'errors' => $errors,
        ], $code);
    }

    public function createSuccess($data = [])
    {
        if (is_array($data) && sizeof($data)) {
            return $this->successWithData($data, FoundationResponse::HTTP_CREATED);
        } else if (is_string($data)) {
            return $this->successWithMessage($data, FoundationResponse::HTTP_CREATED);
        } else {
            return $this->success(FoundationResponse::HTTP_CREATED);
        }
    }

}

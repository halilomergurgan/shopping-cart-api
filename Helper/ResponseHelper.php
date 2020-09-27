<?php

class ResponseHelper
{
    /**
     * @param $message
     * @param $statusCode
     * @return \Slim\Http\Response
     */
    public static function success($message, $statusCode)
    {
        $response = new \Slim\Http\Response();

        $returnData = [
            'status' => true,
            'code' => $statusCode,
            'message' => $message,
        ];

        return $response->withStatus($statusCode)->withHeader("Content-Type", "application/json")->withJson($returnData);
    }

    /**
     * @param $message
     * @param $statusCode
     * @return \Slim\Http\Response
     */
    public static function error($message, $statusCode)
    {
        $data = [
            'status' => false,
            'code' => $statusCode,
            'message' => $message,
        ];

        $response = new \Slim\Http\Response();

        return $response->withStatus($statusCode)->withHeader("Content-Type", "application/json")->withJson($data);
    }

    public static function compact($message, $statusCode, $data = [])
    {
        $response = new \Slim\Http\Response();

        $returnData = [
            'status' => true,
            'code' => $statusCode,
            'message' => $message,
            'data' => $data
        ];

        return $response->withStatus($statusCode)->withHeader("Content-Type", "application/json")->withJson($returnData);
    }
}
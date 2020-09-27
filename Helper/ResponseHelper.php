<?php

class ResponseHelper
{
    /**
     * @param $message
     * @param $statusCode
     * @return \Slim\Http\Response
     */
    public static function success(string $message, int $statusCode)
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
    public static function error(string $message, int $statusCode)
    {
        $data = [
            'status' => false,
            'code' => $statusCode,
            'message' => $message,
        ];

        $response = new \Slim\Http\Response();

        return $response->withStatus($statusCode)->withHeader("Content-Type", "application/json")->withJson($data);
    }

    public static function compact(int $statusCode, $data = [])
    {
        $response = new \Slim\Http\Response();

        $returnData = [
            'status' => true,
            'code' => $statusCode,
            'data' => $data
        ];

        return $response->withStatus($statusCode)->withHeader("Content-Type", "application/json")->withJson($returnData);
    }
}
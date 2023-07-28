<?php

namespace App\Traits;

use Vinkla\Hashids\Facades\Hashids;

trait Response
{

    /**
     * Json response helper
     *
     * @param $data
     * @param int $code
     * @param string[] $messages
     * @param array $extra
     * @return \Illuminate\Http\JsonResponse
     */
    public static function response($data, $code = 200, $pagination = false,  $messages = ["Request Completed Successfully"], $extra = [])
    {
        $response = array_merge([
            "code" => $code,
            "messages" => $messages
        ], $extra);

        $response['data'] = [];

        if ($code >= 200 && $data) {

            if ($pagination) {

                $data = $data->response()->getData(true);

                $response = array_merge([
                    "code" => $code,
                    "messages" => $messages,
                    'data' => $data['data'],
                    'pagination' => $data['pagination']
                ], $extra);
            } else {
                $response['data'] = $data;
            }
        }


        if ($code == 406 || $code == 401 || $code >= 400) {
            $response = [
                "code" => $code,
                "messages" => $messages
            ];
            return response()->json(['error' => $response], $code);
        }

        return response()->json(['response' => $response], $code);
    }

    public function uploadImage($request, $path, $fileName)
    {
        $extension = $request->file('profile_picture')->getClientOriginalExtension();

        return \Storage::disk(env('DISK'))->put('user', $request->profile_picture);
    }
}

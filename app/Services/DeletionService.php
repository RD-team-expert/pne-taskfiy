<?php

namespace App\Services;

use Illuminate\Support\Facades\Session;

class DeletionService
{
    public static function delete($model, $id, $type)
    {
        try {
            if ($id > 0) {
                $item = $model::find($id);
                if (!$item) {
                    return self::errorResponse($type . ' not found.', []);
                }
                $title = $type == 'User' || $type == 'Client' ? $item->first_name . ' ' . $item->last_name : ($type == 'Payslip' ? get_label('payslip_id_prefix', 'PSL-') . $id : ($type == 'Payment' ? get_label('payment_id', 'Payment ID') . $id : $item->title));

                if ($item->delete()) {
                    return self::successResponse($type . ' deleted successfully.', $id, $title);
                }

                return self::errorResponse($type . ' couldn\'t be deleted.');
            } else {
                return self::errorResponse('Default ' . $type . ' cannot be deleted.');
            }
        } catch (\Exception $e) {
            // Log the exception and return a 500 error response
            return response()->json(['error' => true, 'message' => 'An internal server error occurred.'], 500);
        }
    }

    private static function successResponse($message, $id, $title)
    {
        Session::flash('message', $message);
        return formatApiResponse(
            false,
            $message,
            [
                'id' => $id,
                'title' => $title,
                'data' => []
            ]
        );
    }

    private static function errorResponse($message, $data = null)
    {

        $response = ['error' => true, 'message' => $message];

        if (!is_null($data)) {
            $response['data'] = $data;
        }

        return response()->json($response);
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Routing\ResponseFactory;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;

class DownloadController extends Controller
{
    use ValidatesRequests;

    /**
     * @param  Request  $request
     * @param  ResponseFactory  $response
     * @return Response
     *
     * @throws ValidationException
     */
    public function download(Request $request, ResponseFactory $response): Response
    {
        if (!$request->hasValidSignatureWhileIgnoring(['filename', 'path'])) {
            dd(date('Y-m-d H_i_s', $request->get("expires")), $request);
        }

        $data = $this->validate($request, [
            'path'     => 'required',
            'filename' => 'required',
        ]);

        $decryptedPath = decrypt($data['path']);

        if (config('filesystem.default')) {
            app()->terminating(function () use ($decryptedPath) {
                Storage::disk(config('filesystem.default'))->delete($decryptedPath);
            });

            return Storage::disk(config('filesystem.default'))
                ->download($decryptedPath, $data['filename']);
        } else {
            return $response->download(
                decrypt($data['path']),
                $data['filename']
            )->deleteFileAfterSend($shouldDelete = true);
        }
    }
}
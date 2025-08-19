<?php

namespace App\Http\Controllers\Api\DocumentGenerator;

use App\Classes\DocumentGenerator;
use App\Classes\Documents\DocumentGenerator as NewDocumentGenerator;
use App\Exceptions\Api\ApiException;
use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class DocumentGeneratorController extends Controller
{

    public function test()
    {
        return response()->json([
            'message' => 'Доброе утро страна!',
        ]);
    }

    public function generateDocument(Request $request, NewDocumentGenerator $documentGenerator)
    {
        $requestData = $request->all();

        Log::channel('document_generator')->info(
            'Запрос на генерацию документа: ' . var_export($requestData, true)
        );

        try {
            $generatedDocument = $documentGenerator->generateDocumentFromApi($requestData);

            $downloadUrl = [
                'download_link' => route('generated-document.download', [
                    'id' => $generatedDocument->id,
                    'format' => 'word',
                ]),
                'pdf_download_link' => route('generated-document.download', [
                    'id' => $generatedDocument->id,
                    'format' => 'pdf',
                ]),
            ];

            Log::channel('document_generator')->info(
                'Документ успешно сгенерирован: ' . var_export($downloadUrl, true)
            );

            return response()->json($downloadUrl);
        } catch (ApiException $apiExcept) {
            Log::channel('document_generator_errors')->error('Document generation API error', [
                'message' => $apiExcept->getUserMessage(),
                'status' => $apiExcept->getStatus(),
                'code' => $apiExcept->getCode(),
                'request_data' => $requestData,
                'exception' => $apiExcept->getTraceAsString()
            ]);

            return response()->json([
                'message' => $apiExcept->getUserMessage()
            ], $apiExcept->getStatus());
        } catch (Exception $e) {
            Log::channel('document_generator_errors')->error('Unknown document generation error', [
                'message' => $e->getMessage(),
                'exception' => get_class($e),
                'code' => $e->getCode(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $requestData
            ]);

            return response()->json([
                'message' => 'Неизвестная ошибка генерации документа'
            ], 500);
        }
    }

    // public function generateDocument(Request $request, DocumentGenerator $documentGenerator)
    // {
    //     $requestData = $request->all();

    //     Log::channel('document_generator')->info(
    //         'Запрос на генерацию документа: ' . var_export($requestData, true)
    //     );

    //     try {
    //         $downloadUrl = $documentGenerator->generateDocumentFromApi($requestData);

    //         Log::channel('document_generator')->info(
    //             'Документ успешно сгенерирован: ' . var_export($downloadUrl, true)
    //         );

    //         return response()->json($downloadUrl);
    //     } catch (ApiException $apiExcept) {
    //         Log::channel('document_generator_errors')->error('Document generation API error', [
    //             'message' => $apiExcept->getUserMessage(),
    //             'status' => $apiExcept->getStatus(),
    //             'code' => $apiExcept->getCode(),
    //             'request_data' => $requestData,
    //             'exception' => $apiExcept->getTraceAsString()
    //         ]);

    //         return response()->json([
    //             'message' => $apiExcept->getUserMessage()
    //         ], $apiExcept->getStatus());
    //     } catch (Exception $e) {
    //         Log::channel('document_generator_errors')->error('Unknown document generation error', [
    //             'message' => $e->getMessage(),
    //             'exception' => get_class($e),
    //             'code' => $e->getCode(),
    //             'file' => $e->getFile(),
    //             'line' => $e->getLine(),
    //             'trace' => $e->getTraceAsString(),
    //             'request_data' => $requestData
    //         ]);

    //         return response()->json([
    //             'message' => 'Неизвестная ошибка генерации документа'
    //         ], 500);
    //     }
    // }
}

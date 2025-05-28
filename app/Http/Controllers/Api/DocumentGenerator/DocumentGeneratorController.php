<?php

namespace App\Http\Controllers\Api\DocumentGenerator;

use App\Classes\DocumentGenerator;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DocumentGeneratorController extends Controller
{
    public function generateDocument(Request $request, DocumentGenerator $documentGenerator)
    {
        $requestData = $request->all();

        $downloadUrl = $documentGenerator->generateDocumentFromApi($requestData);

        return response()->json([
            'downloadUrl' => url($downloadUrl),
        ]);
    }
}

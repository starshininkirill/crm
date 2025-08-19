<?php

namespace App\Http\Controllers\Web\Global;

use App\Classes\Documents\DocumentFileGenerator;
use App\Http\Requests\Global\OptionRequest;
use App\Http\Controllers\Controller;
use App\Models\Documents\GeneratedDocument;
use App\Models\Global\Option;

class DocumentGeneratorController extends Controller
{
    public function __construct(
        protected DocumentFileGenerator $documentFileGenerator
    ) {}

    public function download(int $id, string $format, string $sign)
    {
        $generatedDocument = GeneratedDocument::findOfFail($id);
        $withSign = $sign === 'with-sign';

        $file =  $this->documentFileGenerator->generateFile($generatedDocument, $format, $withSign);

        return response()->download($file)->deleteFileAfterSend(true);
    }
}

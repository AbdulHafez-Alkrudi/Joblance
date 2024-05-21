<?php

namespace App\Http\Controllers\DocumentAI;

use App\Http\Controllers\BaseController;
use Google\Cloud\DocumentAI\V1\DocumentProcessorServiceClient;

class DocumentAIController extends BaseController
{
    public function processDocument()
    {
        $projectId = 'joblance-418215';
        $location = 'us'; // e.g., 'us' or 'eu'
        $processorId = '32a2e2e0ab131bc';

        $keyFilePath = 'C:\\Users\\LG\\Desktop\\New folder\\Joblance\\storage\\app\\joblance-418215-b1cd749cb951.json';
        $client = new DocumentProcessorServiceClient([
            'credentials' => $keyFilePath
        ]);

        $name = $client->processorName($projectId, $location, $processorId);

        // Ensure the file path is correct and accessible
        $imagePath = public_path('Abbreviation_EN4.png');

        if (!file_exists($imagePath)) {
            // Handle the error appropriately
            die('File does not exist.');
        }

        $content = file_get_contents($imagePath);

        if ($content === false) {
            // Handle the error appropriately
            die('Failed to get file contents.');
        }

        $response = $client->processDocument([
            'name' => $name,
            'rawDocument' => [
                'content' => base64_encode($content), // The content should be base64-encoded
                'mimeType' => 'image/png' // Correct MIME type for PNG images
            ]
        ]);

        return $this->sendResponse($response);
    }
}

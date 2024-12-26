<?php
namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;
use Psr\Log\LoggerInterface;

class DocService
{
    private HttpClientInterface $httpClient;
    private LoggerInterface $logger;
    private string $storageDirectory;

    public function __construct(HttpClientInterface $httpClient, LoggerInterface $logger, string $storageDirectory)
    {
        $this->httpClient = $httpClient;
        $this->logger = $logger;
        $this->storageDirectory = $storageDirectory;
    }

    public function fetchAndStoreDocuments(string $apiUrl): array
    {
        try {
            $response = $this->httpClient->request('GET', $apiUrl);

            if ($response->getStatusCode() !== 200) {
                $errorMessage = 'Failed to fetch documents.';
                $this->logger->error($errorMessage);
                return ['error' => $errorMessage];
            }

            $documents = $response->toArray();

            foreach ($documents as $document) {
                $this->processDocument($document);
            }

            return ['status' => 'success'];
        } catch (\Exception $e) {
            $this->logger->error('Error: ' . $e->getMessage());
            return ['error' => $e->getMessage()];
        }
    }

    private function processDocument(array $document): void
    {
        $decodedFile = base64_decode($document['certificate']);
        $filename = sprintf('%s_%s.pdf', $document['description'], $document['doc_no']);
        file_put_contents($this->storageDirectory . '/' . $filename, $decodedFile);
    }
}


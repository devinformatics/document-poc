<?php
namespace App\Controller;

use App\Service\DocService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    private DocService $docService;

    public function __construct(DocService $docService)
    {
        $this->docService = $docService;
    }

    /**
     * @Route("/{reactRouting}", name="home", defaults={"reactRouting": null})
     */
    public function index(): Response
    {
        return $this->render('home/index.html.twig', [
            'message' => 'Welcome to the Document Service App!',
        ]);
    }

    /**
     * @Route("/trigger-document-service", name="trigger_document_service", methods={"GET"})
     */
    public function triggerDocService(): JsonResponse
    {
        $apiUrl = 'https://raw.githubusercontent.com/RashitKhamidullin/Educhain-Assignment/refs/heads/main/get-documents'; // Replace with actual API URL
        $result = $this->docService->fetchAndStoreDocuments($apiUrl);

        if (isset($result['error'])) {
            return new JsonResponse(['status' => 'error', 'message' => $result['error']], Response::HTTP_BAD_REQUEST);
        }

        return new JsonResponse(['status' => 'success', 'message' => 'Document service triggered successfully.']);
    }
}


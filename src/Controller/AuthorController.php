<?php

namespace App\Controller;

use App\Service\AuthorService;
use App\Service\BookService;
use Exception;
use RuntimeException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AuthorController extends AbstractController
{
    /**
     * @param Request $request
     * @param AuthorService $authorService
     * @return JsonResponse
     * @Route("/api/create-new-author")
     */
    public function createAuthor(Request $request, AuthorService $authorService): JsonResponse
    {
        $json = $request->getContent();
        $body = json_decode($json, true);

        try {
            if (isset($body['name']) && isset($body['surname'])) {
                $authorService->createNewAuthor($body['name'], $body['surname'], $body['idBook'] ?? null);
            } else {
                throw new RuntimeException('Недостаточно данных для записи автора', 400);
            }
        } catch (Exception $e) {
            return new JsonResponse([
                'success' => false,
                'message' => $e->getMessage(),
            ],
                $e->getCode());
        }

        return new JsonResponse(['success' => true]);
    }

    /**
     * @param Request $request
     * @param AuthorService $authorService
     * @return JsonResponse
     * @Route("/api/edit-author")
     */
    public function editAuthor(Request $request, AuthorService $authorService): JsonResponse
    {
        $json = $request->getContent();
        $body = json_decode($json,true);

        try {
            if (isset($body['idAuthor'])) {
                $authorService->editAuthor($body['idAuthor'], $body['name'] ?? null, $body['surname'] ?? null, $body['idBook'] ?? null);
            } else {
                throw new RuntimeException('Идентификатор автора не указан', 400);
            }
        } catch (Exception $e) {
            return new JsonResponse([
                'success' => false,
                'message' => $e->getMessage(),
            ],
            $e->getCode());
        }

        return new JsonResponse(['success' => true]);
    }

    /**
     * @param Request $request
     * @param AuthorService $authorService
     * @return JsonResponse
     * @Route("/api/remove-author")
     */
    public function removeAuthorsByNameAndSurname(Request $request, AuthorService $authorService): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        try {
            $authorService->removeAuthorsByNameAndSurname($data);
        } catch (Exception $e) {
            return new JsonResponse([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }

        return new JsonResponse(['success' => true]);
    }

    /**
     * @param Request $request
     * @param AuthorService $authorService
     * @return JsonResponse
     * @Route("/api/get-authors")
     */
    public function getAuthors(Request $request, AuthorService $authorService): JsonResponse
    {
        try {
            $json = $request->getContent();
            $body = json_decode($json, true);

            return new JsonResponse($authorService->getAuthors($body ?? []));

        } catch (Exception $e) {
            return new JsonResponse([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }
}

<?php

namespace App\Controller;

use App\Service\PublishingHouseService;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class PublishingHouseController extends AbstractController
{
    /**
     * @param Request $request
     * @param PublishingHouseService $publishingHouseService
     * @return JsonResponse
     * @Route("/api/create-pub-house")
     */
    public function createPubHouse(Request $request, PublishingHouseService $publishingHouseService): JsonResponse
    {
        $json = $request->getContent();
        $body = json_decode($json, true);

        try {
            if (isset($body['name']) && isset($body['address'])) {

                $publishingHouseService->createPubHouse($body['name'], $body['address'], $body['idBook'] ?? null);

            } else {
                throw new RuntimeException('Не указаны имя или адрес издательства');
            }
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
     * @param PublishingHouseService $publishingHouseService
     * @return JsonResponse
     * @Route("/api/edit-pub-house")
     */
    public function editPubHouse(Request $request, PublishingHouseService $publishingHouseService): JsonResponse
    {
        $json = $request->getContent();
        $body = json_decode($json,true);

        try {

            if (isset($body['idPubHouse'])) {
                $publishingHouseService->editPubHouse($body['idPubHouse'], $body['name'] ?? null, $body['address'] ?? null);
            } else {
                throw new RuntimeException('Не указан идентификатор издательства');
            }
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
     * @param PublishingHouseService $publishingHouseService
     * @return JsonResponse
     * @Route("/api/remove-pub-house")
     */
    public function removePubHouseByNameAndAddress(Request $request, PublishingHouseService $publishingHouseService): JsonResponse
    {
        $information = json_decode($request->getContent(), true);

        try {
            $publishingHouseService->removePubHouseByNameAndAddress($information);
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
     * @param PublishingHouseService $publishingHouseService
     * @return JsonResponse
     * @Route("/api/get-pub-houses")
     */
    public function getPubHouses(Request $request, PublishingHouseService $publishingHouseService): JsonResponse
    {
        try {
            $json = $request->getContent();
            $body = json_decode($json, true);

            return new JsonResponse($publishingHouseService->getPubHouses($body ?? []));

        } catch (Exception $e) {
            return new JsonResponse([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }
}

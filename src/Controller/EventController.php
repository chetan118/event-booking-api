<?php

namespace App\Controller;

use App\Dto\Event\CreateEventDto;
use App\Dto\Event\UpdateEventDto;
use App\Service\EventService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/api/events')]
class EventController extends AbstractController
{
    public function __construct(
        private EventService $eventService,
        private SerializerInterface $serializer,
        private ValidatorInterface $validator
    ) {}

    #[Route('', name: 'index', methods: ['GET'])]
    public function index(Request $request): JsonResponse
    {
        $location = $request->query->get('location');
        $from = $request->query->get('from');
        $to = $request->query->get('to');
        $page = (int) $request->query->get('page', 1);
        $limit = (int) $request->query->get('limit', 10);

        $fromDate = $from ? new \DateTimeImmutable($from) : null;
        $toDate = $to ? new \DateTimeImmutable($to) : null;

        $result = $this->eventService->listEvents(
            location: $location,
            from: $fromDate,
            to: $toDate,
            page: $page,
            limit: $limit
        );

        return $this->json($result, 200, [], ['groups' => ['event:read']]);
    }

    #[Route('', name: 'event_create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $dto = $this->serializer->deserialize($request->getContent(), CreateEventDto::class, 'json');

        $errors = $this->validator->validate($dto);
        if (count($errors) > 0) {
            return $this->json($errors, Response::HTTP_BAD_REQUEST);
        }

        $event = $this->eventService->createEvent($dto);

        return $this->json($event, Response::HTTP_CREATED, [], ['groups' => 'event:read']);
    }

    #[Route('/{id}', name: 'event_update', methods: ['PUT'])]
    public function update(int $id, Request $request): JsonResponse
    {
        $dto = $this->serializer->deserialize($request->getContent(), UpdateEventDto::class, 'json');

        $errors = $this->validator->validate($dto);
        if (count($errors) > 0) {
            return $this->json($errors, Response::HTTP_BAD_REQUEST);
        }

        $event = $this->eventService->updateEvent($id, $dto);

        return $this->json($event, Response::HTTP_OK, [], ['groups' => 'event:read']);
    }

    #[Route('/{id}', name: 'event_delete', methods: ['DELETE'])]
    public function delete(int $id): JsonResponse
    {
        $this->eventService->deleteEvent($id);

        return $this->json(['message' => 'Event deleted successfully'], Response::HTTP_NO_CONTENT);
    }
}

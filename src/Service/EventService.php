<?php

namespace App\Service;

use App\Dto\Event\CreateEventDto;
use App\Dto\Event\UpdateEventDto;
use App\Entity\Event;
use App\Repository\EventRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class EventService
{
    public function __construct(
        private EntityManagerInterface $em,
        private EventRepository $eventRepository
    ) {}

    public function listEvents(
        ?string $location = null,
        ?\DateTimeInterface $from = null,
        ?\DateTimeInterface $to = null,
        int $page = 1,
        int $limit = 10
    ): array {
        $events = $this->eventRepository->findFiltered($location, $from, $to, $page, $limit);
        $total = $this->eventRepository->countFiltered($location, $from, $to);

        return [
            'items' => $events,
            'meta' => [
                'page' => $page,
                'limit' => $limit,
                'total' => $total,
                'pages' => (int) ceil($total / $limit),
            ],
        ];
    }

    public function createEvent(CreateEventDto $dto): Event
    {
        $event = new Event();
        $event->setTitle($dto->getTitle());
        $event->setDescription($dto->getDescription());
        $event->setLocation($dto->getLocation());
        $event->setStartTime($dto->getStartTime());
        $event->setEndTime($dto->getEndTime());
        $event->setCapacity($dto->getCapacity());
        $event->setCreatedAt(new \DateTimeImmutable('now'));

        $this->em->persist($event);
        $this->em->flush();

        return $event;
    }

    public function updateEvent(int $id, UpdateEventDto $dto): Event
    {
        $event = $this->eventRepository->find($id);

        if (!$event) {
            throw new NotFoundHttpException('Event not found');
        }

        $event->setTitle($dto->getTitle());
        $event->setDescription($dto->getDescription());
        $event->setLocation($dto->getLocation());
        $event->setStartTime($dto->getStartTime());
        $event->setEndTime($dto->getEndTime());
        $event->setCapacity($dto->getCapacity());
        $event->setUpdatedAt(new \DateTimeImmutable('now'));

        $this->em->flush();

        return $event;
    }

    public function deleteEvent(int $id): void
    {
        $event = $this->eventRepository->find($id);

        if (!$event) {
            throw new NotFoundHttpException('Event not found');
        }

        $this->em->remove($event);
        $this->em->flush();
    }
}

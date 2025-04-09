<?php

namespace App\Tests\Service;

use App\DTO\Event\CreateEventDto;
use App\DTO\Event\UpdateEventDto;
use App\Entity\Event;
use App\Service\EventService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class EventServiceTest extends KernelTestCase
{
    private EntityManagerInterface $em;
    private EventService $eventService;
    private Event $createdEvent;
    private Event $event;

    protected function setUp(): void
    {
        self::bootKernel();
        $container = self::getContainer();
        $this->em = $container->get(EntityManagerInterface::class);
        $this->eventService = $container->get(EventService::class);
        $this->em->beginTransaction();
    }

    protected function tearDown(): void
    {
        $this->em->rollback();
        parent::tearDown();
    }

    public function testCreateEvent(): void
    {
        $this->when_create_event_is_called([
            'Symfony Conference',
            'A Tech Conference',
            'India',
            new \DateTimeImmutable('+1 day'),
            new \DateTimeImmutable('+2 days'),
            100,
        ]);
        $this->then_event_has_data('Symfony Conference', 100);
    }

    public function testUpdateEvent(): void
    {
        $this->given_an_event([
            'Symfony Conference',
            'A Tech Conference',
            'India',
            new \DateTimeImmutable('+1 day'),
            new \DateTimeImmutable('+2 days'),
            100,
        ]);
        $this->when_update_event_is_called($this->createdEvent->getId(), [
            'Laravel Conference',
            'A Tech Conference',
            'India',
            new \DateTimeImmutable('+1 day'),
            new \DateTimeImmutable('+2 days'),
            150,
        ]);
        $this->then_event_has_data('Laravel Conference', 150);
    }

    public function testDeleteEvent(): void
    {
        $this->given_an_event([
            'Symfony Conference',
            'A Tech Conference',
            'India',
            new \DateTimeImmutable('+1 day'),
            new \DateTimeImmutable('+2 days'),
            100,
        ]);
        $id = $this->createdEvent->getId();
        $this->when_delete_event_is_called($id);
        $this->then_event_is_deleted($id);
    }

    private function given_an_event(array $eventData)
    {
        $this->createdEvent = $this->eventService->createEvent(new CreateEventDto(...$eventData));
    }

    private function when_create_event_is_called(array $eventData)
    {
        $this->given_an_event($eventData);
        $this->event = $this->createdEvent;
    }

    private function when_update_event_is_called(int $id, array $eventData)
    {
        $updateDto = new UpdateEventDto(...$eventData);
        $this->event = $this->eventService->updateEvent($id, $updateDto);
    }

    private function when_delete_event_is_called(int $id)
    {
        $this->eventService->deleteEvent($id);
    }

    private function then_event_has_data(string $title, int $capacity)
    {
        $this->assertInstanceOf(Event::class, $this->event);
        $this->assertSame($title, $this->event->getTitle());
        $this->assertSame($capacity, $this->event->getCapacity());
    }

    private function then_event_is_deleted(int $id)
    {
        $entityManager = static::getContainer()->get('doctrine')->getManager();
        $deleted = $entityManager->getRepository(Event::class)->find($id);

        $this->assertNull($deleted, 'Event should be deleted from DB');
    }
}

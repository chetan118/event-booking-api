<?php

namespace App\Tests\Repository;

use App\DTO\Event\CreateEventDto;
use App\Repository\EventRepository;
use App\Service\EventService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class EventRepositoryTest extends KernelTestCase
{
    private EntityManagerInterface $em;
    private EventService $eventService;
    private EventRepository $eventRepository;
    private array $results = [];

    protected function setUp(): void
    {
        self::bootKernel();
        $container = self::getContainer();
        $this->em = $container->get(EntityManagerInterface::class);
        $this->eventService = $container->get(EventService::class);
        $this->eventRepository = $container->get(EventRepository::class);
        $this->em->beginTransaction();
    }

    protected function tearDown(): void
    {
        $this->em->rollback();
        parent::tearDown();
    }

    public function testFindFiltered(): void
    {
        $this->given_a_set_of_existing_events([
            [
                'Symfony Conference',
                'A Tech Conference',
                'India',
                new \DateTimeImmutable('+1 day'),
                new \DateTimeImmutable('+2 days'),
                100,
            ],
            [
                'Laravel Conference',
                'A Tech Conference',
                'United States of America',
                new \DateTimeImmutable('+1 day'),
                new \DateTimeImmutable('+2 days'),
                100,
            ],
            [
                'NodeJS Conference',
                'A Tech Conference',
                'India',
                new \DateTimeImmutable('+1 day'),
                new \DateTimeImmutable('+2 days'),
                100,
            ],
        ]);
        $this->when_find_filtered_method_is_called(['India', null, null, 1, 10]);
        $this->then_events_are_fetched(['Symfony Conference', 'NodeJS Conference']);
    }

    private function given_a_set_of_existing_events(array $eventsData) 
    {
        foreach ($eventsData as $eventData) {
            $this->eventService->createEvent(new CreateEventDto(...$eventData));
        }
    }

    private function when_find_filtered_method_is_called(array $findFilteredArgs)
    {
        $this->results = $this->eventRepository->findFiltered(...$findFilteredArgs);
    }

    private function then_events_are_fetched(array $expectedEvents)
    {
        $this->assertCount(count($expectedEvents), $this->results);
        $this->assertEquals($expectedEvents[0], $this->results[0]->getTitle());
        $this->assertEquals($expectedEvents[1], $this->results[1]->getTitle());
    }
}

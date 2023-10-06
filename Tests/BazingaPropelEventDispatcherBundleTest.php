<?php

namespace Bazinga\Bundle\PropelEventDispatcherBundle\Tests;

use Bazinga\Bundle\PropelEventDispatcherBundle\Tests\Fixtures\Model\MyObject;
use Bazinga\Bundle\PropelEventDispatcherBundle\Tests\Fixtures\Model\MyObject3;
use Bazinga\Bundle\PropelEventDispatcherBundle\Tests\Fixtures\EventListener\MyEventSubscriber;
use Bazinga\Bundle\PropelEventDispatcherBundle\Tests\Fixtures\Model\MyObject2;
use Bazinga\Bundle\PropelEventDispatcherBundle\Tests\Fixtures\EventListener\MyEventListener;
use Bazinga\Bundle\PropelEventDispatcherBundle\Tests\Fixtures\TestKernel;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpKernel\Kernel;

class BazingaPropelEventDispatcherBundleTest extends WebTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->deleteTmpDir();
        self::bootKernel();
    }

    protected static function getKernelClass(): string
    {
        return TestKernel::class;
    }

    private function deleteTmpDir(): void
    {
        $dir = sys_get_temp_dir().'/'.Kernel::VERSION;
        if (file_exists($dir)) {
            $fs = new Filesystem();
            $fs->remove($dir);
        }
    }

    public function testGetListener(): void
    {
        $listener = self::getContainer()->get('listener.my_event_listener');

        $this->assertNotNull($listener);
        $this->assertInstanceOf(MyEventListener::class, $listener);
    }

    public function testGetListenerWithNonExistentClass(): void
    {
        $this->assertFalse(class_exists(MyObject2::class, false));

        $listener = self::getContainer()->get('listener.my_event_listener_2');

        $this->assertNotNull($listener);
        $this->assertInstanceOf(MyEventListener::class, $listener);
    }

    public function testGetListenerWithNonExistentParentClass(): void
    {
        $this->assertFalse(class_exists(MyObject2::class, false));

        self::getContainer()->get('listener.my_event_listener_3');
    }

    public function testFireEvent(): void
    {
        $object   = new MyObject();
        $listener = self::getContainer()->get('listener.my_event_listener');

        $this->assertCount(0, $listener->getEvents());

        $object->preSave();
        $this->assertCount(1, $listener->getEvents());

        $events  = $listener->getEvents();
        $subject = $events[0]->getSubject();
        $this->assertSame($object, $subject);
    }

    public function testFireEventWithEarlyBoot(): void
    {
        $listener = self::getContainer()->get('listener.my_event_listener_4');
        $object   = new MyObject3();

        $this->assertCount(0, $listener->getEvents());

        $object->preSave();
        $this->assertCount(1, $listener->getEvents());

        $events  = $listener->getEvents();
        $subject = $events[0]->getSubject();
        $this->assertSame($object, $subject);
    }

    public function testSubscriber(): void
    {
        $subscriber = self::getContainer()->get('subscriber.my_subscriber_1');

        $this->assertNotNull($subscriber);
        $this->assertInstanceOf(MyEventSubscriber::class, $subscriber);
    }

    public function testFireEventWithSubscriber(): void
    {
        $object     = new MyObject3();
        $subscriber = self::getContainer()->get('subscriber.my_subscriber_1');

        $this->assertCount(0, $subscriber->getEvents());

        $object->preInsert();
        $object->preSave();
        $this->assertCount(1, $subscriber->getEvents());

        $events  = $subscriber->getEvents();
        $subject = $events[0]->getSubject();
        $this->assertEquals('pre_insert', $subject->source);
        $this->assertSame($object, $subject);
    }
}

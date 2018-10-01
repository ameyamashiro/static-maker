<?php
namespace Static_Maker;

class Static_MakerTest extends \WP_UnitTestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->class_instance = new Static_Maker_Class();
    }

    public function test_hogemoge()
    {
        $queue_action = new Queue_Actions();

        $queue_action->dequeue_task();

        $this->assertTrue(true);
    }
}

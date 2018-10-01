<?php
declare (strict_types = 1);

use PHPUnit\Framework\TestCase;

final class SampleTest extends TestCase
{

    public function __construct()
    {
        parent::__construct();
        $this->factory = new WP_UnitTest_Factory();
    }

    public function testSample()
    {
        $this->factory->post->create(['post_title' => 'My Title']);

        $query = new WP_Query([
            's' => 'My Title',
        ]);

        $posts = $query->query('');

        $this->assertSame(count($posts), 1);
    }
}

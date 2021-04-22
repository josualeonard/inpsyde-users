<?php declare(strict_types=1); // phpcs:disable Generic.Files.LineEndings.InvalidEOLChar

namespace Sample;

use PHPUnit\Framework\TestCase;

final class SampleTest extends TestCase
{
    public function testAsertTrue(): void
    {
        $this->assertTrue(true);
    }
}

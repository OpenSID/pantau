<?php

namespace Tests\Unit;

use Tests\TestCase;

class DaterangepickerRangeTest extends TestCase
{
    public function test_returns_ranges_array()
    {
        $result = daterangepicker_range();

        $this->assertIsArray($result);
        $this->assertArrayHasKey('ranges', $result);
        $this->assertArrayHasKey('Hari Ini', $result['ranges']);
        $this->assertCount(2, $result['ranges']['Hari Ini']);
    }

    public function test_returns_specific_key()
    {
        $range = daterangepicker_range('Semua Tanggal');

        $this->assertIsArray($range);
        $this->assertEquals('1970-01-01', $range[0]);
        $this->assertMatchesRegularExpression('/^\d{4}-\d{2}-\d{2}$/', $range[1]);
    }
}

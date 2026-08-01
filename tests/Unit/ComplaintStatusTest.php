<?php

namespace Tests\Unit;

use App\Support\ComplaintStatus;
use PHPUnit\Framework\TestCase;

class ComplaintStatusTest extends TestCase
{
    public function test_open_and_closed_detection(): void
    {
        $this->assertFalse(ComplaintStatus::isClosed('PN'));
        $this->assertFalse(ComplaintStatus::isClosed('HL'));
        $this->assertTrue(ComplaintStatus::isClosed('CM'));
        $this->assertTrue(ComplaintStatus::isClosed('CL'));
    }

    public function test_apply_filter_supports_open_and_closed_groups(): void
    {
        $this->assertTrue(ComplaintStatus::isValidFilter('open'));
        $this->assertTrue(ComplaintStatus::isValidFilter('PN'));
        $this->assertFalse(ComplaintStatus::isValidFilter('invalid'));
    }

    public function test_filter_options_include_group_and_individual_statuses(): void
    {
        $options = ComplaintStatus::filterOptions();

        $this->assertArrayHasKey('', $options);
        $this->assertArrayHasKey('open', $options);
        $this->assertArrayHasKey('closed', $options);
        $this->assertArrayHasKey('PN', $options);
    }
}

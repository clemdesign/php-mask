<?php
/**
 * Test Module for Mask Library
 * User: Clem
 * Date: 26/05/2019
 * Time: 13:48
 */
declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Clemdesign\PhpMask\Mask;

final class MaskTest extends TestCase
{
  // Pattern tests
  public function testMaskUsingThePattern0(): void {
    // Basic
    $this->assertEquals(
      "4 1 5",
      Mask::apply("415", "0 0 0")
    );
    // Complex
    $this->assertEquals(
      "4 1 5",
      Mask::apply("4ab1-5bh", "0 0 0")
    );
  }

}

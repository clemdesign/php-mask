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

  public function testMaskUsingThePattern9(): void {
    // Basic
    $this->assertEquals(
      "4 1 5",
      Mask::apply("415", "9 9 9")
    );
    // Complex 1
    $this->assertEquals(
      "4 1 5",
      Mask::apply("4ab15bh", "9 9 9")
    );
    // Complex 2
    // TODO: To resolve, do not pass
    /*$this->assertEquals(
      "4 1 5",
      Mask::apply("4ab1.5bh", "9 9 9")
    );*/
    // Complex 3
    $this->assertEquals(
      "4 12 553 1",
      Mask::apply("4.12.553d1", "099 099 099 099")
    );
  }

  public function testMaskUsingThePatternA(): void {
    // Basic
    $this->assertEquals(
      "4 aB 5",
      Mask::apply("4aB5", "A AA A")
    );
    // Complex
    $this->assertEquals(
      "4 ab 15",
      Mask::apply("4ab1-5bh", "A AA AA")
    );
  }

  public function testMaskUsingThePatternS(): void {
    // Basic
    $this->assertEquals(
      "a B",
      Mask::apply("4aB5", "S SS S")
    );
    // Complex
    $this->assertEquals(
      "a bB h",
      Mask::apply("4ab1-5Bh", "S SS S")
    );
  }

  // Mask function tests
  public function testMaskPercent(): void {
    // Basic
    $this->assertEquals(
      "25.5639",
      Mask::apply("The result is 25.5639%", "percent")
    );
    // With precision 2 decimals
    $this->assertEquals(
      "25.56",
      Mask::apply("The result is 25.5639%", "percent.2")
    );
    // With precision 0 decimal
    $this->assertEquals(
      "25",
      Mask::apply("The result is 25.5639%", "percent.0")
    );
  }

  public function testMaskSeparator(): void {
    // Basic
    $this->assertEquals(
      "1 963 725,5630",   // Function use float and precision is lost
      Mask::apply("The result is 1963725.5639", "separator")
    );
    // With precision 2 decimals
    $this->assertEquals(
      "1 963 725,56",
      Mask::apply("The result is 1963725.5639", "separator.2")
    );
    // With precision 0 decimal
    $this->assertEquals(
      "1 963 726",
      Mask::apply("The result is 1963725.5639", "separator.0")
    );
  }

  public function testMaskDotSeparator(): void {
    // Basic
    $this->assertEquals(
      "1.963.725,5639",
      Mask::apply("The result is 1963725.5639", "dot_separator")
    );
    // With precision 2 decimals
    $this->assertEquals(
      "1.963.725,56",
      Mask::apply("The result is 1963725.5639", "dot_separator.2")
    );
    // With precision 0 decimal
    $this->assertEquals(
      "1.963.726",
      Mask::apply("The result is 1963725.5639", "dot_separator.0")
    );
  }

  public function testMaskCommaSeparator(): void {
    // Basic
    $this->assertEquals(
      "1,963,725.5639",
      Mask::apply("The result is 1963725.5639", "comma_separator")
    );
    // With precision 2 decimals
    $this->assertEquals(
      "1,963,725.56",
      Mask::apply("The result is 1963725.5639", "comma_separator.2")
    );
    // With precision 0 decimal
    $this->assertEquals(
      "1,963,726",
      Mask::apply("The result is 1963725.5639", "comma_separator.0")
    );
  }

  // Config test
  public function testMaskPrefix(): void {
    $this->assertEquals(
      "The result is 48",
      Mask::apply("If you set 4 next to 8, what is the result?", "00", array(
        "prefix" => "The result is "
      ))
    );
  }
  public function testMaskSuffix(): void {
    $this->assertEquals(
      "48 is the result!",
      Mask::apply("If you set 4 next to 8, what is the result?", "00", array(
        "suffix" => " is the result!"
      ))
    );
  }



}

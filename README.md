# php-mask

PHP Mask is a PHP library to make masks for a string.  
It is an adapation of [ngx-mask](https://github.com/JsDaddy/ngx-mask) by  [JsDaddy](https://github.com/JsDaddy).

## Installation

To do.

## Usage

In your PHP application, use the static method `apply` of Mask:

```php

$output = \Clemdesign\PhpMask\Mask::apply($inputValue, $maskExpression, $config);

```

### Arguments

- `$inputValue`: string - The input value to apply mask.
- `$maskExpression`: string - The mask expression for $output.
- `$config`: array - The configuration for operation

#### $maskExpression: Patterns

The patterns are used to filter $inputValue:

  | code  | meaning                                     |
  | ----- | ------------------------------------------- |
  | **0** | digits (like 0 to 9 numbers)                |
  | **9** | digits (like 0 to 9 numbers), but optional  |
  | **A** | letters (uppercase or lowercase) and digits |
  | **S** | only letters (uppercase or lowercase)       |


#### $maskExpression: Special chars

Special chars are used in mask expressions to format output:

   | character |
   |-----------|
   | / |
   | ( |
   | ) |
   | . |
   | : |
   | - |
   | **space** |
   | + |
   | , |
   | @ |


#### $maskExpression: Thousand separator

You can format a number in thousand separator and control precision.

The mask keys are:
- `separator`: Input `1234.56` is ouputed as `1 234.56`
- `dot_separator`: Input `1234,56` is ouputed as `1.234,56`
- `comma_separator`: Input `1234.56` is ouputed as `1,234.56`

To manage precision, keys shall be suffixed by `.{Number}`.

Example:
- `separator.1`: Input `1234.56743` is ouputed as `1 234.5`
- `dot_separator.4`: Input `1234,56743` is ouputed as `1.234,5674`
- `comma_separator.2`: Input `1234.56743` is ouputed as `1,234.56`

#### $maskExpression: Time validation

You can format a time according limit:

  | Mask  | meaning                                     |
  | ----- | ------------------------------------------- |
  | **H** | Input value shall be inside 0 and 2.        |
  | **h** | Input value shall be inside 0 and 3.        |
  | **m** | Input value shall be inside 0 and 5.        |
  | **s** | Input value shall be inside 0 and 5.        |

#### $maskExpression: Percent validation

You can format a value from `$inputValue` as a percent and manage the precision.

Use the key `percent` to have a extract value from `$inputValue` within 0 to 100.

Suffix the key with `.{Number}` to manage precision (`percent.2`).

Example:

```php

$output = \Clemdesign\PhpMask\Mask::apply("99.4125", "percent.2");

// $output contains: 99.41

```

#### $config: Prefix and suffix

You have possibility to set suffix and prefix in output:

```php

$output = \Clemdesign\PhpMask\Mask::apply("0102030405", "00 00 00 00 00", array(
  "prefix" => "My phone is ",
  "suffix" => "!"
));

// $output contains: My phone is 01 02 03 04 05!

```

## Examples

| Input               | Mask           | Output           |
| ------------------- | -------------- | ---------------- |
| Date is 20190526    | 9999-99-99     | 2019-05-26       |
| Month is 20190526   | 0*.00          | 2019.05          |
| 04845798798         | 000.000.000-99 | 048.457.987-98   |
| 048457987           | 000.000.000-99 | 048.457.987-     |
| 0F6.g-lm            | AAAA           | 0F6g             |
| a036s.D2F           | SSSS           | asDF             |


## Contributing

If you think any implementation are just not the best, feel free to submit ideas and pull requests. All your comments and suggestion are welcome.

## Credits:

- [ngx-mask](https://github.com/JsDaddy/ngx-mask) of  [JsDaddy](https://github.com/JsDaddy)

## License

Copyright (c) 2019 [clemdesign](https://github.com/clemdesign/).

For use under the terms of the [MIT](http://www.opensource.org/licenses/mit-license.php) license. 

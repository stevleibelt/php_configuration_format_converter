# php_configuration_format_converter 

A cli tool to convert files from and to yaml, json, xml or php array

## Install

Install it via: 
    * `git clone `[https://github.com/stevleibelt/php_configuration_format_converter](https://github.com/stevleibelt/php_configuration_format_converter)
    * composer and [packagist.org](https://packagist.org/packages/net_bazzline/php_configuration_format_converter) `"net_bazzline/php_configuration_format_converter": "dev-master"`

## Usage

Simple switch to the directory via console and typ `bin/net_bazzline_configuration_format_converter.php`.  

This will display the help screen. To start converting stuff, add source and destination behind the command `convert` like `bin/net_bazzline_configuration_format_converter.php convert my/source/file.yaml my/destination/file.php`.  

The tool decides on the given file extension how he has to handle the conversation. File extensions can be uppercase, lowercase, whatevercase.  

## Supported formats

Right now, the following formares are supported:
    * YAML
    * XML
    * JSON
    * php array

## Examples

Take a look to the directory [examples](https://github.com/stevleibelt/php_configuration_format_converter/tree/master/example) to just start playing arround.

## Notes

The converter will stop working if destination file already exists.  
If you want to overwrite that file, you have to add the option `--force`.

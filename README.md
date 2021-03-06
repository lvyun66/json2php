# json2php

## Introduction
Generate php files(PHP class) based on json

## Installation
Via `Composer`:
```shell
composer require nofuck/json2php "*"
```

## Usage
Usage is very easy. Can easily generate a PHP file:
```php
$namespace = 'tests\\output\\general';
$output = TEST_ROOT . '/output/general/';
if (!is_dir($output)) {
    mkdir($output);
}

$g = new GeneratorPhpFile();
$jsonPath = TEST_ROOT . '/json/normal.json';
$g->generator(file_get_contents($jsonPath), 'Normal', $namespace, $output);
```

If you want to generate a model of Yii2:
```php
$namespace = 'tests\\output\\yii2';
$output = TEST_ROOT . '/output/yii2/';
if (!is_dir($output)) {
    mkdir($output);
}

$g = new GeneratorYiiModel();
$jsonPath = TEST_ROOT . '/json/normal.json';
$g->generator(file_get_contents($jsonPath), 'Normal', $namespace, $output);
```

It’s that simple.

`generator` Method accepts four parameters:
- json 
- className
- namespace
- phpOut

### Console usage
```shell
12:09:05 › ./json2php -h
Description:
  Lists commands

Usage:
  list [options] [--] [<namespace>]

Arguments:
  namespace            The namespace name

Options:
      --raw            To output raw command list
      --format=FORMAT  The output format (txt, xml, json, or md) [default: "txt"]

Help:
  The list command lists all commands:

    php ./json2php list

  You can also display the commands for a specific namespace:

    php ./json2php list test

  You can also output the information in other formats by using the --format option:

    php ./json2php list --format=xml

  It's also possible to get raw list of commands (useful for embedding command runner):

    php ./json2php list --raw
```

There are currently two generators available, one for generating generic PHP files and the other for generating files in the Yii2 model form:
```bash
generator:default  [g:d] Generate a generic php object file containing properties, get and set methods
generator:yii2     [g:yii2] Generate a generic php object file containing properties, get and set methods
```

```bash
12:12:14 › ./json2php g:d -h
Description:
  Generate a generic php object file containing properties, get and set methods

Usage:
  generator:default [options] [--] <json>
  g:d

Arguments:
  json                           Json file path or json string

Options:
  -t, --type[=TYPE]              Json source, file or json string, value in [file | string] [default: "file"]
  -c, --class_name[=CLASS_NAME]  Php class name, the default value is Default [default: "Generator1536207140"]
  -o, --php_out[=PHP_OUT]        Generate php file storage path, default to current directory [default: "./"]
  -h, --help                     Display this help message
  -q, --quiet                    Do not output any message
  -V, --version                  Display this application version
      --ansi                     Force ANSI output
      --no-ansi                  Disable ANSI output
  -n, --no-interaction           Do not ask any interactive question
  -ns, --namespace[=NAMESPACE]   Php namespace, please ensure the correctness of the input namespace
  -v|vv|vvv, --verbose           Increase the verbosity of messages: 1 for normal output, 2 for more verbose output and 3 for debug

Help:
  This command can generate a generic php object
```

```bash
./json2php g:d -t string '{"name": "lvyun"}' --namespace "test\\lvyun" -c UserInfo -o .
```

Output`UserInfo.php`:
```php
<?php

/**
 * Code generated by json2php. DO NOT EDIT.
 * Generator at 2018-09-06 04:15:27
 */

namespace test\lvyun;

class UserInfo
{
    /** @var string */
    private $name;

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param $name string
     */
    public function setName($name)
    {
        $this->name = $name;
    }
}

```
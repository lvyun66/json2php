<?php

namespace Json2php;

use Nette\PhpGenerator\PhpFile;
use Nette\PhpGenerator\PsrPrinter;
use Screw\Str;

class GeneratorYiiModel
{
    public static function generator(string $json, $className = null, $namespace = null, $output = null):string
    {
        $className = $className ? ucwords($className) : 'Generator_' . time();
        $output = $output ?: dirname('./');
        $file = new PhpFile();
        $file->addComment('Code generated by json2php. DO NOT EDIT.');
        $file->addComment('Generator at ' . date('Y-m-d H:i:s', time()));
        if ($namespace) {
            $class = $file->addNamespace($namespace)->addClass($className);
        } else {
            $class = $file->addClass($className);
        }
        $class->addExtend('\yii\base\Model');

        $obj = json_decode($json);

        $attrs = [];
        foreach ($obj as $property => $value) {
            $type = gettype($value);
            if (is_object($value)) {
                $type = self::generator(json_encode($value), $property, $namespace, $output);
            }
            if (is_array($value)) {
                foreach ($value as $k => $v) {
                    if (is_object($v)) {
                        $type = self::generator(json_encode($value), $property, $namespace, $output);
                    } else {
                        $type = gettype($v) . '[]';
                    }
                    break;
                }
            }
            $lower = Str::toLowerCamelCase($property);
            $upper = Str::toUpperCamelCase($property);
            $getter = 'get' . $upper;
            $setter = 'set' . $upper;

            $class->addProperty($lower)
                ->setVisibility('private')
                ->addComment("@var {$type}");
            $class->addMethod($getter)
                ->addBody("return \$this->{$lower};")
                ->addComment("@return {$type}");
            $class->addMethod($setter)
                ->setBody("\$this->{$lower} = \${$lower};")
                ->addComment("@param \${$lower} {$type}")
                ->addParameter($lower);
            $attrs[$lower] = $type;
        }

        // generator yii2 model rules
        $newAttrs = [];
        foreach ($attrs as $attr => $aType) {
            $newAttrs[$aType][] = $attr;
        }
        $rules = [];
        $callback = function ($kv) {
            return "'{$kv}'";
        };
        foreach ($newAttrs as $k => $v) {
            $v = array_map($callback, $v);
            $s = rtrim(implode(', ', $v), ', ');
            $rules[] = "[[$s], '{$k}']";
        }
        $ruleString = implode(",\n\t", $rules);
        $ruleReturn = <<<txt
return [
    {$ruleString}
];
txt;
        $class->addMethod('rules')
            ->setVisibility('public')
            ->setBody($ruleReturn);

        // generator attribute labels method
        $labels = [];
        foreach (array_keys($attrs) as $k) {
            $chinese = str_replace('_', ' ', Str::toSnakeCase($k));
            $labels[] = "'{$k}' => '{$chinese}'";
        }
        $labelString = implode(",\n\t", $labels);
        $labelReturn = <<<txt
return [
    {$labelString}
];
txt;
        $class->addMethod('attributeLabels')
            ->setVisibility('public')
            ->setBody($labelReturn);

        file_put_contents($output . $className . '.php', (new PsrPrinter())->printFile($file));

        return ucwords($className);
    }
}
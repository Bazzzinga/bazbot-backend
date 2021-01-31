<?php


namespace app\models\Settings\Blocks;


use yii\helpers\ArrayHelper;

class BlockBase
{
    private $data;

    public function fields()
    {
        return [];
    }

    public function init($settings)
    {
        $fields = $this->fields();

        foreach ($fields as $field) {
            if (count($field) == 0) {
                continue;
            } else if (count($field) == 1) {
                continue;
            } else  if (count($field) <= 3) {
                $names = $field[0];
                $type  = $field[1];

                $isSimple = true;
                $isMap = false;
                $value = null;
                switch ($type) {
                    case 'string':
                        $value = "";
                        break;
                    case 'bool':
                        $value = false;
                        break;
                    case 'array':
                        $value = [];
                        break;
                    case 'integer':
                    case 'int':
                        $value = 0;
                        break;
                    case 'collection':
                        $isSimple = false;
                        break;
                }

                if (count($field) == 3) {
                    $value = $field[2];
                }

                if (is_iterable($names)) {
                    foreach ($names as $name) {
                        $this->processNewName($settings, $name, $value, $isSimple);
                    }
                } else {
                    $this->processNewName($settings, $names, $value, $isSimple);
                }
            }
        }
    }

    public function __set($name, $value)
    {
        $this->newData($name, $value);
    }

    public function __get($name)
    {
        if (ArrayHelper::keyExists($name, $this->data)) {
            return $this->data[$name];
        }
        return null;
    }

    private function processNewName($settings, $name, $value, $isSimple)
    {
        if ($isSimple) {
            $this->newData($name, Helper::simpleField($settings, $name, $value));
        } else {
            if ($value) {
                $this->newData($name, Helper::classCollectionField($settings, $name, $value));
            }
        }
    }

    private function newData($name, $value)
    {
        $this->data[$name] = $value;
    }
}
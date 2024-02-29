<?php
namespace Cognesy\Instructor\Reflection\ParameterData;

use Cognesy\Instructor\Schema\FCEnum;
use Cognesy\Instructor\Reflection\Enums\PhpType;
use Cognesy\Instructor\Reflection\TypeDefs\TypeDef;
use Cognesy\Instructor\Reflection\Utils\DescriptionUtils;
use Cognesy\Instructor\Reflection\Utils\ReflectionUtils;
use Exception;
use ReflectionEnum;
use ReflectionParameter;

class EnumParameterData extends ParameterData {
    public array $values = [];

    protected function getParameterData(ReflectionParameter $parameter) : void {
        parent::getParameterData($parameter);
        $this->name = $parameter->getName();
        $this->description = DescriptionUtils::getParameterDescription($parameter);
        $type = $parameter->getType();
        if (!$type) {
            throw new Exception("Parameter type is not defined: {$this->name}");
        }
        $enum = new ReflectionEnum($type->getName());
        $this->values = ReflectionUtils::getEnumValues($enum);
    }

    public function toStruct() : FCEnum {
        $fcEnum = new FCEnum();
        $fcEnum->name = $this->name;
        $fcEnum->description = $this->description;
        $fcEnum->values = $this->values;
        return $fcEnum;
    }

    public static function asArrayItem(TypeDef $typeDef) : EnumParameterData {
        $itemType = new EnumParameterData(null);
        $itemType->name = 'items';
        $itemType->type = PhpType::ENUM;
        $itemType->values = $typeDef->values;
        return $itemType;
    }
}
<?php

namespace Tryv\PhpJsonBotInterpreter;

use ReflectionClass;
use RuntimeException;
use SplFixedArray;
use TypeError;

abstract class Prototype implements \JsonSerializable
{
	private static array $module_to_classname = [];
	private static array $global_context = [];
	private static array $runtime_vars = [];

	private static array $aliases = [
		'Update.Message' => 'Evaluator.Update',
	];

	public function jsonSerialize()
	{
		$class_names_to_module = array_flip(self::$module_to_classname);
		return [
			'_' => $class_names_to_module[static::class],
			...get_object_vars($this)
		];
	}

	protected static function register(string $module_name, string $class_name): void
	{
		if (class_exists($class_name) === false) $class_name = (__NAMESPACE__) . '\\' . $class_name;

		$ref = new ReflectionClass($class_name);
		$parent = ($ref->getParentClass() ?: null)?->getName();
		if (self::class !== $parent) throw new RuntimeException(
			"trying to register module '$module_name' for '$class_name' which does not extend " . self::class
		);

		// var_dump($module_name . ' is ' . $class_name . ' now');
		self::$module_to_classname[$module_name] = $class_name;
	}


	/*public static function registerArr(array|string $module_name, string $class_name = null): void
	{
		if (is_array($module_name)) {
			foreach ($module_name as $key => $val) {
				parent::register($key, $val);
			}
		}
		elseif ($class_name !== null) {
			parent::register($module_name, $class_name);
		}
	}*/

	final public static function registerDir(array|string $path, array|string $prefixes = []): void
	{
		$paths = is_string($path) ? [$path] : $path;
		$prefixes = is_string($prefixes) ? [$prefixes] : $prefixes;

		foreach ($paths as $p) {
			$real_p = rtrim($p, '/');
			if (!is_dir($real_p)) continue;

			foreach (glob($real_p . '/*') as $fp) {
				$path_info = pathinfo($fp);
				if (is_dir($fp)) {
					self::registerDir($fp, [...$prefixes, $path_info['basename']]);
				}
				else {
					self::register(
						implode('.', $prefixes) . '.' . $path_info['filename'],
						implode('\\', $prefixes) . '\\' . $path_info['filename'],
					);
				}
			}
		}
	}

	final public static function getModules(&$js = null): array
	{
		$js = '[';
		foreach (self::$module_to_classname as $type => $class) {
			$vars = implode(", ", array_keys(get_class_vars($class)));
			$js .= <<<EOL

			{
				type: '{$type}', // {$vars}
				message0: "",
				message1: "%1",
				args1: [
					{
						type: "input_statement",
						name: "body",
						check: "Array",
					}
				],
				colour: 160,
			},
		EOL;
		}
		$js .= ',{}]' . "\n";

		return (self::$module_to_classname);
	}

	protected static function from(object $obj): self
	{
		// check if type identifier exists on object
		if (!property_exists($obj, '_')) throw new RuntimeException('object does not have type indentifier');

		// if (static::class !== Prototype::class && $obj->_ !== static::class) throw new BadFunctionCallException(static::class . "::from({'_': {$obj->_}, ...)");

		// check if class exists
		$class_name = self::$module_to_classname[$obj->_] ?? self::$module_to_classname[self::$aliases[$obj->_] ?? null] ?? null;
		if (!class_exists($class_name)) throw new RuntimeException("type '{$obj->_}' does not point to a valid registered module");

		// instantiate the object
		$ref = new $class_name;

		// check if $ref extends Prototype
		if (!($ref instanceof self)) throw new RuntimeException("class '$class_name' is not ". static::class);

		// retrive fields
		$required_field_names = ($ref::class)::getRequiredFieldNames();
		$object_vars = get_object_vars($obj);

		foreach ($required_field_names as $key) {
			// check if required fields are exists
			if (false===isset($object_vars[$key])) throw new RuntimeException("field '$key' does not exist on object");
		}

		// iterate over object fields and see if any of them needs to be unserialized
		foreach ($object_vars as $key => $value) {
			// skip the type identifier key
			if ($key === '_') continue;

			// check if field name is valid
			if (false === in_array($key, $required_field_names, true) && false === ($ref::class)::isExpectedFieldName($key)) throw new RuntimeException("field '$key' does not exist on $class_name");

            // handle single element arrays
            if (is_object($value) && ($ref::class)::isFieldArray($key)) {
                $value = [$value];
            }

			// is the field supposed to be an array?
			$type_validator = null;
			if (is_array($value) && ($ref::class)::isFieldArray($key, $type_validator))
			{
				// assign a \SplFixedArray to $ref's fields
				$ref->$key = new SplFixedArray(count($value));

				// iterate over deserialized array's fields
				foreach ($value as $arr_i => $arr_v) {
					// if the array's item is \Prototype then call Prototype::from() to build corresponding type
					// NOTE: doing this allows better type checking in $type_validator
					if (is_object($arr_v) && property_exists($arr_v, '_')) $arr_v = self::from($arr_v);

					// if there was a $type_validator supplied, vlidate the type!
					if ($type_validator && !$type_validator($arr_v)) throw new TypeError("(\$obj as $class_name)->{$key}[$arr_i] is of invalid type: " . get_debug_type($arr_v));

					// finally push the built object into the $ref->$key's array
					$ref->$key[$arr_i] = $arr_v;
				}
				continue;
			}

			// object field
			if (is_object($value) && property_exists($value, '_')) {
				$ref->$key = self::from($value);
				continue;
			}

			// anything else is also allowed
			if (
				is_integer($value) ||
				is_float($value) ||
				is_bool($value) ||
				is_null($value) ||
				// is_array($value) ||
				// $value instanceof stdClass ||
				is_string($value)
			) {
				$ref->$key = $value;
				continue;
			}

			// normally it should not reach this point
			throw new TypeError("(\$obj as $class_name)->{$key} has unexpected type of: ".get_debug_type($value));
		}

		return $ref;
	}

	// protected function __get(string $name) {
	// 	return $this->$name ?? null;
	// }

	protected static function isExpectedFieldName(string $name): bool {
		return true;
	}

	protected static function getRequiredFieldNames(): array {
		return [];
	}

	/** is the field supposed to be an array of specific type? */
	protected static function isFieldArray(string $name, ?\Closure &$type_checker = null): bool
	{
		return false;
	}

	protected static function setContextValue(string $key, $value): void
	{
		self::$global_context[$key] = $value;
	}

	protected static function getContextValue(string $key, $fallback = null): mixed
	{
		return array_key_exists($key, self::$global_context) ? self::$global_context[$key] : (is_callable($fallback) ? ($fallback)() : $fallback);
	}

	protected static function resetRuntimeVars(): void
	{
		self::$runtime_vars = [];
	}

	protected static function setRuntimeVar(string $key, $value): void
	{
		self::$runtime_vars[$key] = $value;
	}

	protected static function getRuntimeVar(string $key, $fallback = null): mixed
	{
		return array_key_exists($key, self::$runtime_vars) ? self::$runtime_vars[$key] : (is_callable($fallback) ? ($fallback)() : $fallback);
	}

	protected static function array_get_by_key(array|object $array, string $key, $fallback = null) {
		if (strpos($key, '.') === false) {
			$index = $key;
			$key = null;
		}
		else {
			list($index, $key) = explode('.', $key, 2);
		}

		switch (gettype($array)) {
			case 'array':
				if (!array_key_exists($index, $array)) return is_callable($fallback) ? $fallback() : $fallback;
				if ($key !== null && strlen($key) > 0) return self::array_get_by_key($array[$index], $key, $fallback);
				return $array[$index];
				break;

			case 'object':
				if (!property_exists($array, $index)) return is_callable($fallback) ? $fallback() : $fallback;
				if ($key !== null && strlen($key) > 0) return self::array_get_by_key($array->$index, $key, $fallback);
				return $array->$index;
				break;
		}

		return is_callable($fallback) ? $fallback() : $fallback;
	}

}


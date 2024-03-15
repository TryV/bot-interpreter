<?php

namespace Tryv\PhpJsonBotInterpreter;

use Tryv\PhpJsonBotInterpreter\Exception\IncompleteSchemaException;
use Tryv\PhpJsonBotInterpreter\Interface\EventInterface;
use Tryv\PhpJsonBotInterpreter\Prototype;

final class DynamicBot extends Prototype
{
	public function run(array $events, array $context = [])
	{
		parent::resetRuntimeVars();

		foreach ($context as $key => $val)
			parent::setContextValue($key, $val);

		foreach ($events as $event) {
			$ref = parent::from($event);
			if ($ref instanceof EventInterface) {
				$ref->trigger();
			}
		}

	}

    public static function getEnvList(array|object $events): array
    {
        $keys = [];
        foreach ((array)$events as $event) {
            if (is_object($event) && property_exists($event, '_')) {
                if ($event->_ === 'Evaluator.Env') {
                    return [$event?->at];
                }
                $keys = array_merge($keys, (__METHOD__)($event));
            }

            if (is_array($event)) {
                $keys = array_merge($keys, (__METHOD__)($event));
            }
        }

        return $keys;
    }

    public static function convertBlockly(string $blocks): array
    {
        $blocks = json_decode($blocks, true);
        while (array_key_exists('blocks', $blocks)) $blocks = $blocks['blocks'];

        $filtered_blocks = array_values(array_filter($blocks, fn ($i) => str_starts_with($i['type'] ?? '', 'Event')));

        $convertNestedJson = function ($obj, $is_in_array = false) use (&$convertNestedJson) {

            if (is_array($obj) === false) return $obj;

            if (array_is_list($obj)===true) {
                foreach ($obj as &$item) {
                    $item = $convertNestedJson($item);
                }
            }
            else {
                $next = isset($obj['next']['block']) === true ? $obj['next']['block'] : null;
                foreach ($obj as $key => $val)
                {
                    unset($obj[$key]);
                    switch ($key)
                    {
                        case 'type':
                            $obj['_'] = $val;
                            if (str_starts_with($val, 'Update.')) {
                                list(, $at) = explode('.', $val, 2);
                                if (in_array($at, ['Message']) === true) {
                                    if (!isset($obj['inputs']['next']['block']))
                                        throw new IncompleteSchemaException(__FILE__, __LINE__);

                                    $next = $convertNestedJson($obj['inputs']['next']['block']);
                                    $snake_case = strtolower(preg_replace('/([a-z])([A-Z])/', '$1_$2', $next));
                                    return [
                                        '_' => 'Evaluator.Update',
                                        'at' => substr($val, strlen('Update.')) . '.' . $snake_case,
                                    ];
                                }
                                return substr($val, strlen('Update.')) . (isset($obj['inputs']['next']['block']) ? '.' . $convertNestedJson($obj['inputs']['next']['block']) : '');
                            }
                            elseif ($val === 'lists_create_with') {
                                return array_map(fn ($i) => $convertNestedJson($i['block'], true), array_values($obj['inputs'] ?? []));
                            }
                            elseif ($val === 'text') {
                                return $obj['fields']['TEXT'];
                            }
                            break;

                        case 'inputs':
                            foreach ($val as $input_key => $input) {
                                if (is_array($input) && isset($input['block'])) {
                                    $obj[$input_key] = $convertNestedJson($input['block']);
                                }
                                else {
                                    $obj[$input_key] = $convertNestedJson($input);
                                }
                            }
                            break;

                        case 'fields':
                            foreach ($val as $field_key => $field) {
                                $obj[$field_key] = $convertNestedJson($field);
                            }
                            break;
                    }

                }
                if ($next !== null) {
                    return [$obj, ...$convertNestedJson($next, true)];
                }
            }

            return $is_in_array ? [$obj] : $obj;
        };

        $arr = $convertNestedJson($filtered_blocks);
        return json_decode(json_encode($arr));
    }

}

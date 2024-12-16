<?php

namespace NovaItemsField;

use Laravel\Nova\Fields\Field;
use Laravel\Nova\Fields\SupportsDependentFields;
use Laravel\Nova\Fields\AsHTML;
use Laravel\Nova\Fields\Copyable;
use Laravel\Nova\Http\Requests\NovaRequest;

class Items extends Field
{
    use AsHTML;
    use Copyable;

    use SupportsDependentFields;

    public $items = [];
    public $component = 'nova-items-field';
    public $placeholder = "Add a new item";
    public $inputType = 'text';
    public $max = false;
    public $fullWidth = false;
    public $maxHeight = false;
    public $draggable = false;
    public $hideCreateButton = false;
    public $createButtonValue = "Add";
    public $deleteButtonValue;
    public $listFirst = false;
    public $detailItemComponent = 'detail-nova-items-field-item';

    public function __construct($name, $attribute = null, callable $resolveCallback = null)
    {
        parent::__construct($name, $attribute, $resolveCallback);

        $this->withMeta([
            'max' => $this->max,
            'items' => $this->items,
            'listFirst' => $this->listFirst,
            'inputType' => $this->inputType,
            'draggable' => $this->draggable,
            'fullWidth' => $this->fullWidth,
            'maxHeight' => $this->maxHeight,
            'placeholder' => $this->placeholder,
            'hideCreateButton' => $this->hideCreateButton,
            'createButtonValue' => $this->createButtonValue,
            'deleteButtonValue' => $this->deleteButtonValue,
            'detailItemComponent' => $this->detailItemComponent,
        ]);
    }

    public function resolve($resource, ?string $attribute = null): void
    {
        parent::resolve($resource, $attribute);

        if (is_null($this->fillCallback)) {
            $this->fillUsing(function ($request, $model, $attribute, $requestAttribute) {
                $model->$attribute = $this->isValidNullValue($request->$attribute) ? null : json_decode($request->$attribute, true);
            });
        }

        $this->withMeta([
            'max' => $this->max,
            'items' => $this->items,
            'listFirst' => $this->listFirst,
            'inputType' => $this->inputType,
            'draggable' => $this->draggable,
            'fullWidth' => $this->fullWidth,
            'maxHeight' => $this->maxHeight,
            'placeholder' => $this->placeholder,
            'hideCreateButton' => $this->hideCreateButton,
            'createButtonValue' => $this->createButtonValue,
            'deleteButtonValue' => $this->deleteButtonValue,
            'detailItemComponent' => $this->detailItemComponent,
        ]);
    }

    public function jsonSerialize(): array
    {
        return array_merge(parent::jsonSerialize(), [
            'asHtml' => $this->asHtml,
            'copyable' => $this->copyable,
        ]);
    }

    public function rules($rules)
    {
        if (!is_array($rules)) {
            abort(500, 'Nova Items Field requires array of validation rules');
        }

        $this->rules = [new ArrayRules($rules)];
        return $this;
    }

    public function values($values)
    {
        if (is_array($values) && count($values)) {
            $this->items = $values;
        }

        return $this;
    }

    public function max($max)
    {
        $this->max = $max;
        return $this;
    }

    public function hideCreateButton($hideCreateButton = true)
    {
        $this->hideCreateButton = $hideCreateButton;
        return $this;
    }

    public function inputType($inputType)
    {
        $this->inputType = $inputType;
        return $this;
    }

    public function fullWidth($fullWidth = true)
    {
        $this->fullWidth = $fullWidth;
        return $this;
    }

    public function maxHeight($maxHeight)
    {
        $this->maxHeight = $maxHeight;
        return $this;
    }

    public function draggable($draggable = true)
    {
        $this->draggable = $draggable;
        return $this;
    }

    public function placeholder($text)
    {
        $this->placeholder = $text;
        return $this;
    }

    public function listFirst($listFirst = true)
    {
        $this->listFirst = $listFirst;
        return $this;
    }

    public function deleteButtonValue($deleteButtonValue)
    {
        $this->deleteButtonValue = $deleteButtonValue;
        return $this;
    }

    public function createButtonValue($createButtonValue)
    {
        $this->createButtonValue = $createButtonValue;
        return $this;
    }

    public function detailItemComponent($detailItemComponent)
    {
        $this->detailItemComponent = $detailItemComponent;
        return $this;
    }

    public function indexAsList()
    {
        $this->withMeta(['indexAsList' => true]);
        return $this;
    }

    public function detailsAsTotal()
    {
        $this->withMeta(['detailsAsTotal' => true]);
        return $this;
    }
}

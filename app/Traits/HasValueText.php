<?php

namespace App\Traits;

trait HasValueText
{
    public function toValueTextArray($valueField = 'id', $textField = 'name')
    {
        return [
            'value' => $this->$valueField,
            'label'  => $this->$textField,
        ];
    }
}

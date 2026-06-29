<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class DynamicImport implements ToCollection, WithHeadingRow
{
    protected $columns;
    protected $modelClass;

    public function __construct(array $columns, string $modelClass)
    {
        $this->columns = $columns;
        $this->modelClass = $modelClass;
    }

    public function collection(Collection $rows)
    {
        // dd($rows, $this->columns, $this->modelClass);
        $rows = $rows->map(function ($row) {
            return collect($row)->mapWithKeys(function ($value, $key) {
                return [strtolower($key) => $value];
            });
        });

        foreach ($rows as $row) {
            $data = [];

            foreach ($this->columns as $column) {
                $key = strtolower(str_replace(' ', '_', $column)); 
                $data[$column] = $row[$key] ?? null;
            }

            $allEmpty = collect($this->columns)->every(function ($column) use ($data) {
                return empty($data[$column]);
            });

            if ($allEmpty) {
                continue;
            }

            $query = $this->modelClass::query();
            foreach ($this->columns as $column) {
                $query->where($column, $data[$column]);
            }

            $existing = $query->first();

            if ($existing) {
                $existing->update($data);
            } else {
                $this->modelClass::create($data);
            }
        }
    }


}

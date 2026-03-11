<?php

declare(strict_types=1);

namespace EslamRedaDiv\FilamentCopilot\Discovery;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

class SchemaInspector
{
    /**
     * Get a human-readable schema description for a model.
     */
    public function inspect(string $modelClass): array
    {
        /** @var Model $model */
        $model = new $modelClass;
        $table = $model->getTable();
        $columns = $this->getColumns($table);
        $fillable = $model->getFillable();
        $casts = $model->getCasts();

        return [
            'model' => $modelClass,
            'table' => $table,
            'columns' => $columns,
            'fillable' => $fillable,
            'casts' => $casts,
            'primary_key' => $model->getKeyName(),
        ];
    }

    /**
     * Build a concise text description of the schema for the AI context.
     */
    public function describeForAi(string $modelClass): string
    {
        $schema = $this->inspect($modelClass);
        $lines = ["Model: {$schema['model']} (table: {$schema['table']})"];

        $lines[] = 'Columns:';
        foreach ($schema['columns'] as $column) {
            $nullable = $column['nullable'] ? ', nullable' : '';
            $lines[] = "  - {$column['name']} ({$column['type']}{$nullable})";
        }

        if (! empty($schema['fillable'])) {
            $lines[] = 'Fillable: '.implode(', ', $schema['fillable']);
        }

        if (! empty($schema['casts'])) {
            $castStr = collect($schema['casts'])
                ->map(fn ($type, $field) => "{$field}: {$type}")
                ->implode(', ');
            $lines[] = "Casts: {$castStr}";
        }

        return implode("\n", $lines);
    }

    protected function getColumns(string $table): array
    {
        if (! Schema::hasTable($table)) {
            return [];
        }

        $columns = Schema::getColumns($table);

        return collect($columns)->map(fn (array $column) => [
            'name' => $column['name'],
            'type' => $column['type_name'] ?? $column['type'] ?? 'unknown',
            'nullable' => $column['nullable'] ?? false,
        ])->toArray();
    }
}

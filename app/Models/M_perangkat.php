<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class M_perangkat extends Model
{
    use HasFactory;

    protected $table = "M_PERANGKAT";
    protected $primaryKey = 'ID_PERANGKAT';
    public $timestamps = false;

    protected $fillable = [
        'NAMA_PERANGKAT',
        'KODE_PERANGKAT',
        'CREATE_BY',
        'UPDATE_BY',
        'STATUS',
        'param1', 'param2', 'param3', 'param4', 'param5', 'param6',
        'param7', 'param8', 'param9', 'param10', 'param11', 'param12',
        'param13', 'param14', 'param15', 'param16'
    ];

    /**
     * Get dynamic field schema from param columns
     * Returns array of non-null params for form generation
     */
    public function getDynamicSchema(): array
    {
        $schema = [];
        for ($i = 1; $i <= 16; $i++) {
            $paramValue = $this->{"param$i"};
            if (!empty($paramValue)) {
                $schema["param$i"] = [
                    'label' => $paramValue,
                    'type' => $this->inferFieldType($paramValue),
                ];
            }
        }
        return $schema;
    }

    /**
     * Determine field type based on field name
     */
    private function inferFieldType(string $fieldName): string
    {
        // Jika mengandung 'KETERANGAN' -> textarea
        if (stripos($fieldName, 'KETERANGAN') !== false) {
            return 'textarea';
        }
        return 'text';
    }

    /**
     * Get table headers from param columns
     */
    public function getDynamicHeaders(): array
    {
        $headers = [];
        for ($i = 1; $i <= 16; $i++) {
            $paramValue = $this->{"param$i"};
            if (!empty($paramValue)) {
                $headers[] = [
                    'key' => "param$i",
                    'label' => $paramValue
                ];
            }
        }
        return $headers;
    }

    /**
     * Get all param values as array
     */
    public function getParamLabels(): array
    {
        $params = [];
        for ($i = 1; $i <= 16; $i++) {
            $paramValue = $this->{"param$i"};
            if (!empty($paramValue)) {
                $params["param$i"] = $paramValue;
            }
        }
        return $params;
    }

    /**
     * Relationship: One perangkat has many inventaris
     */
    public function inventaris()
    {
        return $this->hasMany(T_inventaris::class, 'ID_PERANGKAT', 'ID_PERANGKAT');
    }
}

<?php

namespace App\Transformers;

use CodeIgniter\API\BaseTransformer;

class AuthTransformer extends BaseTransformer
{
    public function toArray(mixed $resource): array
    {
        return [
            'id' => $resource['id'],
            'name' => $resource['name'],
            'email' => $resource['email'],
            'created_at' => $resource['created_at'],
            'updated_at' => $resource['updated_at'],
        ];
    }
}

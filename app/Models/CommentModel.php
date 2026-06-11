<?php

namespace App\Models;

use CodeIgniter\Model;

class CommentModel extends Model
{
    protected $table            = 'comments';
    protected $primaryKey       = 'id';
    protected $returnType       = 'array';
    protected $allowedFields    = ['post_id', 'user_id', 'content'];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $validationRules      = [
        'content' => 'required',
    ];

    public function getByPostWithAuthors(int $postId): array
    {
        return $this->db->table('comments c')
            ->select('c.id, c.content, c.created_at, u.name as commenter_name')
            ->join('users u', 'u.id = c.user_id')
            ->where('c.post_id', $postId)
            ->orderBy('c.created_at', 'ASC')
            ->get()
            ->getResultArray();
    }
}

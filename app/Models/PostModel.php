<?php

namespace App\Models;

use CodeIgniter\Model;

class PostModel extends Model
{
    protected $table            = 'posts';
    protected $primaryKey       = 'id';
    protected $returnType       = 'array';
    protected $allowedFields    = ['title', 'content', 'user_id', 'status'];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $validationRules      = [
        'title' => 'required|min_length[3]|max_length[255]',
        'content' => 'required',
        'user_id' => 'required|integer',
        'status' => 'required|in_list[draft,published,archived]',
    ];

    public function getPostWithAuthor(int $postId)
    {
        return $this->db->table('posts p')
            ->select('p.*, u.name as author_name, u.email as author_email')
            ->join('users u', 'u.id = p.user_id')
            ->where('p.id', $postId)
            ->get()
            ->getRowArray();
    }

    public function getPublishedWithAuthors(int $limit = 10, int $page = 1)
    {
        $total = $this->db->table('posts p')
            ->join('users u', 'u.id = p.user_id')
            ->where('p.status', 'published')
            ->countAllResults();

        $offset = ($page - 1) * $limit;

        $posts = $this->db->table('posts p')
            ->select('p.*, u.name as author_name')
            ->join('users u', 'u.id = p.user_id')
            ->where('p.status', 'published')
            ->limit($limit, $offset)
            ->get()
            ->getResultArray();

        return [
            'data' => $posts,
            'pagination' => [
                'total' => $total,
                'per_page' => $limit,
                'current_page' => (int)$page,
                'last_page' => (int) ceil($total / $limit),
            ]
        ];
    }
}

<?php

namespace App\Controllers\Api;

use App\Models\PostModel;

class PostController extends BaseApiController
{
    protected PostModel $postModel;

    public function __construct()
    {
        $this->postModel = new PostModel();
    }

    public function index()
    {
        $limit = $this->request->getGet('limit', FILTER_VALIDATE_INT) ?? 10;
        $page = $this->request->getGet('page', FILTER_VALIDATE_INT) ?? 1;
        $result = $this->postModel->getPublishedWithAuthors($limit, $page);
        return $this->respondWithSuccess($result);
    }

    public function show($id = null)
    {
        $post = $this->postModel->getWithRelations((int)$id);
        if (!$post) {
            return $this->respondWithError('Post not found', 404);
        }
        return $this->respondWithSuccess($post);
    }

    public function create()
    {
        $rules = [
            'title' => 'required|min_length[3]|max_length[255]',
            'content' => 'required',
            'status' => 'permit_empty|in_list[draft,published,archived]',
        ];
        if (!$this->validate($rules)) {
            return $this->respondWithError('Validation failed', 422, $this->validator->getErrors());
        }

        $data = [
            'user_id' => $this->getAuthUserId(),
            'title' => $this->request->getVar('title'),
            'content' => $this->request->getVar('content'),
            'status' => $this->request->getVar('status') ?? 'draft',
        ];

        $postId = $this->postModel->insert($data);
        if (!$postId) {
            return $this->respondWithError('Failed to create post', 500);
        }

        $post = $this->postModel->find($postId);
        return $this->respondWithSuccess($post, 'Post created successfully', 201);
    }

    public function update($id = null)
    {
        $post = $this->postModel->find((int)$id);
        if (!$post) {
            return $this->respondWithError('Post not found', 404);
        }

        if ((int) $post['user_id'] !== $this->getAuthUserId()) {
            return $this->respondWithError('Forbidden', 403);
        }

        $rules = [
            'title' => 'permit_empty|min_length[3]|max_length[255]',
            'content' => 'permit_empty',
            'status' => 'permit_empty|in_list[draft,published,archived]',
        ];
        if (!$this->validate($rules)) {
            return $this->respondWithError('Validation failed', 422, $this->validator->getErrors());
        }
        $data = array_filter([
            'title' => $this->request->getVar('title'),
            'content' => $this->request->getVar('content'),
            'status' => $this->request->getVar('status'),
        ]);

        $this->postModel->update((int) $post['id'], $data);

        return $this->respondWithSuccess($this->postModel->find($post['id']), 'Post updated successfully');
    }

    public function delete($id = null)
    {
        $post = $this->postModel->find((int)$id);
        if (!$post) {
            return $this->respondWithError('Post not found', 404);
        }

        if ((int) $post['user_id'] !== $this->getAuthUserId()) {
            return $this->respondWithError('Forbidden', 403);
        }

        $this->postModel->delete((int) $post['id']);
        return $this->respondWithSuccess(null, 'Post deleted successfully');
    }
}

<?php

namespace App\Controllers\Api;

use App\Models\CommentModel;
use App\Models\PostModel;

class CommentController extends BaseApiController
{
    protected CommentModel $commentModel;
    protected PostModel $postModel;

    public function __construct()
    {
        $this->commentModel = new CommentModel();
        $this->postModel = new PostModel();
    }

    public function create($postId = null)
    {
        if (!$postId || !$this->postModel->find($postId)) {
            return $this->respondWithError('Post not found', 404);
        }

        $rules = $this->commentModel->getValidationRules();
        if (!$this->validate($rules)) {
            return $this->respondWithError('Validation failed', 422, $this->validator->getErrors());
        }

        $data = [
            'post_id' => $postId,
            'user_id' => $this->getAuthUserId(),
            'content' => $this->request->getVar('content'),
        ];

        $commentId = $this->commentModel->insert($data);

        if (!$commentId) {
            return $this->respondWithError('Failed to create comment', 500);
        }

        $comment = $this->commentModel->find($commentId);

        return $this->respondWithSuccess($comment, 'Comment created successfully', 201);
    }

    public function update($postId = null, $id = null)
    {
        if (!$id || !$this->commentModel->find($id)) {
            return $this->respondWithError('Comment not found', 404);
        }

        $rules = $this->commentModel->getValidationRules();
        if (!$this->validate($rules)) {
            return $this->respondWithError('Validation failed', 422, $this->validator->getErrors());
        }

        $data = [
            'content' => $this->request->getVar('content'),
        ];

        if (!$this->commentModel->update($id, $data)) {
            return $this->respondWithError('Failed to update comment', 500);
        }

        $comment = $this->commentModel->find($id);

        return $this->respondWithSuccess($comment, 'Comment updated successfully');
    }

    public function delete($postId = null, $id = null)
    {
        if (!$id || !$this->commentModel->find($id)) {
            return $this->respondWithError('Comment not found', 404);
        }

        if (!$this->commentModel->delete($id)) {
            return $this->respondWithError('Failed to delete comment', 500);
        }

        return $this->respondWithSuccess(null, 'Comment deleted successfully');
    }
}

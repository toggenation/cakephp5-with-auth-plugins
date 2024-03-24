<?php

declare(strict_types=1);

namespace App\Policy;

use App\Model\Entity\Post;
use Authorization\IdentityInterface;

/**
 * Post policy
 */
class PostPolicy
{
    /**
     * Check if $user can add Post
     *
     * @param \Authorization\IdentityInterface $user The user.
     * @param \App\Model\Entity\Post $post
     * @return bool
     */
    public function canAdd(IdentityInterface $user, Post $post)
    {
        return true;
    }

    public function canCookie(IdentityInterface $user, Post $post)
    {
        return true;
    }

    public function canTestRedirect(IdentityInterface $user, Post $post)
    {
        return false;
    }
    /**
     * Check if $user can edit Post
     *
     * @param \Authorization\IdentityInterface $user The user.
     * @param \App\Model\Entity\Post $post
     * @return bool
     */
    public function canEdit(IdentityInterface $user, Post $post)
    {
        return $this->isAuthor($user, $post);
    }

    protected function isAuthor(IdentityInterface $user, Post $post)
    {
        /**
         * @var \Authorization\Identity $user
         */
        return $post->user_id === $user->getIdentifier();
    }
    /**
     * Check if $user can delete Post
     *
     * @param \Authorization\IdentityInterface $user The user.
     * @param \App\Model\Entity\Post $post
     * @return bool
     */
    public function canDelete(IdentityInterface $user, Post $post)
    {
        return $this->isAuthor($user, $post);
    }

    /**
     * Check if $user can view Post
     *
     * @param \Authorization\IdentityInterface $user The user.
     * @param \App\Model\Entity\Post $post
     * @return bool
     */
    public function canView(IdentityInterface $user, Post $post)
    {
        return true;
    }
}

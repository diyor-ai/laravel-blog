<?php

namespace Tests\Unit;

use App\Models\Post;
use App\Models\User;
use App\Policies\PostPolicy;
use PHPUnit\Framework\TestCase;

class PostPolicyTest extends TestCase
{
    private function userWithId(int $id): User
    {
        return (new User)->forceFill(['id' => $id]);
    }

    private function postOwnedBy(int $id): Post
    {
        return (new Post)->forceFill(['user_id' => $id]);
    }

    public function test_only_the_owner_can_update_delete_and_restore(): void
    {
        $policy = new PostPolicy;
        $owner = $this->userWithId(1);
        $other = $this->userWithId(2);
        $post = $this->postOwnedBy(1);

        foreach (['update', 'delete', 'restore'] as $ability) {
            $this->assertTrue($policy->$ability($owner, $post), "owner should be able to $ability");
            $this->assertFalse($policy->$ability($other, $post), "other user must not $ability");
        }
    }

    public function test_nobody_can_force_delete(): void
    {
        $policy = new PostPolicy;

        $this->assertFalse($policy->forceDelete($this->userWithId(1), $this->postOwnedBy(1)));
        $this->assertFalse($policy->forceDelete($this->userWithId(2), $this->postOwnedBy(1)));
    }
}

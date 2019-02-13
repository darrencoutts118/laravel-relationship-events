<?php

namespace Chelout\RelationshipEvents\Tests\Feature;

use Chelout\RelationshipEvents\Tests\Stubs\Post;
use Chelout\RelationshipEvents\Tests\Stubs\Tag;
use Chelout\RelationshipEvents\Tests\TestCase;
use Illuminate\Support\Facades\Event;

class HasMorphedByManyEventsTest extends TestCase
{
    public function setup()
    {
        parent::setup();

        Post::setupTable();
        Tag::setupTable();
    }

    /** @test */
    public function it_fires_morphedByManyAttaching_and_morphedByManyAttached_when_created()
    {
        Event::fake();

        $tag = Tag::create();
        $post = $tag->posts()->create(['title' => 'Post title']);

        $this->assertCount(1, $tag->posts);
        $this->assertEquals('Post title', $tag->posts[0]->title);

        Event::assertDispatched(
            'eloquent.morphedByManyAttaching: ' . Tag::class,
            function ($e, $callback) use ($post, $tag) {
                return $callback[0] == 'posts' && $callback[1]->is($tag) && $callback[2][0] == $post->id;
            }
        );
        Event::assertDispatched(
            'eloquent.morphedByManyAttached: ' . Tag::class,
            function ($e, $callback) use ($post, $tag) {
                return $callback[0] == 'posts' && $callback[1]->is($tag) && $callback[2][0] == $post->id;
            }
        );
    }

    /** @test */
    public function it_fires_morphedByManyAttaching_and_stop_if_false_is_returned()
    {
        Event::listen(
            'eloquent.morphedByManyAttaching: ' . Tag::class,
            function () {
                return false;
            }
        );

        $tag = Tag::create();
        $post = $tag->posts()->create(['title' => 'Post title']);

        $this->assertCount(0, $tag->posts);
    }

    /** @test */
    public function it_fires_morphedByManyAttaching_and_morphedByManyAttached_when_saved()
    {
        Event::fake();

        $tag = Tag::create();
        $post = $tag->posts()->save(new Post);

        $this->assertCount(1, $tag->posts);

        Event::assertDispatched(
            'eloquent.morphedByManyAttaching: ' . Tag::class,
            function ($e, $callback) use ($post, $tag) {
                return $callback[0] == 'posts' && $callback[1]->is($tag) && $callback[2][0] == $post->id;
            }
        );
        Event::assertDispatched(
            'eloquent.morphedByManyAttached: ' . Tag::class,
            function ($e, $callback) use ($post, $tag) {
                return $callback[0] == 'posts' && $callback[1]->is($tag) && $callback[2][0] == $post->id;
            }
        );
    }

    /** @test */
    public function it_fires_morphedByManyAttaching_and_stops_when_false_is_returned()
    {
        Event::listen(
            'eloquent.morphedByManyAttaching: ' . Tag::class,
            function () {
                return false;
            }
        );

        $tag = Tag::create();
        $post = $tag->posts()->save(new Post);

        $this->assertCount(0, $tag->posts);
    }

    /** @test */
    public function it_fires_morphedByManyAttaching_and_morphedByManyAttached_when_attached()
    {
        Event::fake();

        $post = Post::create();
        $tag = Tag::create();
        $tag->posts()->attach($post);

        $this->assertCount(1, $tag->posts);

        Event::assertDispatched(
            'eloquent.morphedByManyAttaching: ' . Tag::class,
            function ($e, $callback) use ($post, $tag) {
                return $callback[0] == 'posts' && $callback[1]->is($tag) && $callback[2][0] == $post->id;
            }
        );
        Event::assertDispatched(
            'eloquent.morphedByManyAttached: ' . Tag::class,
            function ($e, $callback) use ($post, $tag) {
                return $callback[0] == 'posts' && $callback[1]->is($tag) && $callback[2][0] == $post->id;
            }
        );
    }

    /** @test */
    public function it_fires_morphedByManyAttaching_and_stops_when_false_is_returned_when_attaching()
    {
        Event::listen(
            'eloquent.morphedByManyAttaching: ' . Tag::class,
            function () {
                return false;
            }
        );

        $post = Post::create();
        $tag = Tag::create();
        $tag->posts()->attach($post);

        $this->assertCount(0, $tag->posts);
    }

    /** @test */
    public function it_fires_morphedByManyDetaching_and_morphedByManyDetached_when_detached()
    {
        Event::fake();

        $post = Post::create();
        $tag = Tag::create();
        $tag->posts()->attach($post);
        $tag->posts()->detach($post);

        $this->assertCount(0, $tag->posts);

        Event::assertDispatched(
            'eloquent.morphedByManyDetaching: ' . Tag::class,
            function ($e, $callback) use ($post, $tag) {
                return $callback[0] == 'posts' && $callback[1]->is($tag) && $callback[2][0] == $post->id;
            }
        );
        Event::assertDispatched(
            'eloquent.morphedByManyDetached: ' . Tag::class,
            function ($e, $callback) use ($post, $tag) {
                return $callback[0] == 'posts' && $callback[1]->is($tag) && $callback[2][0] == $post->id;
            }
        );
    }

    /** @test */
    public function it_fires_morphedByManyDetaching_and_stops_when_false_is_returned()
    {
        Event::listen(
            'eloquent.morphedByManyDetaching: ' . Tag::class,
            function () {
                return false;
            }
        );

        $post = Post::create();
        $tag = Tag::create();
        $tag->posts()->attach($post);
        $tag->posts()->detach($post);

        $this->assertCount(1, $tag->posts);
    }

    /** @test */
    public function it_fires_morphedByManySyncing_and_morphedByManySynced()
    {
        Event::fake();

        $post = Post::create();
        $tag = Tag::create();
        $tag->posts()->sync($post);

        $this->assertCount(1, $tag->posts);

        Event::assertDispatched(
            'eloquent.morphedByManySyncing: ' . Tag::class,
            function ($e, $callback) use ($post, $tag) {
                return $callback[0] == 'posts' && $callback[1]->is($tag) && $callback[2][0] == $post->id;
            }
        );
        Event::assertDispatched(
            'eloquent.morphedByManySynced: ' . Tag::class,
            function ($e, $callback) use ($post, $tag) {
                return $callback[0] == 'posts' && $callback[1]->is($tag) && $callback[2][0] == $post->id;
            }
        );
    }

    /** @test */
    public function it_fires_morphedByManySyncing_and_stops_when_false_is_returned()
    {
        Event::listen(
            'eloquent.morphedByManySyncing: ' . Tag::class,
            function () {
                return false;
            }
        );

        $post = Post::create();
        $tag = Tag::create();
        $tag->posts()->sync($post);

        $this->assertCount(0, $tag->posts);
    }

    /** @test */
    public function it_fires_morphedByManyToggling_and_morphedByManyToggled()
    {
        Event::fake();

        $post = Post::create();
        $tag = Tag::create();
        $tag->posts()->toggle($post);

        $this->assertCount(1, $tag->posts);

        Event::assertDispatched(
            'eloquent.morphedByManyToggling: ' . Tag::class,
            function ($e, $callback) use ($post, $tag) {
                return $callback[0] == 'posts' && $callback[1]->is($tag) && $callback[2][0] == $post->id;
            }
        );
        Event::assertDispatched(
            'eloquent.morphedByManyToggled: ' . Tag::class,
            function ($e, $callback) use ($post, $tag) {
                return $callback[0] == 'posts' && $callback[1]->is($tag) && $callback[2][0] == $post->id;
            }
        );
    }

    /** @test */
    public function it_fires_morphedByManyToggling_and_stops_when_false_is_returned()
    {
        Event::listen(
            'eloquent.morphedByManyToggling: ' . Tag::class,
            function () {
                return false;
            }
        );

        $post = Post::create();
        $tag = Tag::create();
        $tag->posts()->toggle($post);

        $this->assertCount(0, $tag->posts);
    }

    /** @test */
    public function it_fires_morphedByManyUpdatingExistingPivot_and_morphedByManyUpdatedExistingPivot()
    {
        Event::fake();

        $post = Post::create();
        $tag = Tag::create();
        $tag->posts()->attach($post);
        $tag->posts()->updateExistingPivot(1, ['pivot_field' => 'Pivot Field']);

        $this->assertCount(1, $tag->posts);
        $this->assertEquals('Pivot Field', $tag->posts[0]->pivot->pivot_field);

        Event::assertDispatched(
            'eloquent.morphedByManyUpdatingExistingPivot: ' . Tag::class,
            function ($e, $callback) use ($post, $tag) {
                return $callback[0] == 'posts' && $callback[1]->is($tag) && $callback[2][0] == $post->id;
            }
        );
        Event::assertDispatched(
            'eloquent.morphedByManyUpdatedExistingPivot: ' . Tag::class,
            function ($e, $callback) use ($post, $tag) {
                return $callback[0] == 'posts' && $callback[1]->is($tag) && $callback[2][0] == $post->id;
            }
        );
    }

    /** @test */
    public function it_fires_morphedByManyUpdatingExistingPivot_and_stops_when_false_is_returned()
    {
        Event::listen(
            'eloquent.morphedByManyUpdatingExistingPivot: ' . Tag::class,
            function () {
                return false;
            }
        );

        $post = Post::create();
        $tag = Tag::create();
        $tag->posts()->attach($post);
        $tag->posts()->updateExistingPivot(1, ['pivot_field' => 'Pivot Field']);

        $this->assertCount(1, $tag->posts);
        $this->assertNull($tag->posts[0]->pivot->pivot_field);
    }
}

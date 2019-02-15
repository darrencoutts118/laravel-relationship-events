<?php

namespace Chelout\RelationshipEvents\Tests\Feature;

use Chelout\RelationshipEvents\Tests\Stubs\Post;
use Chelout\RelationshipEvents\Tests\Stubs\Tag;
use Chelout\RelationshipEvents\Tests\TestCase;
use Illuminate\Support\Facades\Event;

class HasMorphToManyEventsTest extends TestCase
{
    public function setup()
    {
        parent::setup();

        Post::setupTable();
        Tag::setupTable();
    }

    /** @test */
    public function it_fires_morphToManyAttaching_and_morphToManyAttached()
    {
        Event::fake();

        $post = Post::create();
        $tag = Tag::create();
        $post->tags()->attach($tag);

        $this->assertCount(1, $post->tags);

        Event::assertDispatched(
            'eloquent.morphToManyAttaching: ' . Post::class,
            function ($e, $callback) use ($post, $tag) {
                return $callback[0] == 'tags' && $callback[1]->is($post) && $callback[2][0] == $tag->id;
            }
        );
        Event::assertDispatched(
            'eloquent.morphToManyAttached: ' . Post::class,
            function ($e, $callback) use ($post, $tag) {
                return $callback[0] == 'tags' && $callback[1]->is($post) && $callback[2][0] == $tag->id;
            }
        );
    }

    /** @test */
    public function it_fires_morphToManyAttaching_and_stops_if_false_is_returned()
    {
        Event::listen(
            'eloquent.morphToManyAttaching: ' . Post::class,
            function () {
                return false;
            }
        );

        $post = Post::create();
        $tag = Tag::create();
        $post->tags()->attach($tag);

        $this->assertCount(0, $post->tags);
    }

    /** @test */
    public function it_fires_morphToManyDetaching_and_morphToManyDetached()
    {
        Event::fake();

        $post = Post::create();
        $tag = Tag::create();
        $post->tags()->attach($tag);
        $post->tags()->detach($tag);

        $this->assertCount(0, $post->tags);

        Event::assertDispatched(
            'eloquent.morphToManyDetaching: ' . Post::class,
            function ($e, $callback) use ($post, $tag) {
                return $callback[0] == 'tags' && $callback[1]->is($post) && $callback[2][0] == $tag->id;
            }
        );
        Event::assertDispatched(
            'eloquent.morphToManyDetached: ' . Post::class,
            function ($e, $callback) use ($post, $tag) {
                return $callback[0] == 'tags' && $callback[1]->is($post) && $callback[2][0] == $tag->id;
            }
        );
    }

    /** @test */
    public function it_fires_morphToManyDetaching_and_stop_if_false_is_returned()
    {
        Event::listen(
            'eloquent.morphToManyDetaching: ' . Post::class,
            function () {
                return false;
            }
        );

        $post = Post::create();
        $tag = Tag::create();
        $post->tags()->attach($tag);
        $post->tags()->detach($tag);

        $this->assertCount(1, $post->tags);
    }

    /** @test */
    public function it_fires_morphToManySyncing_and_morphToManySynced()
    {
        Event::fake();

        $post = Post::create();
        $tag = Tag::create();
        $post->tags()->sync($tag);

        $this->assertCount(1, $post->tags);

        Event::assertDispatched(
            'eloquent.morphToManySyncing: ' . Post::class,
            function ($e, $callback) use ($post, $tag) {
                return $callback[0] == 'tags' && $callback[1]->is($post) && $callback[2][0] == $tag->id;
            }
        );
        Event::assertDispatched(
            'eloquent.morphToManySynced: ' . Post::class,
            function ($e, $callback) use ($post, $tag) {
                return $callback[0] == 'tags' && $callback[1]->is($post) && $callback[2][0] == $tag->id;
            }
        );
    }

    /** @test */
    public function it_fires_morphToManySyncing_and_stops_when_false_is_returned()
    {
        Event::listen(
            'eloquent.morphToManySyncing: ' . Post::class,
            function () {
                return false;
            }
        );

        $post = Post::create();
        $tag = Tag::create();
        $post->tags()->sync($tag);

        $this->assertCount(0, $post->tags);
    }

    /** @test */
    public function it_fires_morphToManyUpdatingExistingPivot_and_morphToManyUpdatedExistingPivot()
    {
        Event::fake();

        $post = Post::create();
        $tag = Tag::create();
        $post->tags()->sync($tag);
        $post->tags()->updateExistingPivot(1, ['pivot_field' => 'pivot']);

        $this->assertCount(1, $post->tags);
        $this->assertEquals('pivot', $post->tags[0]->pivot->pivot_field);

        Event::assertDispatched(
            'eloquent.morphToManyUpdatingExistingPivot: ' . Post::class,
            function ($e, $callback) use ($post, $tag) {
                return $callback[0] == 'tags' && $callback[1]->is($post) && $callback[2][0] == $tag->id;
            }
        );
        Event::assertDispatched(
            'eloquent.morphToManyUpdatedExistingPivot: ' . Post::class,
            function ($e, $callback) use ($post, $tag) {
                return $callback[0] == 'tags' && $callback[1]->is($post) && $callback[2][0] == $tag->id;
            }
        );
    }

    /** @test */
    public function it_fires_morphToManyUpdatingExistingPivot_and_stops_when_flase_is_returned()
    {
        Event::listen(
            'eloquent.morphToManyUpdatingExistingPivot: ' . Post::class,
            function () {
                return false;
            }
        );

        $post = Post::create();
        $tag = Tag::create();
        $post->tags()->sync($tag);
        $post->tags()->updateExistingPivot(1, ['pivot_field' => 'pivot']);

        $this->assertCount(1, $post->tags);
        $this->assertNull($post->tags[0]->pivot->pivot_field);
    }
}

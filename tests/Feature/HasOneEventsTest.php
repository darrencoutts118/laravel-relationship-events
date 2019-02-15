<?php

namespace Chelout\RelationshipEvents\Tests\Feature;

use Chelout\RelationshipEvents\Tests\Stubs\Profile;
use Chelout\RelationshipEvents\Tests\Stubs\User;
use Chelout\RelationshipEvents\Tests\TestCase;
use Illuminate\Support\Facades\Event;

class HasOneEventsTest extends TestCase
{
    public function setup()
    {
        parent::setup();

        User::setupTable();
        Profile::setupTable();
    }

    /** @test */
    public function it_fires_hasOneCreating_and_hasOneCreated_when_a_belonged_model_created()
    {
        Event::fake();

        $user = User::create();
        $profile = $user->profile()->create([]);

        $this->assertNotNull($user->profile);

        Event::assertDispatched(
            'eloquent.hasOneCreating: ' . User::class,
            function ($e, $callback) use ($user, $profile) {
                return $callback[0]->is($user) && $callback[1]->is($profile);
            }
        );
        Event::assertDispatched(
            'eloquent.hasOneCreated: ' . User::class,
            function ($e, $callback) use ($user, $profile) {
                return $callback[0]->is($user) && $callback[1]->is($profile);
            }
        );
    }

    /** @test */
    public function it_fires_hasOneCreating_and_stops_when_false_is_returned()
    {
        Event::listen(
            'eloquent.hasOneCreating: ' . User::class,
            function () {
                return false;
            }
        );

        $user = User::create();
        $profile = $user->profile()->create([]);

        $this->assertNull($user->profile);
    }

    /** @test */
    public function it_fires_hasOneSaving_and_hasOneSaved_when_a_belonged_model_saved()
    {
        Event::fake();

        $user = User::create();
        $profile = $user->profile()->save(new Profile);

        $this->assertNotNull($user->profile);

        Event::assertDispatched(
            'eloquent.hasOneSaving: ' . User::class,
            function ($e, $callback) use ($user, $profile) {
                return $callback[0]->is($user) && $callback[1]->is($profile);
            }
        );
        Event::assertDispatched(
            'eloquent.hasOneSaved: ' . User::class,
            function ($e, $callback) use ($user, $profile) {
                return $callback[0]->is($user) && $callback[1]->is($profile);
            }
        );
    }

    /** @test */
    public function it_fires_hasOneSaving_and_stops_when_false_is_returned()
    {
        Event::listen(
            'eloquent.hasOneSaving: ' . User::class,
            function () {
                return false;
            }
        );

        $user = User::create();
        $profile = $user->profile()->save(new Profile);

        $this->assertNull($user->profile);
    }

    /** @test */
    public function it_fires_hasOneUpdating_and_hasOneUpdated_when_a_belonged_model_updated()
    {
        Event::fake();

        $user = User::create();
        $profile = $user->profile()->save(new Profile);
        $user->profile()->update(['username' => 'joeblogs1']);

        $this->assertNotNull($user->profile);
        $this->assertEquals('joeblogs1', $user->profile->username);

        Event::assertDispatched(
            'eloquent.hasOneUpdating: ' . User::class,
            function ($e, $callback) use ($user, $profile) {
                return $callback[0]->is($user) && $callback[1]->is($profile);
            }
        );
        Event::assertDispatched(
            'eloquent.hasOneUpdated: ' . User::class,
            function ($e, $callback) use ($user, $profile) {
                return $callback[0]->is($user) && $callback[1]->is($profile);
            }
        );
    }

    /** @test */
    public function it_fires_hasOneUpdating_and_stops_when_false_is_returned()
    {
        Event::listen(
            'eloquent.hasOneUpdating: ' . User::class,
            function () {
                return false;
            }
        );

        $user = User::create();
        $profile = $user->profile()->save(new Profile);
        $user->profile()->update(['username' => 'joeblogs1']);

        $this->assertNotNull($user->profile);
        $this->assertNull($user->profile->username);
    }
}

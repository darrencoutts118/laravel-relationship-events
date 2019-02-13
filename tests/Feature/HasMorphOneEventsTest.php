<?php

namespace Chelout\RelationshipEvents\Tests\Feature;

use Chelout\RelationshipEvents\Tests\Stubs\Address;
use Chelout\RelationshipEvents\Tests\Stubs\User;
use Chelout\RelationshipEvents\Tests\TestCase;
use Illuminate\Support\Facades\Event;

class HasMorphOneEventsTest extends TestCase
{
    public function setup()
    {
        parent::setup();

        User::setupTable();
        Address::setupTable();
    }

    /** @test */
    public function it_fires_morphOneCreating_and_morphOneCreated_when_belonged_model_with_morph_one_created()
    {
        Event::fake();

        $user = User::create();
        $address = $user->address()->create([]);

        $this->assertNotNull($user->address);

        Event::assertDispatched(
            'eloquent.morphOneCreating: ' . User::class,
            function ($event, $callback) use ($user, $address) {
                return $callback[0]->is($user) && $callback[1]->is($address);
            }
        );
        Event::assertDispatched(
            'eloquent.morphOneCreated: ' . User::class,
            function ($event, $callback) use ($user, $address) {
                return $callback[0]->is($user) && $callback[1]->is($address);
            }
        );
    }

    /** @test */
    public function it_fires_morphOneCreating_and_stop_when_return_is_false_when_belonged_model_with_morph_one_created()
    {
        Event::listen(
            'eloquent.morphOneCreating: ' . User::class,
            function () {
                return false;
            }
        );

        $user = User::create();
        $address = $user->address()->create([]);

        $this->assertNull($user->address);
    }

    /** @test */
    public function it_fires_morphOneSaving_and_morphOneSaved_when_belonged_model_with_morph_one_saved()
    {
        Event::fake();

        $user = User::create();
        $address = $user->address()->save(new Address);

        $this->assertNotNull($user->address);

        Event::assertDispatched(
            'eloquent.morphOneSaving: ' . User::class,
            function ($event, $callback) use ($user, $address) {
                return $callback[0]->is($user) && $callback[1]->is($address);
            }
        );
        Event::assertDispatched(
            'eloquent.morphOneSaved: ' . User::class,
            function ($event, $callback) use ($user, $address) {
                return $callback[0]->is($user) && $callback[1]->is($address);
            }
        );
    }

    /** @test */
    public function it_fires_morphOneSaving_and_stop_when_return_is_false_when_belonged_model_with_morph_one_saved()
    {
        Event::listen(
            'eloquent.morphOneSaving: ' . User::class,
            function () {
                return false;
            }
        );

        $user = User::create();
        $address = $user->address()->save(new Address);

        $this->assertNull($user->address);
    }

    /** @test */
    public function it_fires_morphOneUpdating_and_morphOneUpdated_when_belonged_model_with_morph_one_updated()
    {
        Event::fake();

        $user = User::create();
        $address = $user->address()->save(new Address);
        $user->address()->update(['city' => 'Aberdeen']);

        $this->assertNotNull($user->address);
        $this->assertEquals('Aberdeen', $user->address->city);

        Event::assertDispatched(
            'eloquent.morphOneUpdating: ' . User::class,
            function ($event, $callback) use ($user, $address) {
                return $callback[0]->is($user) && $callback[1]->is($address);
            }
        );
        Event::assertDispatched(
            'eloquent.morphOneUpdated: ' . User::class,
            function ($event, $callback) use ($user, $address) {
                return $callback[0]->is($user) && $callback[1]->is($address);
            }
        );
    }

    /** @test */
    public function it_fires_morphOneUpdating_and_stop_when_return_is_false_when_belonged_model_with_morph_one_updated()
    {
        Event::listen(
            'eloquent.morphOneUpdating: ' . User::class,
            function () {
                return false;
            }
        );

        $user = User::create();
        $address = $user->address()->save(new Address);
        $user->address()->update(['city' => 'Aberdeen']);

        $this->assertNotNull($user->address);
        $this->assertNull($user->address->city);
    }
}

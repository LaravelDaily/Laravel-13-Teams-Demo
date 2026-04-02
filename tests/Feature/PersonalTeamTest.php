<?php

use App\Enums\TeamRole;
use App\Models\Team;
use App\Models\User;

test('personal team is created on registration', function () {
    $this->post('/register', [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $user = User::where('email', 'test@example.com')->first();

    expect($user->personalTeam())->not->toBeNull();
    expect($user->personalTeam()->is_personal)->toBeTrue();
    expect($user->current_team_id)->toBe($user->personalTeam()->id);
});

test('personal teams cannot be deleted', function () {
    $user = User::factory()->create();
    $personalTeam = $user->currentTeam;

    expect($user->can('delete', $personalTeam))->toBeFalse();
});

test('personal teams cannot be left', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->post(route('team.leave'))
        ->assertForbidden();
});

test('cannot invite members to personal teams', function () {
    $user = User::factory()->create();
    $personalTeam = $user->currentTeam;

    expect($personalTeam->is_personal)->toBeTrue();
    expect($user->can('inviteMember', $personalTeam))->toBeFalse();
});

test('can invite members to organization teams', function () {
    $owner = User::factory()->create();
    $team = Team::factory()->create();
    $team->members()->attach($owner, ['role' => TeamRole::Owner->value]);

    expect($team->is_personal)->toBeFalse();
    expect($owner->can('inviteMember', $team))->toBeTrue();
});

test('fallback team is personal team after leaving org team', function () {
    $user = User::factory()->create();
    $personalTeam = $user->personalTeam();

    $orgTeam = Team::factory()->create();
    $orgTeam->members()->attach($user, ['role' => TeamRole::Member->value]);
    $user->switchTeam($orgTeam);

    $this->actingAs($user)
        ->post(route('team.leave'));

    expect($user->fresh()->current_team_id)->toBe($personalTeam->id);
});

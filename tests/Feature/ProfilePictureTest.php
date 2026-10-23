<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProfilePictureTest extends TestCase
{
    use RefreshDatabase;

    private function fakeImage(string $name = 'profile.png'): UploadedFile
    {
        $pngContent = base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg==');
        return UploadedFile::fake()->createWithContent($name, $pngContent);
    }

    public function test_student_registration_accepts_a_profile_picture(): void
    {
        Storage::fake('public');

        $response = $this->post(route('register'), [
            'name' => 'Test Student',
            'email' => 'student@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'student_number' => 'STU-TEST-001',
            'course' => 'Computer Science',
            'year_level' => '1st Year',
            'profile_picture' => $this->fakeImage('profile.png'),
        ]);

        $response->assertRedirect(route('dashboard'));
        $user = User::where('email', 'student@example.com')->firstOrFail();
        $this->assertDatabaseHas('students', ['user_id' => $user->id]);
        $this->assertNotNull($user->profile_picture);
        Storage::disk('public')->assertExists($user->profile_picture);
        $this->assertAuthenticatedAs($user);
    }

    public function test_user_can_upload_a_profile_picture_to_their_own_profile(): void
    {
        Storage::fake('public');
        $user = User::factory()->create(['role' => 'student']);

        $response = $this->actingAs($user)->put(route('users.update', $user), [
            'name' => $user->name,
            'email' => $user->email,
            'profile_picture' => $this->fakeImage('profile.png'),
        ]);

        $response->assertRedirect(route('dashboard'));
        $user->refresh();
        $this->assertNotNull($user->profile_picture);
        Storage::disk('public')->assertExists($user->profile_picture);
    }

    public function test_admin_can_upload_a_profile_picture_to_their_own_profile(): void
    {
        Storage::fake('public');
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->put(route('users.update', $admin), [
            'name' => 'Updated Admin Name',
            'email' => $admin->email,
            'profile_picture' => $this->fakeImage('admin.png'),
        ]);

        $response->assertRedirect(route('users.edit', $admin));
        $admin->refresh();
        $this->assertSame('Updated Admin Name', $admin->name);
        $this->assertNotNull($admin->profile_picture);
        Storage::disk('public')->assertExists($admin->profile_picture);
    }

    public function test_admin_can_upload_a_profile_picture_for_another_user(): void
    {
        Storage::fake('public');
        $admin = User::factory()->create(['role' => 'admin']);
        $user = User::factory()->create(['role' => 'dorm_manager']);

        $response = $this->actingAs($admin)->put(route('users.update', $user), [
            'name' => $user->name,
            'email' => $user->email,
            'role' => $user->role,
            'status' => 'active',
            'profile_picture' => $this->fakeImage('profile.png'),
        ]);

        $response->assertRedirect(route('users.index'));
        $user->refresh();
        $this->assertNotNull($user->profile_picture);
        Storage::disk('public')->assertExists($user->profile_picture);
    }

    public function test_admin_can_upload_a_profile_picture_when_creating_user(): void
    {
        Storage::fake('public');
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->post(route('users.store'), [
            'name' => 'New Manager',
            'email' => 'newmgr@example.com',
            'password' => 'password123',
            'role' => 'dorm_manager',
            'status' => 'active',
            'profile_picture' => $this->fakeImage('newmgr.png'),
        ]);

        $response->assertRedirect(route('users.index'));
        $created = User::where('email', 'newmgr@example.com')->firstOrFail();
        $this->assertNotNull($created->profile_picture);
        Storage::disk('public')->assertExists($created->profile_picture);
    }

    public function test_user_can_remove_their_profile_picture(): void
    {
        Storage::fake('public');
        $user = User::factory()->create([
            'role' => 'student',
            'profile_picture' => 'profile-pictures/existing.png',
        ]);
        Storage::disk('public')->put('profile-pictures/existing.png', 'content');

        $response = $this->actingAs($user)->put(route('users.update', $user), [
            'name' => $user->name,
            'email' => $user->email,
            'remove_profile_picture' => 1,
        ]);

        $response->assertRedirect(route('dashboard'));
        $user->refresh();
        $this->assertNull($user->profile_picture);
        Storage::disk('public')->assertMissing('profile-pictures/existing.png');
    }

    public function test_user_cannot_open_another_users_profile_edit_page(): void
    {
        $user = User::factory()->create(['role' => 'student']);
        $anotherUser = User::factory()->create(['role' => 'student']);

        $this->actingAs($user)
            ->get(route('users.edit', $anotherUser))
            ->assertForbidden();
    }
}
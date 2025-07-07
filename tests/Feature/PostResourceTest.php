<?php

use Eclipse\World\Filament\Clusters\World\Resources\PostResource;
use Eclipse\World\Filament\Clusters\World\Resources\PostResource\Pages\ListPosts;
use Eclipse\World\Models\Country;
use Eclipse\World\Models\Post;

use function Pest\Livewire\livewire;

beforeEach(function () {
    $this->setUpSuperAdmin();
});

test('unauthorized access can be prevented', function () {
    // Create regular user with no permissions
    $this->setUpCommonUser();

    // Create test data
    $country = Country::factory()->create();
    $post = Post::factory()->create(['country_id' => $country->id]);

    // View table
    $this->get(PostResource::getUrl())
        ->assertForbidden();

    // Add direct permission to view the table, since otherwise any other action below is not available even for testing
    $this->user->givePermissionTo('view_any_post');

    // Create post
    livewire(ListPosts::class)
        ->assertActionDisabled('create');

    // Edit post
    livewire(ListPosts::class)
        ->assertCanSeeTableRecords([$post])
        ->assertTableActionDisabled('edit', $post);

    // Delete post
    livewire(ListPosts::class)
        ->assertTableActionDisabled('delete', $post)
        ->assertTableBulkActionDisabled('delete');

    // Restore and force delete
    $post->delete();
    $this->assertSoftDeleted($post);

    livewire(ListPosts::class)
        ->assertTableActionDisabled('restore', $post)
        ->assertTableBulkActionDisabled('restore')
        ->assertTableActionDisabled('forceDelete', $post)
        ->assertTableBulkActionDisabled('forceDelete');
});

test('posts table can be displayed', function () {
    $this->get(PostResource::getUrl())
        ->assertSuccessful();
});

test('form validation works', function () {
    $country = Country::factory()->create();
    $component = livewire(ListPosts::class);

    // Test required fields
    $component->callAction('create')
        ->assertHasActionErrors([
            'country_id' => 'required',
            'code' => 'required',
            'name' => 'required',
        ]);

    // Test with valid data
    $validData = [
        'country_id' => $country->id,
        'code' => '1000',
        'name' => 'Ljubljana',
    ];

    $component->callAction('create', $validData)
        ->assertHasNoActionErrors();
});

test('new post can be created', function () {
    $country = Country::factory()->create();
    $data = [
        'country_id' => $country->id,
        'code' => '1000',
        'name' => 'Ljubljana',
    ];

    livewire(ListPosts::class)
        ->callAction('create', $data)
        ->assertHasNoActionErrors();

    $post = Post::where('code', $data['code'])
        ->where('country_id', $data['country_id'])
        ->first();
    
    expect($post)->toBeObject();

    foreach ($data as $key => $val) {
        expect($post->$key)->toEqual($val);
    }
});

test('existing post can be updated', function () {
    $country = Country::factory()->create();
    $post = Post::factory()->create([
        'country_id' => $country->id,
        'code' => '1000',
        'name' => 'Ljubljana',
    ]);

    $data = [
        'code' => '2000',
        'name' => 'Maribor',
    ];

    livewire(ListPosts::class)
        ->callTableAction('edit', $post, $data)
        ->assertHasNoTableActionErrors();

    $post->refresh();

    foreach ($data as $key => $val) {
        expect($post->$key)->toEqual($val);
    }
});

test('post can be deleted', function () {
    $country = Country::factory()->create();
    $post = Post::factory()->create(['country_id' => $country->id]);

    livewire(ListPosts::class)
        ->callTableAction('delete', $post)
        ->assertHasNoTableActionErrors();

    $this->assertSoftDeleted($post);
});

test('post can be restored', function () {
    $country = Country::factory()->create();
    $post = Post::factory()->create(['country_id' => $country->id]);
    $post->delete();

    $this->assertSoftDeleted($post);

    livewire(ListPosts::class)
        ->filterTable('trashed')
        ->assertTableActionExists('restore')
        ->assertTableActionEnabled('restore', $post)
        ->assertTableActionVisible('restore', $post)
        ->callTableAction('restore', $post)
        ->assertHasNoTableActionErrors();

    $this->assertNotSoftDeleted($post);
});

test('post can be force deleted', function () {
    $country = Country::factory()->create();
    $post = Post::factory()->create(['country_id' => $country->id]);

    $post->delete();
    $this->assertSoftDeleted($post);

    livewire(ListPosts::class)
        ->filterTable('trashed')
        ->assertTableActionExists('forceDelete')
        ->assertTableActionEnabled('forceDelete', $post)
        ->assertTableActionVisible('forceDelete', $post)
        ->callTableAction('forceDelete', $post)
        ->assertHasNoTableActionErrors();

    $this->assertModelMissing($post);
});

test('filtering by country works', function () {
    // Create two countries
    $country1 = Country::factory()->create(['id' => 'SI', 'name' => 'Slovenia']);
    $country2 = Country::factory()->create(['id' => 'HR', 'name' => 'Croatia']);

    // Create posts for each country
    $post1 = Post::factory()->create(['country_id' => $country1->id, 'name' => 'Ljubljana']);
    $post2 = Post::factory()->create(['country_id' => $country2->id, 'name' => 'Zagreb']);

    // Test filtering by first country
    livewire(ListPosts::class)
        ->filterTable('country_id', $country1->id)
        ->assertCanSeeTableRecords([$post1])
        ->assertCanNotSeeTableRecords([$post2]);

    // Test filtering by second country
    livewire(ListPosts::class)
        ->filterTable('country_id', $country2->id)
        ->assertCanSeeTableRecords([$post2])
        ->assertCanNotSeeTableRecords([$post1]);

    // Test removing filter shows all posts
    livewire(ListPosts::class)
        ->removeTableFilter('country_id')
        ->assertCanSeeTableRecords([$post1, $post2]);
});

test('cannot create duplicate country-post code combo', function () {
    $country = Country::factory()->create(['id' => 'SI']);
    
    // Create first post
    $firstPost = Post::factory()->create([
        'country_id' => $country->id,
        'code' => '1000',
        'name' => 'Ljubljana',
    ]);

    // Try to create duplicate country-code combination
    $duplicateData = [
        'country_id' => $country->id,
        'code' => '1000',
        'name' => 'Different Name',
    ];

    livewire(ListPosts::class)
        ->callAction('create', $duplicateData)
        ->assertHasActionErrors(['code']);

    // Verify only one post exists
    expect(Post::where('country_id', $country->id)->where('code', '1000')->count())
        ->toBe(1);
});

test('can create same post code for different countries', function () {
    // Create two countries
    $country1 = Country::factory()->create(['id' => 'SI']);
    $country2 = Country::factory()->create(['id' => 'HR']);

    // Create post with same code for first country
    $post1Data = [
        'country_id' => $country1->id,
        'code' => '1000',
        'name' => 'Ljubljana',
    ];

    livewire(ListPosts::class)
        ->callAction('create', $post1Data)
        ->assertHasNoActionErrors();

    // Create post with same code for second country (should work)
    $post2Data = [
        'country_id' => $country2->id,
        'code' => '1000',
        'name' => 'Zagreb',
    ];

    livewire(ListPosts::class)
        ->callAction('create', $post2Data)
        ->assertHasNoActionErrors();

    // Verify both posts exist
    expect(Post::where('code', '1000')->count())->toBe(2);
    expect(Post::where('country_id', $country1->id)->where('code', '1000')->count())->toBe(1);
    expect(Post::where('country_id', $country2->id)->where('code', '1000')->count())->toBe(1);
});

test('country flag is displayed in table', function () {
    $country = Country::factory()->create([
        'id' => 'SI',
        'name' => 'Slovenia',
        'flag' => '🇸🇮',
    ]);
    
    $post = Post::factory()->create([
        'country_id' => $country->id,
        'code' => '1000',
        'name' => 'Ljubljana',
    ]);

    livewire(ListPosts::class)
        ->assertCanSeeTableRecords([$post])
        ->assertSeeHtml('🇸🇮');
});

test('updating post respects unique constraint', function () {
    $country = Country::factory()->create(['id' => 'SI']);
    
    // Create two posts
    $post1 = Post::factory()->create([
        'country_id' => $country->id,
        'code' => '1000',
        'name' => 'Ljubljana',
    ]);
    
    $post2 = Post::factory()->create([
        'country_id' => $country->id,
        'code' => '2000',
        'name' => 'Maribor',
    ]);

    // Try to update post2 to have same code as post1
    livewire(ListPosts::class)
        ->callTableAction('edit', $post2, [
            'code' => '1000', // This should fail
            'name' => 'Updated Name',
        ])
        ->assertHasTableActionErrors(['code']);

    // Verify post2 wasn't updated
    $post2->refresh();
    expect($post2->code)->toBe('2000');
    expect($post2->name)->toBe('Maribor');
});

test('can update post with same code (no change)', function () {
    $country = Country::factory()->create(['id' => 'SI']);
    
    $post = Post::factory()->create([
        'country_id' => $country->id,
        'code' => '1000',
        'name' => 'Ljubljana',
    ]);

    // Update post name but keep same code (should work)
    livewire(ListPosts::class)
        ->callTableAction('edit', $post, [
            'code' => '1000', // Same code
            'name' => 'Updated Ljubljana',
        ])
        ->assertHasNoTableActionErrors();

    $post->refresh();
    expect($post->code)->toBe('1000');
    expect($post->name)->toBe('Updated Ljubljana');
}); 
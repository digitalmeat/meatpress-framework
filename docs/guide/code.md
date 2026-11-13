# Hooks, post types and taxonomies

The framework discovers classes with PHP attributes in `app/` and `themes/meat-theme/app/`: no registration file. After adding a class, clear the cache:

```bash
docker compose exec app php artisan discovery:clear
```

## Hooks

```php
namespace App\Cms\Hooks;

use Meatpress\Attributes\Action;
use Meatpress\Attributes\Filter;

class Newsletter
{
    #[Action('save_post_event', priority: 20)]
    public function syncEvent(int $postId): void
    {
        // …
    }

    #[Filter('excerpt_length')]
    public function excerptLength(int $length): int
    {
        return 30;
    }
}
```

Generate one with `php artisan meatpress:make:action SyncLeads --hook=save_post`.

## Post types and taxonomies

```bash
php artisan meatpress:make:post-type Project
php artisan meatpress:make:taxonomy ProjectCategory
```

```php
namespace App\Cms\PostTypes;

use Meatpress\Attributes\PostType;
use Meatpress\Attributes\PostType\HasArchive;
use Meatpress\Attributes\PostType\MenuIcon;
use Meatpress\Attributes\PostType\PublicPostType;
use Meatpress\Attributes\PostType\Supports;

#[PostType]
#[PublicPostType]
#[HasArchive]
#[Supports(['title', 'thumbnail'])]
#[MenuIcon('dashicons-portfolio')]
class Project {}
```

## Queries

`Meatpress\Support\Facades\PostQuery` builds `WP_Query` arguments fluently (it replaces the Themosis `App\Wordpress` builder):

```php
use Meatpress\Support\Facades\PostQuery;

$projects = PostQuery::select()
    ->postType('project')
    ->latest()
    ->take(6)
    ->get();
```

## Routes

The template hierarchy answers most requests. Use `Route::wp()` in `routes/web.php` when a page needs a controller, middleware or a named route:

```php
// The page whose slug is "contacto"
Route::wp('page', 'contacto', [ContactController::class, 'show']);

// Every page using the "landing" template (declare before a generic Route::wp('page'))
Route::wp('template', 'landing', [LandingController::class, 'show']);
```

Plain Laravel routes (`Route::get`) work too, for APIs or pages outside WordPress.

The framework's own source is the reference: [`meatpress-framework`](https://github.com/digitalmeat/meatpress-framework).

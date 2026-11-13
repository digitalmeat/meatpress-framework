<?php

declare(strict_types=1);

use Illuminate\Container\Container;
use Illuminate\Support\Facades\Facade;
use Meatpress\Attributes\Contracts\ConfiguresArguments;
use Meatpress\Attributes\Taxonomy;
use Meatpress\Attributes\Taxonomy\AllowHierarchy;
use Meatpress\Attributes\Taxonomy\Args;
use Meatpress\Attributes\Taxonomy\CheckedOntop;
use Meatpress\Attributes\Taxonomy\DefaultTerm;
use Meatpress\Attributes\Taxonomy\Description;
use Meatpress\Attributes\Taxonomy\Exclusive;
use Meatpress\Attributes\Taxonomy\Hierarchical;
use Meatpress\Attributes\Taxonomy\Label;
use Meatpress\Attributes\Taxonomy\MetaBoxCb;
use Meatpress\Attributes\Taxonomy\MetaBoxSanitizeCb;
use Meatpress\Attributes\Taxonomy\ObjectType;
use Meatpress\Attributes\Taxonomy\PublicTaxonomy;
use Meatpress\Attributes\Taxonomy\QueryVar;
use Meatpress\Attributes\Taxonomy\RestBase;
use Meatpress\Attributes\Taxonomy\RestControllerClass;
use Meatpress\Attributes\Taxonomy\RestNamespace;
use Meatpress\Attributes\Taxonomy\Rewrite;
use Meatpress\Attributes\Taxonomy\ShowAdminColumn;
use Meatpress\Attributes\Taxonomy\ShowInNavMenus;
use Meatpress\Attributes\Taxonomy\ShowInQuickEdit;
use Meatpress\Attributes\Taxonomy\ShowInRest;
use Meatpress\Attributes\Taxonomy\ShowTagcloud;
use Meatpress\Attributes\Taxonomy\ShowUI;
use Meatpress\Attributes\Taxonomy\Sort;
use Meatpress\Attributes\Taxonomy\UpdateCountCallback;
use Meatpress\Taxonomy\Domain\Contracts\TaxonomyAttributeInterface;
use Mockery as m;

// Test class implementing the Taxonomy interface
#[ShowTagcloud]
#[ShowInQuickEdit]
#[ShowAdminColumn]
#[DefaultTerm('Default Term')]
#[Sort]
#[Args(['orderby' => 'name'])]
#[CheckedOntop]
#[Exclusive]
#[AllowHierarchy]
#[PublicTaxonomy]
#[Label('Test Taxonomy')]
#[Description('This is a test taxonomy description')]
#[ShowUI]
#[ShowInNavMenus]
#[QueryVar('test_query_var')]
#[Rewrite(['slug' => 'test-slug'])]
#[RestNamespace('test/v1')]
#[RestControllerClass('WP_REST_Terms_Controller')]
#[RestBase('test-terms')]
#[ShowInRest]
#[Hierarchical]
#[ObjectType(['post', 'page'])]
class TestTaxonomy implements TaxonomyAttributeInterface
{
    public array $attributeArgs = [];

    public function setArg(string $key, mixed $value): void
    {
        $this->attributeArgs[$key] = $value;
    }

    public function getArg(string $key, mixed $default = null): mixed
    {
        return $this->attributeArgs[$key] ?? $default;
    }

    protected string $slug = 'test-taxonomy';

    protected array $objectType = ['post'];

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function getName(): string
    {
        return 'Test Taxonomy';
    }

    public function getPluralName(): string
    {
        return 'Test Taxonomies';
    }

    public function getLabels(): array
    {
        return [
            'name' => 'Test Taxonomies',
            'singular_name' => 'Test Taxonomy',
        ];
    }

    public function getObjectType(): array|string
    {
        return $this->objectType;
    }

    #[MetaBoxCb]
    public function metaBoxCallback(): void
    {
        // Meta box callback implementation
    }

    #[MetaBoxSanitizeCb]
    public function sanitizeMetaBox($terms): array
    {
        return (array) $terms;
    }

    #[UpdateCountCallback]
    public function updateCount($terms, $taxonomy): void
    {
        // Update count implementation
    }

    public function withArgs(): array
    {
        return [
            'public' => true,
        ];
    }

    public function getArgs(): array
    {
        return array_merge(
            $this->attributeArgs,
            $this->withArgs(),
            [
                'labels' => $this->getLabels(),
            ]
        );
    }
}

beforeAll(function (): void {
    // Create and configure the container
    $app = new Container;
    Facade::setFacadeApplication($app);
});

afterAll(function (): void {
    m::close();
    Facade::clearResolvedInstances();
    Facade::setFacadeApplication(null);
});

// Helper function to test simple boolean attributes
function testBooleanAttribute(string $attributeName, string $argName): void
{
    test(sprintf('%s attribute sets %s parameter', $attributeName, $argName), function () use ($argName): void {
        $taxonomy = new TestTaxonomy;

        // Simulate the discovery process by manually processing attributes
        $reflectionClass = new ReflectionClass($taxonomy);
        foreach ($reflectionClass->getAttributes() as $attribute) {
            $attributeInstance = $attribute->newInstance();
            if ($attributeInstance instanceof ConfiguresArguments) {
                $taxonomy->attributeArgs = $attributeInstance->configureArguments($taxonomy->attributeArgs);
            }
        }

        expect($taxonomy->attributeArgs[$argName])->toBeTrue();
    });
}

// Helper function to test string/value attributes
function testValueAttribute(string $attributeName, string $argName, mixed $expectedValue): void
{
    test(sprintf('%s attribute sets %s parameter', $attributeName, $argName), function () use ($argName, $expectedValue): void {
        $taxonomy = new TestTaxonomy;

        // Simulate the discovery process by manually processing attributes
        $reflectionClass = new ReflectionClass($taxonomy);
        foreach ($reflectionClass->getAttributes() as $attribute) {
            $attributeInstance = $attribute->newInstance();
            if ($attributeInstance instanceof ConfiguresArguments) {
                $taxonomy->attributeArgs = $attributeInstance->configureArguments($taxonomy->attributeArgs);
            }
        }

        expect($taxonomy->attributeArgs[$argName])->toBe($expectedValue);
    });
}

// Helper function to test method attributes
function testMethodAttribute(string $attributeName, string $argName, string $methodName, string $attributeClass): void
{
    test(sprintf('%s attribute sets %s parameter', $attributeName, $argName), function () use ($argName, $methodName, $attributeClass): void {
        $taxonomy = new TestTaxonomy;

        // Reset attributeArgs to avoid interference
        $taxonomy->attributeArgs = [];

        // Get methods with the specific attribute
        $reflectionClass = new ReflectionClass($taxonomy);
        $methods = $reflectionClass->getMethods(ReflectionMethod::IS_PUBLIC);

        foreach ($methods as $method) {
            $attributes = $method->getAttributes($attributeClass);
            foreach ($attributes as $attribute) {
                $attributeInstance = $attribute->newInstance();
                $taxonomy->attributeArgs = $attributeInstance->configureArguments($taxonomy->attributeArgs, [$taxonomy, $method->getName()]);
            }
        }

        expect($taxonomy->attributeArgs[$argName])->toBeArray()
            ->toHaveCount(2)
            ->toHaveKey(0)
            ->toHaveKey(1);

        expect($taxonomy->attributeArgs[$argName][0])->toBeInstanceOf(TestTaxonomy::class);
        expect($taxonomy->attributeArgs[$argName][1])->toBe($methodName);
    });
}

// Group 1: Boolean Attributes Tests
testBooleanAttribute('ShowTagcloud', 'show_tagcloud');
testBooleanAttribute('ShowInQuickEdit', 'show_in_quick_edit');
testBooleanAttribute('ShowAdminColumn', 'show_admin_column');
testBooleanAttribute('Sort', 'sort');
testBooleanAttribute('CheckedOntop', 'checked_ontop');
testBooleanAttribute('Exclusive', 'exclusive');
testBooleanAttribute('AllowHierarchy', 'allow_hierarchy');
testBooleanAttribute('PublicTaxonomy', 'public');
testBooleanAttribute('ShowUI', 'show_ui');
testBooleanAttribute('ShowInNavMenus', 'show_in_nav_menus');
testBooleanAttribute('ShowInRest', 'show_in_rest');
testBooleanAttribute('Hierarchical', 'hierarchical');

// Group 2: Value Attributes Tests
testValueAttribute('DefaultTerm', 'default_term', 'Default Term');
testValueAttribute('Args', 'args', ['orderby' => 'name']);
testValueAttribute('ObjectType', 'object_type', ['post', 'page']);
testValueAttribute('Label', 'label', 'Test Taxonomy');
testValueAttribute('Description', 'description', 'This is a test taxonomy description');
testValueAttribute('QueryVar', 'query_var', 'test_query_var');
testValueAttribute('Rewrite', 'rewrite', ['slug' => 'test-slug']);
testValueAttribute('RestNamespace', 'rest_namespace', 'test/v1');
testValueAttribute('RestControllerClass', 'rest_controller_class', 'WP_REST_Terms_Controller');
testValueAttribute('RestBase', 'rest_base', 'test-terms');

// Group 3: Method Attributes Tests
testMethodAttribute('MetaBoxCb', 'meta_box_cb', 'metaBoxCallback', MetaBoxCb::class);
testMethodAttribute('MetaBoxSanitizeCb', 'meta_box_sanitize_cb', 'sanitizeMetaBox', MetaBoxSanitizeCb::class);
testMethodAttribute('UpdateCountCallback', 'update_count_callback', 'updateCount', UpdateCountCallback::class);

// Test the final getArgs method
test('getArgs method merges attribute args with withArgs and labels', function (): void {
    $taxonomy = new TestTaxonomy;

    // Simulate the discovery process by manually processing attributes
    $reflectionClass = new ReflectionClass($taxonomy);
    foreach ($reflectionClass->getAttributes() as $attribute) {
        $attributeInstance = $attribute->newInstance();
        if ($attributeInstance instanceof ConfiguresArguments) {
            $taxonomy->attributeArgs = $attributeInstance->configureArguments($taxonomy->attributeArgs);
        }
    }

    $args = $taxonomy->getArgs();

    // Check that all expected keys exist
    $expectedKeys = [
        'show_tagcloud', 'show_in_quick_edit', 'show_admin_column',
        'default_term', 'sort', 'args', 'checked_ontop', 'exclusive',
        'allow_hierarchy', 'public', 'label', 'description', 'show_ui',
        'show_in_nav_menus', 'query_var', 'rewrite', 'rest_namespace',
        'rest_controller_class', 'rest_base', 'show_in_rest',
        'hierarchical', 'labels',
    ];

    foreach ($expectedKeys as $key) {
        expect($args)->toHaveKey($key);
    }

    // Check specific values
    expect($args['public'])->toBeTrue();
    expect($args['labels'])->toBeArray()
        ->toHaveKey('name')
        ->toHaveKey('singular_name');
});

test('resolveSlug returns the explicit slug, or the class name in kebab-case', function (): void {
    expect((new Taxonomy('theme-presse'))->resolveSlug('App\\Cms\\Taxonomies\\ThemeDePresse'))->toBe('theme-presse')
        ->and((new Taxonomy)->resolveSlug('App\\Cms\\Taxonomies\\ThemeDePresse'))->toBe('theme-de-presse');
});

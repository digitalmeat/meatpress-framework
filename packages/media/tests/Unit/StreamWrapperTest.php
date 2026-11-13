<?php

declare(strict_types=1);

use Meatpress\Media\Domain\Models\MediaConfig;
use Meatpress\Media\Infrastructure\WordPress\StreamWrapper;
use Meatpress\Media\Tests\Fixtures\CountingStorage;
use Meatpress\Media\Tests\Fixtures\TemporaryStorage;

/*
|--------------------------------------------------------------------------
| WordPress's own file functions, on meatpress://, against a real disk
|--------------------------------------------------------------------------
|
| The wrapper is what lets WordPress, Gutenberg, ACF, WooCommerce and every
| import write to any disk without knowing it. Each test uses the functions
| WordPress calls, and reads the result back from the disk.
*/

beforeEach(function (): void {
    $this->disk = new TemporaryStorage;
    StreamWrapper::register($this->disk->storage, new MediaConfig('https://media.test'));
});

afterEach(function (): void {
    StreamWrapper::unregister();
});

it('writes a file to the disk when the stream is closed, and reads it back', function (): void {
    expect(file_put_contents('meatpress://2026/10/photo.jpg', 'bytes'))->toBe(5)
        ->and(file_get_contents($this->disk->root.'/2026/10/photo.jpg'))->toBe('bytes')
        ->and(file_get_contents('meatpress://2026/10/photo.jpg'))->toBe('bytes')
        ->and(filesize('meatpress://2026/10/photo.jpg'))->toBe(5)
        ->and(file_exists('meatpress://2026/10/photo.jpg'))->toBeTrue()
        ->and(is_file('meatpress://2026/10/photo.jpg'))->toBeTrue();
});

it('reads and writes with streams like fopen, fread, fwrite, fseek and fgets', function (): void {
    $handle = fopen('meatpress://notes.txt', 'w');
    fwrite($handle, "one\ntwo\nthree\n");
    fclose($handle);

    $handle = fopen('meatpress://notes.txt', 'r');
    expect(fgets($handle))->toBe("one\n");
    fseek($handle, 4);
    expect(fread($handle, 3))->toBe('two')
        ->and(ftell($handle))->toBe(7)
        ->and(feof($handle))->toBeFalse();
    fseek($handle, 0, SEEK_END);
    fread($handle, 1);
    expect(feof($handle))->toBeTrue();
    fclose($handle);
});

it('appends to an existing file', function (): void {
    file_put_contents('meatpress://log.txt', 'a');
    file_put_contents('meatpress://log.txt', 'b', FILE_APPEND);

    expect(file_get_contents($this->disk->root.'/log.txt'))->toBe('ab');
});

it('refuses to create a file that exists with mode x, and to read one that does not', function (): void {
    file_put_contents('meatpress://there.txt', '1');

    $quietly = static function (callable $call): mixed {
        set_error_handler(static fn (): bool => true);

        try {
            return $call();
        } finally {
            restore_error_handler();
        }
    };

    expect($quietly(static fn () => fopen('meatpress://there.txt', 'x')))->toBeFalse()
        ->and($quietly(static fn () => fopen('meatpress://nowhere.txt', 'r')))->toBeFalse()
        ->and($quietly(static fn () => file_get_contents('meatpress://nowhere.txt')))->toBeFalse()
        ->and(file_exists('meatpress://nowhere.txt'))->toBeFalse();
});

it('copies a local file in, a file out, and between two paths', function (): void {
    $local = tempnam(sys_get_temp_dir(), 'pm');
    file_put_contents($local, 'local bytes');

    expect(copy($local, 'meatpress://in/local.txt'))->toBeTrue()
        ->and(file_get_contents($this->disk->root.'/in/local.txt'))->toBe('local bytes')
        ->and(copy('meatpress://in/local.txt', 'meatpress://in/copy.txt'))->toBeTrue()
        ->and(file_get_contents($this->disk->root.'/in/copy.txt'))->toBe('local bytes');

    $out = $local.'.out';
    expect(copy('meatpress://in/copy.txt', $out))->toBeTrue()
        ->and(file_get_contents($out))->toBe('local bytes');

    unlink($local);
    unlink($out);
});

it('renames inside the wrapper, and deletes', function (): void {
    file_put_contents('meatpress://a.txt', 'x');

    expect(rename('meatpress://a.txt', 'meatpress://dir/b.txt'))->toBeTrue()
        ->and(file_exists($this->disk->root.'/a.txt'))->toBeFalse()
        ->and(file_get_contents($this->disk->root.'/dir/b.txt'))->toBe('x')
        ->and(unlink('meatpress://dir/b.txt'))->toBeTrue()
        ->and(file_exists($this->disk->root.'/dir/b.txt'))->toBeFalse()
        ->and(file_exists('meatpress://dir/b.txt'))->toBeFalse();
});

it('answers for directories without asking the disk, so a page render costs no call', function (): void {
    expect(is_dir('meatpress://2026/10'))->toBeTrue()
        ->and(file_exists('meatpress://2026/10'))->toBeTrue()
        ->and(mkdir('meatpress://2026/11', 0755, true))->toBeTrue()
        ->and(is_dir('meatpress://'))->toBeTrue()
        ->and(is_file('meatpress://2026/10'))->toBeFalse()
        ->and(is_writable('meatpress://2026/10'))->toBeTrue();
});

it('lists a directory with scandir and opendir, and removes it', function (): void {
    file_put_contents('meatpress://2026/10/a.jpg', '1');
    file_put_contents('meatpress://2026/10/b.jpg', '2');
    file_put_contents('meatpress://2026/10/sub/c.jpg', '3');

    $names = scandir('meatpress://2026/10');
    sort($names);
    expect($names)->toBe(['.', '..', 'a.jpg', 'b.jpg', 'sub']);

    $handle = opendir('meatpress://2026/10');
    $seen = [];
    while (($entry = readdir($handle)) !== false) {
        $seen[] = $entry;
    }
    closedir($handle);
    expect(array_diff($seen, ['.', '..']))->toHaveCount(3)
        ->and(rmdir('meatpress://2026/10'))->toBeTrue()
        ->and(file_exists($this->disk->root.'/2026/10/a.jpg'))->toBeFalse();
});

it('tolerates what WordPress does after a write: chmod and touch', function (): void {
    file_put_contents('meatpress://f.txt', 'x');

    expect(@chmod('meatpress://f.txt', 0644))->toBeTrue()
        ->and(@touch('meatpress://f.txt'))->toBeTrue();
});

it('gives the same stat for a file as the disk knows', function (): void {
    file_put_contents('meatpress://s.txt', '12345');
    $stat = stat('meatpress://s.txt');

    expect($stat['size'])->toBe(5)
        ->and($stat['mode'] & 0170000)->toBe(0100000)
        ->and(abs($stat['mtime'] - time()))->toBeLessThan(5)
        ->and(filemtime('meatpress://s.txt'))->toBe($stat['mtime']);
});

it('does not ask the disk twice about the same path in a request, and forgets after a write', function (): void {
    file_put_contents('meatpress://c.txt', '1');
    $counting = new CountingStorage($this->disk->storage);
    StreamWrapper::register($counting, new MediaConfig('https://media.test'));

    file_exists('meatpress://c.txt');
    file_exists('meatpress://c.txt');
    filesize('meatpress://c.txt');
    expect($counting->existsCalls)->toBe(1);

    file_put_contents('meatpress://c.txt', '22');
    clearstatcache();
    expect(filesize('meatpress://c.txt'))->toBe(2);
});

it('refuses a path that climbs out of the library', function (): void {
    set_error_handler(static fn (): bool => true);
    $written = file_put_contents('meatpress://../escape.txt', 'x');
    restore_error_handler();

    expect($written)->toBeFalse()
        ->and(file_exists(dirname($this->disk->root).'/escape.txt'))->toBeFalse();
});

it('writes with the cache control of the configuration', function (): void {
    $recording = new CountingStorage($this->disk->storage);
    StreamWrapper::register($recording, new MediaConfig('https://media.test', [], 'public, max-age=60'));

    file_put_contents('meatpress://h.txt', 'x');

    expect($recording->lastWriteOptions)->toBe(['CacheControl' => 'public, max-age=60']);
});

it('is registered once under the meatpress scheme, and unregisters', function (): void {
    expect(in_array('meatpress', stream_get_wrappers(), true))->toBeTrue();

    StreamWrapper::unregister();

    expect(in_array('meatpress', stream_get_wrappers(), true))->toBeFalse();

    StreamWrapper::register($this->disk->storage, new MediaConfig('https://media.test'));
    StreamWrapper::register($this->disk->storage, new MediaConfig('https://media.test'));

    expect(in_array('meatpress', stream_get_wrappers(), true))->toBeTrue();
});

it('does not read a file back from the disk in the request that wrote it, nor ask its size', function (): void {
    $counting = new CountingStorage($this->disk->storage);
    StreamWrapper::register($counting, new MediaConfig('https://media.test'));

    file_put_contents('meatpress://2026/10/photo.jpg', 'original bytes');

    expect(file_get_contents('meatpress://2026/10/photo.jpg'))->toBe('original bytes')
        ->and(file_get_contents('meatpress://2026/10/photo.jpg'))->toBe('original bytes')
        ->and(filesize('meatpress://2026/10/photo.jpg'))->toBe(14)
        ->and(file_exists('meatpress://2026/10/photo.jpg'))->toBeTrue()
        ->and($counting->readCalls)->toBe(0)
        ->and($counting->statCalls)->toBe(0)
        ->and($counting->existsCalls)->toBe(0);
});

it('reads a file the disk already had once, however many times the request reads it', function (): void {
    $this->disk->storage->write('2026/10/old.jpg', 'old bytes');
    $counting = new CountingStorage($this->disk->storage);
    StreamWrapper::register($counting, new MediaConfig('https://media.test'));

    foreach (range(1, 3) as $ignored) {
        expect(file_get_contents('meatpress://2026/10/old.jpg'))->toBe('old bytes');
    }

    expect($counting->readCalls)->toBe(1);
});

it('serves the new bytes after a file is rewritten, deleted or renamed', function (): void {
    file_put_contents('meatpress://a.jpg', 'one');
    file_get_contents('meatpress://a.jpg');
    file_put_contents('meatpress://a.jpg', 'two');

    expect(file_get_contents('meatpress://a.jpg'))->toBe('two');

    rename('meatpress://a.jpg', 'meatpress://b.jpg');
    expect(file_get_contents('meatpress://b.jpg'))->toBe('two')
        ->and(file_exists('meatpress://a.jpg'))->toBeFalse();

    unlink('meatpress://b.jpg');
    expect(file_exists('meatpress://b.jpg'))->toBeFalse();
});

it('does not keep a file bigger than the limit on the local disk, and still reads it', function (): void {
    $counting = new CountingStorage($this->disk->storage);
    StreamWrapper::register($counting, new MediaConfig('https://media.test'));
    StreamWrapper::limitLocalCopies(10);

    file_put_contents('meatpress://big.bin', str_repeat('x', 100));

    expect(file_get_contents('meatpress://big.bin'))->toBe(str_repeat('x', 100))
        ->and($counting->readCalls)->toBe(1);

    StreamWrapper::limitLocalCopies(StreamWrapper::DEFAULT_LOCAL_COPY_LIMIT);
});

it('removes its local copies when it unregisters', function (): void {
    file_put_contents('meatpress://tmp-check.jpg', 'x');
    $before = glob(sys_get_temp_dir().'/meatpress-media-copy-*');

    StreamWrapper::unregister();

    expect($before)->not->toBe([])
        ->and(glob(sys_get_temp_dir().'/meatpress-media-copy-*'))->toBe([]);
});

it('deletes without asking the disk first, and says false for a file it found missing', function (): void {
    $this->disk->storage->write('gone.jpg', 'x');
    $counting = new CountingStorage($this->disk->storage);
    StreamWrapper::register($counting, new MediaConfig('https://media.test'));

    expect(unlink('meatpress://gone.jpg'))->toBeTrue()
        ->and($counting->existsCalls)->toBe(0)
        ->and(file_exists('meatpress://gone.jpg'))->toBeFalse()
        ->and(@unlink('meatpress://gone.jpg'))->toBeFalse();
});

it('takes meatpress://uploads as the root of the library, so WordPress can trim the slashes of its base', function (): void {
    file_put_contents('meatpress://uploads/2026/10/a.jpg', 'x');

    expect(file_get_contents($this->disk->root.'/2026/10/a.jpg'))->toBe('x')
        ->and(file_exists('meatpress://uploads/2026/10/a.jpg'))->toBeTrue()
        ->and(file_exists('meatpress://2026/10/a.jpg'))->toBeTrue()
        ->and(StreamWrapper::keyOf('meatpress://uploads'))->toBe('')
        ->and(StreamWrapper::keyOf('meatpress://uploads/sites/3/b.jpg'))->toBe('sites/3/b.jpg')
        ->and(StreamWrapper::keyOf('meatpress://uploadsX/b.jpg'))->toBe('uploadsX/b.jpg')
        ->and(is_dir('meatpress://uploads'))->toBeTrue();
});

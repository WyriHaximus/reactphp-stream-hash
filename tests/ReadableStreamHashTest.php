<?php declare(strict_types=1);

namespace WyriHaximus\React\Tests\Stream\Hash;

use PHPUnit\Framework\TestCase;
use React\EventLoop\Factory;
use React\Stream\ThroughStream;
use WyriHaximus\React\Stream\Hash\ReadableStreamHash;
use function Clue\React\Block\await;
use function React\Promise\Stream\buffer;

/**
 * @internal
 */
final class ReadableStreamHashTest extends TestCase
{
    /**
     * @dataProvider WyriHaximus\React\Tests\Stream\Hash\DataProvider::provideData
     */
    public function testHash(string $algo, string $data): void
    {
        $catchedHash = null;
        $catchedRawHash = null;
        $catchedAlgo = null;
        $loop = Factory::create();
        $throughStream = new ThroughStream();
        $stream = new ReadableStreamHash($throughStream, $algo);
        $stream->on('hash', function ($hash, $algo) use (&$catchedHash, &$catchedAlgo): void {
            $catchedHash = $hash;
            $catchedAlgo = $algo;
        });
        $stream->on('hash_raw', function ($hash, $algo) use (&$catchedRawHash): void {
            $catchedRawHash = $hash;
        });
        $loop->addTimer(0.001, function () use ($throughStream, $data): void {
            $throughStream->write($data);
            $throughStream->end();
        });
        self::assertSame($data, await(buffer($stream), $loop));
        self::assertSame($algo, $catchedAlgo);
        self::assertSame(\hash($algo, $data), $catchedHash);
        self::assertSame(\hash($algo, $data, true), $catchedRawHash);
    }

    /**
     * @dataProvider WyriHaximus\React\Tests\Stream\Hash\DataProvider::provideData
     */
    public function testHashHMAC(string $algo, string $data): void
    {
        $key = 'foo.bar';
        $catchedHash = null;
        $catchedRawHash = null;
        $catchedAlgo = null;
        $loop = Factory::create();
        $throughStream = new ThroughStream();
        $stream = new ReadableStreamHash($throughStream, $algo, $key);
        $stream->on('hash', function ($hash, $algo) use (&$catchedHash, &$catchedAlgo): void {
            $catchedHash = $hash;
            $catchedAlgo = $algo;
        });
        $stream->on('hash_raw', function ($hash, $algo) use (&$catchedRawHash): void {
            $catchedRawHash = $hash;
        });
        $loop->addTimer(0.001, function () use ($throughStream, $data): void {
            $throughStream->write($data);
            $throughStream->end();
        });
        self::assertSame($data, await(buffer($stream), $loop));
        self::assertSame($algo, $catchedAlgo);
        self::assertSame(\hash_hmac($algo, $data, $key), $catchedHash);
        self::assertSame(\hash_hmac($algo, $data, $key, true), $catchedRawHash);
    }
}

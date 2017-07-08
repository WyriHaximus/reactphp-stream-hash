<?php declare(strict_types=1);

namespace WyriHaximus\React\Tests\Stream\Hash;

use PHPUnit\Framework\TestCase;
use React\EventLoop\Factory;
use React\Stream\ThroughStream;
use WyriHaximus\React\Stream\Hash\ReadableStreamHash;
use function Clue\React\Block\await;
use function React\Promise\Stream\buffer;

final class ReadableStreamHashTest extends TestCase
{
    public function provideData()
    {
        foreach (hash_algos() as $algo) {
            yield [$algo, 'a'];
            yield [$algo, 'abc'];
            yield [$algo, str_pad('a', 1337)];
            yield [$algo, str_pad('a', 100000)];
        }
    }

    /**
     * @dataProvider provideData
     */
    public function testHash(string $algo, string $data)
    {
        $catchedHash = null;
        $catchedRawHash = null;
        $catchedAlgo = null;
        $loop = Factory::create();
        $throughStream = new ThroughStream();
        $stream = new ReadableStreamHash($throughStream, $algo);
        $stream->on('hash', function ($hash, $algo) use (&$catchedHash, &$catchedAlgo) {
            $catchedHash = $hash;
            $catchedAlgo = $algo;
        });
        $stream->on('hash_raw', function ($hash, $algo) use (&$catchedRawHash) {
            $catchedRawHash = $hash;
        });
        $loop->addTimer(0.001, function () use ($throughStream, $data) {
            $throughStream->write($data);
            $throughStream->end();
        });
        self::assertSame($data, await(buffer($stream), $loop));
        self::assertSame($algo, $catchedAlgo);
        self::assertSame(hash($algo, $data), $catchedHash);
        self::assertSame(hash($algo, $data, true), $catchedRawHash);
    }

    /**
     * @dataProvider provideData
     */
    public function testHashHMAC(string $algo, string $data)
    {
        $key = 'foo.bar';
        $catchedHash = null;
        $catchedRawHash = null;
        $catchedAlgo = null;
        $loop = Factory::create();
        $throughStream = new ThroughStream();
        $stream = new ReadableStreamHash($throughStream, $algo, $key);
        $stream->on('hash', function ($hash, $algo) use (&$catchedHash, &$catchedAlgo) {
            $catchedHash = $hash;
            $catchedAlgo = $algo;
        });
        $stream->on('hash_raw', function ($hash, $algo) use (&$catchedRawHash) {
            $catchedRawHash = $hash;
        });
        $loop->addTimer(0.001, function () use ($throughStream, $data) {
            $throughStream->write($data);
            $throughStream->end();
        });
        self::assertSame($data, await(buffer($stream), $loop));
        self::assertSame($algo, $catchedAlgo);
        self::assertSame(hash_hmac($algo, $data, $key), $catchedHash);
        self::assertSame(hash_hmac($algo, $data, $key, true), $catchedRawHash);
    }
}

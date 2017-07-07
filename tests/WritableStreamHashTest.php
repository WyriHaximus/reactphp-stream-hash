<?php declare(strict_types=1);

namespace WyriHaximus\React\Tests\Stream\Hash;

use PHPUnit\Framework\TestCase;
use React\EventLoop\Factory;
use React\Stream\ThroughStream;
use WyriHaximus\React\Stream\Hash\WritableStreamHash;
use function Clue\React\Block\await;
use function React\Promise\Stream\buffer;

final class WritableStreamHashTest extends TestCase
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
        $catchedAlgo = null;
        $loop = Factory::create();
        $throughStream = new ThroughStream();
        $stream = new WritableStreamHash($throughStream, $algo);
        $stream->on('hash', function ($hash, $algo) use (&$catchedHash, &$catchedAlgo) {
            $catchedHash = $hash;
            $catchedAlgo = $algo;
        });
        $loop->addTimer(0.001, function () use ($stream, $data) {
            $stream->write($data);
            $stream->end();
        });
        self::assertSame($data, await(buffer($throughStream), $loop));
        self::assertSame($algo, $catchedAlgo);
        self::assertSame(hash($algo, $data), $catchedHash);
    }
}

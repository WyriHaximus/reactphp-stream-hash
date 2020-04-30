<?php declare(strict_types=1);

namespace WyriHaximus\React\Tests\Stream\Hash;

final class DataProvider
{
    private const INCOMPATIBLE_ALGOS = [
        'adler32',
        'crc32',
        'crc32b',
        'crc32c',
        'fnv132',
        'fnv1a32',
        'fnv164',
        'fnv1a64',
        'joaat',
    ];

    public function provideData()
    {
        foreach (\hash_algos() as $algo) if (!\in_array($algo, self::INCOMPATIBLE_ALGOS)) {
            yield [$algo, 'a'];
            yield [$algo, 'abc'];
            yield [$algo, 'abcdefg'];
            yield [$algo, 'abcdefghij'];
            yield [$algo, 'abcdefghijklm'];
            yield [$algo, 'abcdefghijklmnop'];
            yield [$algo, 'abcdefghijklmnopqrst'];
            yield [$algo, 'abcdefghijklmnopqrstuvw'];
            yield [$algo, 'abcdefghijklmnopqrstuvwxyz'];
            foreach (\range(128, 256) as $size) {
                yield [$algo, \str_pad('a', $size)];
            }
            yield [$algo, \str_pad('a', 1337)];
            foreach (range(1, 128) as $size) {
                yield [$algo, \bin2hex(random_bytes($size))];
            }
        }
    }
}

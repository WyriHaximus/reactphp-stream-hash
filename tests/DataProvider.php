<?php declare(strict_types=1);

namespace WyriHaximus\React\Tests\Stream\Hash;

final class DataProvider
{
    public function provideData()
    {
        foreach (hash_algos() as $algo) {
            yield [$algo, 'a'];
            yield [$algo, 'abc'];
            yield [$algo, 'abcdefg'];
            yield [$algo, 'abcdefghij'];
            yield [$algo, 'abcdefghijklm'];
            yield [$algo, 'abcdefghijklmnop'];
            yield [$algo, 'abcdefghijklmnopqrst'];
            yield [$algo, 'abcdefghijklmnopqrstuvw'];
            yield [$algo, 'abcdefghijklmnopqrstuvwxyz'];
            foreach (range(128, 256) as $size) {
                yield [$algo, str_pad('a', $size)];
            }
            yield [$algo, str_pad('a', 1337)];
        }
    }
}

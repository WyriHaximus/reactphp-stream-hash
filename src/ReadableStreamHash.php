<?php declare(strict_types=1);

namespace WyriHaximus\React\Stream\Hash;

use Evenement\EventEmitter;
use React\Stream\ReadableStreamInterface;
use React\Stream\Util;
use React\Stream\WritableStreamInterface;

final class ReadableStreamHash extends EventEmitter implements ReadableStreamInterface
{
    /**
     * @var WritableStreamInterface
     */
    private $stream;

    /**
     * @var resource
     */
    private $context;

    /**
     * @param ReadableStreamInterface $stream
     * @param string                  $algo
     * @param int                     $options
     * @param string                  $key
     */
    public function __construct(ReadableStreamInterface $stream, string $algo, int $options = 0, string $key = '')
    {
        $this->stream = $stream;
        $this->context = hash_init($algo, $options, $key);
        $this->stream->on('data', function ($data) {
            hash_update($this->context, $data);
            $this->emit('data', [$data]);
        });
        $this->stream->once('close', function () use ($algo) {
            $this->emit('close');
            $this->emit('hash', [
                hash_final($this->context),
                $algo,
            ]);
        });
        Util::forwardEvents($stream, $this, ['error', 'end']);
    }

    public function isReadable()
    {
        return $this->stream->isReadable();
    }

    public function pause()
    {
        return $this->stream->pause();
    }

    public function resume()
    {
        return $this->stream->resume();
    }

    public function pipe(WritableStreamInterface $dest, array $options = [])
    {
        return $this->stream->pipe($dest, $options);
    }

    public function close()
    {
        $this->stream->close();
    }
}

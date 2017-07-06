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
     * WritableStreamHash constructor.
     * @param WritableStreamInterface $stream
     */
    public function __construct(ReadableStreamInterface $stream, string $hash, int $options = 0, string $key = '')
    {
        $this->stream = $stream;
        $this->context = hash_init($hash, $options, $key);
        $this->stream->on('data', function ($data) {
            $this->emit('data', [$data]);
            hash_update($this->context, $data);
        });
        $this->stream->on('close', function () {
            $this->emit('close');
            $this->emit('hash', [
                hash_final($this->context),
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

<?php declare(strict_types=1);

namespace WyriHaximus\React\Stream\Hash;

use Evenement\EventEmitter;
use React\Stream\WritableStreamInterface;

final class WritableStreamHash extends EventEmitter implements WritableStreamInterface
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
    public function __construct(WritableStreamInterface $stream, string $hash, int $options = 0, string $key = '')
    {
        $this->stream = $stream;
        $this->context = hash_init($hash, $options, $key);
        $this->stream->once('close', function () {
            $this->emit('hash', [
                hash_final($this->context),
            ]);
        });
    }

    public function isWritable()
    {
        return $this->stream->isWritable();
    }

    public function write($data)
    {
        hash_update($this->context, $data);

        return $this->stream->write($data);
    }

    public function end($data = null)
    {
        if ($data !== null) {
            hash_update($this->context, $data);
        }
        $this->stream->end($data);
    }

    public function close()
    {
        $this->stream->close();
    }
}

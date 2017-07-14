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
     * @param string                  $algo
     * @param string|null             $key
     */
    public function __construct(WritableStreamInterface $stream, string $algo, string $key = null)
    {
        $this->stream = $stream;
        $options = [$algo];
        if ($key !== null && strlen($key) > 0) {
            $options[] = HASH_HMAC;
            $options[] = $key;
        }
        $this->context = hash_init(...$options);
        $this->stream->once('close', function () use ($algo) {
            $hash = hash_final($this->context, true);
            if (count($this->listeners('hash')) > 0) {
                $this->emit('hash', [
                    bin2hex($hash),
                    $algo,
                ]);
            }
            if (count($this->listeners('hash_raw')) > 0) {
                $this->emit('hash_raw', [
                    $hash,
                    $algo,
                ]);
            }
            $this->emit('close');
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

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
     * @param string|null             $key
     */
    public function __construct(ReadableStreamInterface $stream, string $algo, string $key = null)
    {
        $this->stream = $stream;
        $options = [$algo];
        if ($key !== null && strlen($key) > 0) {
            $options[] = HASH_HMAC;
            $options[] = $key;
        }
        $this->context = hash_init(...$options);
        $this->stream->on('data', function ($data) {
            hash_update($this->context, $data);
            $this->emit('data', [$data]);
        });
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

<?php

namespace Cyve\AudioEncoder;

class Mp3Encoder
{
    /**
     * @param string $source
     * @param $destination
     * @param array $options
     * @return int
     */
    public function encode(string $source, $destination, $options = []): int
    {
        if (!is_file($source)) {
            throw new \RuntimeException(sprintf('The file `%s` does not exists', $source));
        }

        $bitrate = (int) ($options['bitrate'] ?? 128);

        switch($this->getMimetype($source)){
            case 'audio/wav':
            case 'audio/wave':
            case 'audio/x-wav':
            case 'audio/x-wave':
                exec('lame -b '.$bitrate.' '.escapeshellarg($source).' '.escapeshellarg($destination), $output, $status);
                break;
            case 'audio/mpeg':
                exec('lame -b '.$bitrate.' '.escapeshellarg($source).' '.escapeshellarg($destination), $output, $status);
                break;
            case 'audio/mp4':
                exec('mplayer -ao pcm:file=tmp.wav '.escapeshellarg($source), $output, $status);
                exec('lame -b '.$bitrate.' tmp.wav '.escapeshellarg($destination), $output, $status);
                exec('rm -f tmp.wav', $output, $status);
                break;
            case 'audio/wma':
            case 'audio/x-ms-wma':
                exec('mplayer -vo null -vc dummy -af resample=44100 -ao pcm -ao pcm:file=tmp.wav '.escapeshellarg($source), $output, $status);
                exec('lame -b '.$bitrate.' tmp.wav '.escapeshellarg($destination), $output, $status);
                exec('rm -f tmp.wav', $output, $status);
                break;
            case 'audio/flac':
            case 'audio/x-flac':
                exec('flac -cd '.escapeshellarg($source).' | lame -b '.$bitrate.' - '.escapeshellarg($destination), $output, $status);
                break;
            default:
                throw new \RuntimeException('Unsupported MIME type');
        }

        return $status;
    }

    /**
     * @param string $filepath
     * @return string|null
     */
    private function getMimeType(string $filepath): ?string
    {
        if (!$finfo = new \finfo(FILEINFO_MIME)) {
            return null;
        }

        return strstr($finfo->file($filepath), ';', true);
    }
}

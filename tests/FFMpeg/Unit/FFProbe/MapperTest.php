<?php

namespace Tests\FFMpeg\Unit\FFProbe;

use FFMpeg\FFProbe;
use FFMpeg\FFProbe\DataMapping\Format;
use FFMpeg\FFProbe\DataMapping\Stream;
use FFMpeg\FFProbe\DataMapping\StreamCollection;
use FFMpeg\FFProbe\Mapper;
use Tests\FFMpeg\Unit\TestCase;

class MapperTest extends TestCase
{
    /**
     * @dataProvider provideMappings
     */
    public function testMap($type, $data, $expected)
    {
        $mapper = new Mapper();
        $this->assertEquals($expected, $mapper->map($type, $data));
    }

    public function testMapInvalidArgument()
    {
        $this->expectException('\FFMpeg\Exception\InvalidArgumentException');
        $mapper = new Mapper();
        $mapper->map('cool type', 'data');
    }

    public function provideMappings()
    {
        $format = json_decode(file_get_contents(__DIR__.'/../../fixtures/ffprobe/show_format.json'), true);
        $streams = json_decode(file_get_contents(__DIR__.'/../../fixtures/ffprobe/show_streams.json'), true);

        return [
            [FFProbe::TYPE_FORMAT, $format, new Format($format['format'])],
            [FFProbe::TYPE_STREAMS, $streams, new StreamCollection(array_map(function ($streamData) {
                return new Stream($streamData);
            }, $streams['streams']))],
        ];
    }
}

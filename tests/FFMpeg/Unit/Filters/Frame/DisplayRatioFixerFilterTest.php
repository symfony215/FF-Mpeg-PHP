<?php

namespace Tests\FFMpeg\Unit\Filters\Frame;

use FFMpeg\Coordinate\TimeCode;
use FFMpeg\FFProbe\DataMapping\Stream;
use FFMpeg\FFProbe\DataMapping\StreamCollection;
use FFMpeg\Filters\Frame\DisplayRatioFixerFilter;
use FFMpeg\Media\Frame;
use Tests\FFMpeg\Unit\TestCase;

class DisplayRatioFixerFilterTest extends TestCase
{
    public function testApply()
    {
        $stream = new Stream(['codec_type' => 'video', 'width' => 960, 'height' => 720]);
        $streams = new StreamCollection([$stream]);

        $video = $this->getVideoMock(__FILE__);
        $video->expects($this->once())
                ->method('getStreams')
                ->will($this->returnValue($streams));

        $frame = new Frame($video, $this->getFFMpegDriverMock(), $this->getFFProbeMock(), new TimeCode(0, 0, 0, 0));
        $filter = new DisplayRatioFixerFilter();
        $this->assertEquals(['-s', '960x720'], $filter->apply($frame));
    }
}

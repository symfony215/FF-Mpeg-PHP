<?php

namespace Tests\FFMpeg\Unit\Filters\Video;

use FFMpeg\Filters\Video\ResizeFilter;
use FFMpeg\Filters\Video\VideoFilters;
use Tests\FFMpeg\Unit\TestCase;

class VideoFiltersTest extends TestCase
{
    /**
     * @dataProvider provideResizeOptions
     */
    public function testResize($mode, $forceStandards)
    {
        $capturedFilter = null;

        $video = $this->getVideoMock();
        $filters = new VideoFilters($video);
        $dimension = $this->getDimensionMock();

        $video->expects($this->once())
            ->method('addFilter')
            ->with($this->isInstanceOf('FFMpeg\Filters\Video\ResizeFilter'))
            ->will($this->returnCallback(function ($filter) use (&$capturedFilter) {
                $capturedFilter = $filter;
            }));

        $filters->resize($dimension, $mode, $forceStandards);

        $this->assertSame($mode, $capturedFilter->getMode());
        $this->assertSame($forceStandards, $capturedFilter->areStandardsForced());
        $this->assertSame($dimension, $capturedFilter->getDimension());
    }

    public function provideResizeOptions()
    {
        return [
            [ResizeFilter::RESIZEMODE_FIT, true],
            [ResizeFilter::RESIZEMODE_SCALE_WIDTH, true],
            [ResizeFilter::RESIZEMODE_SCALE_HEIGHT, false],
            [ResizeFilter::RESIZEMODE_INSET, false],
        ];
    }

    public function testResample()
    {
        $capturedFilter = null;

        $video = $this->getVideoMock();
        $filters = new VideoFilters($video);
        $framerate = $this->getFramerateMock();
        $gop = 42;

        $video->expects($this->once())
            ->method('addFilter')
            ->with($this->isInstanceOf('FFMpeg\Filters\Video\FrameRateFilter'))
            ->will($this->returnCallback(function ($filter) use (&$capturedFilter) {
                $capturedFilter = $filter;
            }));

        $filters->framerate($framerate, $gop);

        $this->assertSame($framerate, $capturedFilter->getFramerate());
        $this->assertSame($gop, $capturedFilter->getGOP());
    }

    public function testSynchronize()
    {
        $video = $this->getVideoMock();
        $filters = new VideoFilters($video);

        $video->expects($this->once())
            ->method('addFilter')
            ->with($this->isInstanceOf('FFMpeg\Filters\Video\SynchronizeFilter'));

        $filters->synchronize();
    }
}

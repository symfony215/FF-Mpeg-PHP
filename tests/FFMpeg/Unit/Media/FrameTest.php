<?php

namespace Tests\FFMpeg\Unit\Media;

use FFMpeg\Media\Frame;

class FrameTest extends AbstractMediaTestCase
{
    public function testGetTimeCode()
    {
        $driver = $this->getFFMpegDriverMock();
        $ffprobe = $this->getFFProbeMock();
        $timecode = $this->getTimeCodeMock();

        $frame = new Frame($this->getVideoMock(__FILE__), $driver, $ffprobe, $timecode);
        $this->assertSame($timecode, $frame->getTimeCode());
    }

    public function testFiltersReturnFilters()
    {
        $driver = $this->getFFMpegDriverMock();
        $ffprobe = $this->getFFProbeMock();
        $timecode = $this->getTimeCodeMock();

        $frame = new Frame($this->getVideoMock(__FILE__), $driver, $ffprobe, $timecode);
        $this->assertInstanceOf('FFMpeg\Filters\Frame\FrameFilters', $frame->filters());
    }

    public function testAddFiltersAddsAFilter()
    {
        $driver = $this->getFFMpegDriverMock();
        $ffprobe = $this->getFFProbeMock();
        $timecode = $this->getTimeCodeMock();

        $filters = $this->getMockBuilder('FFMpeg\Filters\FiltersCollection')
            ->disableOriginalConstructor()
            ->getMock();

        $filter = $this->getMockBuilder('FFMpeg\Filters\Frame\FrameFilterInterface')->getMock();

        $filters->expects($this->once())
            ->method('add')
            ->with($filter);

        $frame = new Frame($this->getVideoMock(__FILE__), $driver, $ffprobe, $timecode);
        $frame->setFiltersCollection($filters);
        $frame->addFilter($filter);
    }

    /**
     * @dataProvider provideSaveOptions
     */
    public function testSave($accurate, $base64, $commands)
    {
        $driver = $this->getFFMpegDriverMock();
        $ffprobe = $this->getFFProbeMock();
        $timecode = $this->getTimeCodeMock();
        $timecode->expects($this->once())
            ->method('__toString')
            ->will($this->returnValue('timecode'));

        $pathfile = '/target/destination';

        if (!$base64) {
            array_push($commands, $pathfile);
        }

        $driver->expects($this->once())
            ->method('command')
            ->with($commands);

        if (!$base64) {
            $frame = new Frame($this->getVideoMock(__FILE__), $driver, $ffprobe, $timecode);
            $this->assertSame($frame, $frame->save($pathfile, $accurate, $base64));
        } else {
            $frame = new Frame($this->getVideoMock(__FILE__), $driver, $ffprobe, $timecode);
            $frame->save($pathfile, $accurate, $base64);
        }
    }

    public function provideSaveOptions()
    {
        return [
            [false, false, [
                '-y', '-ss', 'timecode',
                '-i', __FILE__,
                '-vframes', '1',
                '-f', 'image2', ],
            ],
            [true, false, [
                '-y', '-i', __FILE__,
                '-vframes', '1', '-ss', 'timecode',
                '-f', 'image2', ],
            ],
            [false, true, [
                '-y', '-ss', 'timecode',
                '-i', __FILE__,
                '-vframes', '1',
                '-f', 'image2pipe', '-', ],
            ],
            [true, true, [
                '-y', '-i', __FILE__,
                '-vframes', '1', '-ss', 'timecode',
                '-f', 'image2pipe', '-', ],
            ],
        ];
    }
}

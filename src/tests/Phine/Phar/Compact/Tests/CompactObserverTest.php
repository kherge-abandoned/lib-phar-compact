<?php

namespace Phine\Phar\Compact\Tests;

use Phine\Phar\Builder;
use Phine\Phar\Compact\CompactObserver;
use Phine\Phar\Compact\Test\AbstractTestCase;
use Phine\Test\Property;

/**
 * Performs functional testing on `CompactObserver`
 *
 * @see Phine\Phar\Compact\CompactObserver
 *
 * @author Kevin Herrera <kevin@herrera.io>
 */
class CompactObserverTest extends AbstractTestCase
{
    /**
     * The observer instance being tested.
     *
     * @var CompactObserver
     */
    private $observer;

    /**
     * Make sure that we can compact file contents.
     */
    public function testAddFile()
    {
        $file = $this->temp->createDir() . DIRECTORY_SEPARATOR . 'test.json';

        file_put_contents(
            $file,
            <<<CONTENTS
{
    "key": "value"
}

CONTENTS
        );

        $this->builder->addFile($file, 'test.json');

        $this->assertEquals(
            '{"key":"value"}',
            file_get_contents($this->phar['test.json']),
            'The file contents should be compacted.'
        );
    }

    /**
     * Make sure that we can compact string contents.
     */
    public function testAddString()
    {
        $this->builder->addFromString(
            'test.json',
            <<<CONTENTS
{
    "key": "value"
}

CONTENTS
        );

        $this->assertEquals(
            '{"key":"value"}',
            file_get_contents($this->phar['test.json']),
            'The string contents should be compacted.'
        );
    }

    /**
     * Make sure that the temporary files are cleaned up.
     */
    public function testCleanUp()
    {
        $file = tempnam(sys_get_temp_dir(), 'compact');

        // inject a temporary file
        Property::set($this->observer, 'files', array($file));

        $this->observer->cleanUp();

        $this->assertFileNotExists(
            $file,
            'The temporary file should have been deleted.'
        );
    }

    /**
     * Make sure that temporary files are cleaned up on destruction.
     */
    public function testDestruct()
    {
        $file = tempnam(sys_get_temp_dir(), 'compact');

        // inject a temporary file
        Property::set($this->observer, 'files', array($file));

        $this->observer->__destruct();

        $this->assertFileNotExists(
            $file,
            'The temporary file should have been deleted.'
        );
    }

    /**
     * Make sure that an exception is thrown for unsupported subjects.
     */
    public function testUnsupportedSubject()
    {
        $this->setExpectedException(
            'Phine\\Compact\\Exception\\CompactException',
            'The subject "Phine\\Phar\\Subject\\Builder\\SetStub" is not supported.'
        );

        $this->observer->receiveUpdate(
            $this->builder->getSubject(Builder::SET_STUB)
        );
    }

    /**
     * Creates a new observer instance for testing.
     */
    protected function setUp()
    {
        parent::setUp();

        $this->observer = new CompactObserver($this->collection);
        $this->builder->observe(Builder::ADD_FILE, $this->observer);
        $this->builder->observe(Builder::ADD_STRING, $this->observer);
    }
}

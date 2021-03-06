<?php

namespace Phine\Phar\Compact;

use Phine\Compact\CollectionInterface;
use Phine\Compact\CompactInterface;
use Phine\Compact\Exception\CompactException;
use Phine\Compact\Exception\FileException;
use Phine\Exception\Exception;
use Phine\Observer\ObserverInterface;
use Phine\Observer\SubjectInterface;
use Phine\Phar\Subject\AbstractSubject;
use Phine\Phar\Subject\Arguments;
use Phine\Phar\Subject\Builder\AddFile;
use Phine\Phar\Subject\Builder\AddString;

/**
 * Observes builder events for compacting file contents.
 *
 * @author Kevin Herrera <kevin@herrera.io>
 */
class CompactObserver implements ObserverInterface
{
    /**
     * The collection of compactors.
     *
     * @var CollectionInterface
     */
    private $collection;

    /**
     * The temporary files.
     *
     * @var array
     */
    private $files = array();

    /**
     * Sets the collection of compactors.
     *
     * @param CollectionInterface $collection The collection of compactors.
     */
    public function __construct(CollectionInterface $collection)
    {
        $this->collection = $collection;
    }

    /**
     * Cleans up the temporary files.
     *
     * @see Phine\Phar\Compact\CompactObserver::cleanUp
     */
    public function __destruct()
    {
        $this->cleanUp();
    }

    /**
     * Cleans up the temporary files.
     */
    public function cleanUp()
    {
        foreach ($this->files as $file) {
            unlink($file);
        }

        $this->files = array();
    }

    /**
     * {@inheritDoc}
     */
    public function receiveUpdate(SubjectInterface $subject)
    {
        /** @var AbstractSubject $subject */
        $arguments = $subject->getArguments();

        if ($subject instanceof AddFile) {
            $this->addFile($arguments);
        } elseif ($subject instanceof AddString) {
            $this->addString($arguments);
        } else {
            throw CompactException::createUsingFormat(
                'The subject "%s" is not supported.',
                get_class($subject)
            );
        }
    }

    /**
     * Compact the contents of a file.
     *
     * @param Arguments $arguments The subject arguments.
     *
     * @throws Exception
     * @throws FileException If the temporary file could not be written.
     */
    private function addFile(Arguments $arguments)
    {
        if (null !== ($compactor = $this->getCompactor($arguments['file']))) {
            $file = @tempnam(sys_get_temp_dir(), 'compact');

            if (false === $file) {
                // @codeCoverageIgnoreStart
                throw FileException::createUsingLastError();
            }
            // @codeCoverageIgnoreEnd

            $contents = $compactor->compactFile($arguments['file']);

            if (false === @file_put_contents($file, $contents)) {
                // @codeCoverageIgnoreStart
                throw FileException::createUsingLastError();
            }
            // @codeCoverageIgnoreEnd

            $arguments['file'] = $file;
        }
    }

    /**
     * Compact the string contents of a file.
     *
     * @param Arguments $arguments The subject arguments.
     */
    private function addString(Arguments $arguments)
    {
        if (null !== ($compactor = $this->getCompactor($arguments['local']))) {
            $arguments['contents'] = $compactor->compactContents(
                $arguments['contents']
            );
        }
    }

    /**
     * Returns the compactor for the file name.
     *
     * @param string $file The file name.
     *
     * @return CompactInterface The supported compactor.
     */
    private function getCompactor($file)
    {
        return $this->collection->getCompactor(
            pathinfo($file, PATHINFO_EXTENSION)
        );
    }
}

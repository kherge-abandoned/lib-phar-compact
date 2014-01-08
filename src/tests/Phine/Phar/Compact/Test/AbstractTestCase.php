<?php

namespace Phine\Phar\Compact\Test;

use Phar;
use Phine\Compact\Collection;
use Phine\Compact\Json;
use Phine\Phar\Builder;
use Phine\Test\Temp;
use PHPUnit_Framework_TestCase as TestCase;

/**
 * Simplifies the process of testing observers.
 *
 * @author Kevin Herrera <kevin@herrera.io>
 */
class AbstractTestCase extends TestCase
{
    /**
     * The archive builder.
     *
     * @var Builder
     */
    protected $builder;

    /**
     * The collection of compactors.
     *
     * @var Collection
     */
    protected $collection;

    /**
     * The archive.
     *
     * @var Phar
     */
    protected $phar;

    /**
     * The temporary file manager.
     *
     * @var Temp
     */
    protected $temp;

    /**
     * Sets up the test case.
     */
    protected function setUp()
    {
        $this->temp = new Temp();
        $this->builder = Builder::create(
            $this->temp->createDir() . DIRECTORY_SEPARATOR . 'test.phar'
        );
        $this->phar = $this->builder->getPhar();
        $this->collection = new Collection();
        $this->collection->addCompactor(new Json());
    }

    /**
     * Clean up the temporary files.
     */
    protected function tearDown()
    {
        $this->temp->purgePaths();
    }
}

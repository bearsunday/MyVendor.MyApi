<?php

namespace MyVendor\MyApi;

abstract class AbstractDatabaseTestCase extends \PHPUnit_Extensions_Database_TestCase
{
    /**
     * @var \BEAR\Resource\ResourceInterface
     */
    protected $resource;

    protected function setUp()
    {
        parent::setUp();
        $this->resource = $GLOBALS['RESOURCE'];
    }

    protected function getSetUpOperation()
    {
        return \PHPUnit_Extensions_Database_Operation_Factory::CLEAN_INSERT(true);
    }

    protected function getTearDownOperation()
    {
        return \PHPUnit_Extensions_Database_Operation_Factory::TRUNCATE();
    }

    public function getDataSet()
    {
        $path = dirname((new \ReflectionClass($this))->getFileName()) . '/fixtures';
        $dataSets = [];
        foreach (glob(rtrim($path, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . '*.xml') as $file) {
            $dataSets[] = $this->createMySQLXMLDataSet($file);
        }
        $compositeDataset = new \PHPUnit_Extensions_Database_DataSet_CompositeDataSet;
        foreach($dataSets as $dataSet) {
            $compositeDataset->addDataSet($dataSet);
        }

        return $compositeDataset;
    }

    public function getConnection()
    {
        $pdo = new \PDO("{$GLOBALS['DB_DSN']};dbname={$GLOBALS['DB_DBNAME']};charset=UTF8", $GLOBALS['DB_USER']);

        return $this->createDefaultDBConnection($pdo);
    }

    public function getRowCount()
    {
        return $this->getConnection()->getRowCount(static::TABLE);
    }

    public function getPdo()
    {
        return $this->getConnection()->getConnection();
    }
}

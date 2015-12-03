<?php

namespace MyVendor\MyApi\Resource\App;

use MyVendor\MyApi\AbstractDatabaseTestCase;

class PostTest extends AbstractDatabaseTestCase
{
    const URI = 'app://self/post';

    const TABLE = 'post';

    public function testPost()
    {
        $query = [
            'title' =>  'test_title',
            'body' => 'test_body'
        ];
        $ro = $this->resource->post->uri(self::URI)->withQuery($query)->eager->request();
        $this->assertSame(201, $ro->code);
        $this->assertArrayHasKey('Location', $ro->headers);
        return $ro->headers['Location'];

        $queryTable = $this->getConnection()->createQueryTable(self::TABLE,
            'SELECT title, body FROM ' . self::TABLE);
        $xml = __DIR__ . '/expected_post';
        $expectedTable = $this->createFlatXmlDataSet($xml)->getTable(self::TABLE);
        $this->assertTablesEqual($expectedTable, $queryTable);
    }

    /**
     * @depends testPost
     */
    public function testOnGet($loacation)
    {
        $uri = sprintf('app://self%s', $loacation);
        $ro = $this->resource->get->uri($uri)->eager->request();
        $this->assertSame(200, $ro->code);
        $this->assertSame('test_title', $ro->body['title']);
        $this->assertSame('test_body', $ro->body['body']);
    }

    /**
     * @depends testPost
     */
    public function testOnDelete($loacation)
    {
        $uri = sprintf('app://self%s', $loacation);
        $count = $this->getRowCount($loacation);
        $ro = $this->resource->delete->uri($uri)->eager->request();
        $this->assertSame(200, $ro->code);
        $decrement = $count - (int) $this->getRowCount();
        $this->assertSame(1, $decrement);
    }
}


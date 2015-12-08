<?php

namespace MyVendor\MyApi\Resource\App;

use BEAR\RepositoryModule\Annotation\Cacheable;
use BEAR\RepositoryModule\Annotation\Refresh;
use BEAR\Resource\Code;
use BEAR\Resource\ResourceObject;
use Koriym\Now\NowInject;
use Koriym\QueryLocator\QueryLocatorInject;
use Koriym\QueryLocator\QueryLocatorInterface;
use Ray\AuraSqlModule\AuraSqlInject;
use Ray\AuraSqlModule\AuraSqlInsertInject;

/**
 * @Cacheable
 */
class Comment extends ResourceObject
{
    use AuraSqlInject;
    use AuraSqlInsertInject;
    use QueryLocatorInject;
    use NowInject;

    public function onGet($post_id)
    {
        $bind = ['post_id' => $post_id];
        $this->body = $this->pdo->fetchAssoc($this->query['comment'], $bind);

        return $this;
    }

    /**
     * @Refresh(uri="app://self/post?id={post_id}")
     */
    public function onPost($post_id, $body)
    {
        $this->insert
            ->into('comment')
            ->cols([
                'post_id',
                'body'
            ])
            ->bindValues([
                'post_id' => $post_id,
                'body' => $body
            ])
            ->set('updated_at', $this->now);
        $sth = $this->pdo->prepare($this->insert->getStatement());
        $sth->execute($this->insert->getBindValues());
        $this->code = Code::CREATED;

        $this->code = 201;
        $id = 1;
        $this->headers['Location'] = "/comment?id={$id}";

        return $this;
    }
}

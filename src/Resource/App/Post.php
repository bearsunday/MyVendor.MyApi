<?php

namespace MyVendor\MyApi\Resource\App;

use BEAR\RepositoryModule\Annotation\Cacheable;
use BEAR\Resource\Annotation\Embed;
use BEAR\Resource\Annotation\Link;
use BEAR\Resource\Code;
use BEAR\Resource\Exception\ResourceNotFoundException;
use BEAR\Resource\Exception\ServerErrorException;
use BEAR\Resource\ResourceObject;
use Koriym\Now\NowInject;
use Koriym\QueryLocator\QueryLocatorInject;
use Ray\AuraSqlModule\AuraSqlDeleteInject;
use Ray\AuraSqlModule\AuraSqlInject;
use Ray\AuraSqlModule\AuraSqlInsertInject;

/**
 * @Cacheable
 */
class Post extends ResourceObject
{
    use AuraSqlInject;
    use AuraSqlInsertInject;
    use AuraSqlDeleteInject;
    use NowInject;
    use QueryLocatorInject;

    /**
     * @Embed(rel="comment", src="app://self/comment?post_id={id}")
     * @Link(rel="comment", href="app://self/comment?post_id={id}")
     */
    public function onGet($id)
    {
        $post = $this->pdo->fetchOne($this->query['post'], ['id' => $id]);
        if (! $post) {
            throw new ResourceNotFoundException;
        }
        $this->body += $post;

        return $this;
    }

    public function onPost($title, $body)
    {
        $this->insert
            ->into('post')
            ->cols([
                'title',
                'body',
                'updated_at'
            ])
            ->bindValues([
                'title' => $title,
                'body' => $body,
                'updated_at' => $this->now
            ]);
        $sth = $this->pdo->prepare($this->insert->getStatement());
        $sth->execute($this->insert->getBindValues());
        $this->code = 201;
        $id = $this->pdo->lastInsertId();
        $this->headers['Location'] = "/post?id={$id}";

        return $this;
    }

    public function onDelete($id)
    {
        $this->delete
            ->from('post')
            ->where('id = :id')
            ->bindValue('id', $id);
        $sth = $this->pdo->prepare($this->delete->getStatement());
        $result = $sth->execute($this->delete->getBindValues());
        if (! $result) {
            $msg = sprintf('%s:%s', __METHOD__ , $id);
            throw new ServerErrorException($msg);
        }
        $this->code = 200;

        return $this;
    }
}

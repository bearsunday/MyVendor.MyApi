<?php

namespace MyVendor\MyApi\Resource\App;

use BEAR\RepositoryModule\Annotation\Cacheable;
use BEAR\Resource\Annotation\Embed;
use BEAR\Resource\Annotation\Link;
use BEAR\Resource\Exception\ResourceNotFoundException;
use BEAR\Resource\Exception\ServerErrorException;
use BEAR\Resource\ResourceObject;
use Ray\AuraSqlModule\AuraSqlInject;

/**
 * @Cacheable
 */
class Post extends ResourceObject
{
    use AuraSqlInject;

    /**
     * @Embed(rel="comment", src="app://self/comment?post_id={id}")
     * @Link(rel="comment", href="app://self/comment?post_id={id}")
     */
    public function onGet($id)
    {
        $sql  = 'SELECT * FROM post WHERE id = :id';
        $bind = ['id' => $id];
        $post =  $this->pdo->fetchOne($sql, $bind);
        if (! $post) {
            throw new ResourceNotFoundException;
        }
        $this->body += $post;

        return $this;
    }

    public function onPost($title, $body)
    {
        $sql = 'INSERT INTO post (title, body) VALUES(:title, :body)';
        $statement = $this->pdo->prepare($sql);
        $bind = [
            'title' => $title,
            'body' => $body
        ];
        $statement->execute($bind);
        $id = $this->pdo->lastInsertId();

        $this->code = 201;
        $this->headers['Location'] = "/post?id={$id}";

        return $this;
    }

    public function onDelete($id)
    {
        $sql  = 'DELETE FROM post WHERE id = :id';
        $statement = $this->pdo->prepare($sql);
        $bind = ['id' => $id];
        $result = $statement->execute($bind);
        if (! $result) {
            $msg = sprintf('%s:%s', __METHOD__ , $id);
            throw new ServerErrorException($msg);
        }
        $this->code = 200;

        return $this;
    }
}

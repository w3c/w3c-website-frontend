<?php

declare(strict_types=1);

namespace App\Query\CraftCMS\Blog;

use App\Service\CraftCMS;
use Strata\Data\Query\GraphQLMutation;

class CreateComment extends GraphQLMutation
{

    public function getRequiredDataProviderClass(): string
    {
        return CraftCMS::class;
    }

    public function __construct(
        int $id,
        string $name,
        ?string $email,
        string $comment,
        ?int $parentId = null
    ) {
        $this->setGraphQLFromFile(__DIR__ . '/../graphql/blog/create-comment.graphql')
            ->setRootPropertyPath('[saveComment]')
            ->addVariable('postId', $id)
            ->addVariable('name', $name)
            ->addVariable('email', $email)
            ->addVariable('comment', $comment)
            ->addVariable('parentId', $parentId)
        ;
    }
}

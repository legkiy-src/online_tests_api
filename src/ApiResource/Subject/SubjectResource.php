<?php

declare(strict_types=1);

namespace App\ApiResource\Subject;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Patch;
use App\State\Subject\GetSubjectCollectionProvider;
//use App\State\Subject\SubjectProcessor;

#[ApiResource(
    operations: [
        /*new Get(
            uriTemplate: '/subjects/{id}',
            requirements: ['id' => '\d+'],
            provider: GetSubjectProvider ::class
        ),*/
        new GetCollection(
            uriTemplate: '/subjects',
            provider: GetSubjectCollectionProvider::class
        ),
        /*new Post(
            uriTemplate: '/subjects',
            input: CreateSubjectDto::class,
            output: SubjectOutputDto::class,
            processor: SubjectProcessor::class
        ),
        new Put(
            uriTemplate: '/subjects/{id}',
            requirements: ['id' => '\d+'],
            input: UpdateSubjectDto::class,
            provider: SubjectProvider::class,
            processor: SubjectProcessor::class
        ),
        new Patch(
            uriTemplate: '/subjects/{id}',
            requirements: ['id' => '\d+'],
            input: UpdateSubjectDto::class,
            provider: SubjectProvider::class,
            processor: SubjectProcessor::class
        ),
        new Delete(
            uriTemplate: '/subjects/{id}',
            requirements: ['id' => '\d+'],
            provider: SubjectProvider::class,
            processor: SubjectProcessor::class
        ),*/
    ]
)]
class SubjectResource
{
}

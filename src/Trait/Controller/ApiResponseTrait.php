<?php

namespace App\Trait\Controller;

use DateTime;
use Doctrine\Common\Annotations\AnnotationReader;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactory;
use Symfony\Component\Serializer\Mapping\Loader\AnnotationLoader;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\AbstractObjectNormalizer;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

trait ApiResponseTrait
{
    private function notFoundIfNull($data = null): ?Response
    {
        if (null === $data) {
            return new Response('', 404);
        }

        return null;
    }

    private function normalizedJsonResponse($data, $groups = ['api_data']): Response
    {
        if (null === $data) {
            return new Response('', 404);
        }

        return $this->jsonResponse($this->normalize($data, $groups));
    }

    private function jsonResponse(?array $data = []): Response
    {
        $output = [];

        if (!isset($data['response'])) {
            $output['response'] = $data;
        } else {
            $output = $data;
        }

        if (count($output['response']) === 0) {
            return new Response('', 404);
        }

        return $this->json($output);
    }

    private function normalize($data, array $groups = ['api_data'])
    {
        $dateCallback = function ($innerObject) {
            return $innerObject instanceof DateTime ? $innerObject->format('Y-m-d H:i:s') : '';
        };

        $defaultContext = [
            AbstractNormalizer::CALLBACKS => [
                'created_at' => $dateCallback,
                'updated_at' => $dateCallback,
                'deleted_at' => $dateCallback
            ],
            AbstractNormalizer::CIRCULAR_REFERENCE_HANDLER => function ($articles, $format, $context) {
                return $articles->getId();
            }
        ];

        $classMetadataFactory = new ClassMetadataFactory(new AnnotationLoader(new AnnotationReader()));
        $normalizer = new ObjectNormalizer(
            $classMetadataFactory,
            null, null, null, null, null,
            $defaultContext
        );
        $serializer = new Serializer([$normalizer, new DateTimeNormalizer()]);

        try {
            return $serializer->normalize($data, null,
                [
                    'groups' => $groups,
                    AbstractObjectNormalizer::ENABLE_MAX_DEPTH => true
                ]
            );
        } catch (ExceptionInterface) {
            return [];
        }
    }

    private function unauthenticated()
    {
        return $this->json([], 401);
    }

    private function normalizedCustomPagination(array $data = [], $groups = ['api_data'], array $pagination = [], array $other = []): Response
    {
        return $this->jsonResponse(array_merge([
            'data' => $this->normalize($data, $groups),
            'pagination' => [
                'current' => $pagination['current'],
                'total' => $pagination['total'],
                'pages' => ceil($pagination['total'] / $pagination['perPage'])
            ]
        ], $other));
    }

    private function normalizedPagination(PaginationInterface $pagination, $groups = ['api_data'], array $other = []): Response
    {
        if ($pagination->getTotalItemCount() === 0) {
            return new Response('', 404);
        }

        return $this->jsonResponse(array_merge([
            'data' => $this->normalize($pagination->getItems(), $groups),
            'pagination' => [
                'current' => $pagination->getCurrentPageNumber(),
                'total' => $pagination->getTotalItemCount(),
                'pages' => ceil($pagination->getTotalItemCount() / $pagination->getItemNumberPerPage())
            ]
        ], $other));
    }
}
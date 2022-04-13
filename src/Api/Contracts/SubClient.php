<?php


namespace Miniyus\RestfulApiClient\Api\Contracts;

interface SubClient
{
    /**
     * @param array|string|null $input
     * @return array|string|null
     */
    public function get(array|string $input = null): array|string|null;

    /**
     * @param array $input
     * @return array|string|null
     */
    public function post(array $input = []): array|string|null;

    /**
     * @param int|array|string $input
     * @return array|string|null
     */
    public function put(int|array|string $input = []): array|string|null;

    /**
     * @param array|int|string $input
     * @return array|string|null
     */
    public function delete(array|int|string $input = []): array|string|null;
}

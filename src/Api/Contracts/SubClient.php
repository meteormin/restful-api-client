<?php


namespace Miniyus\RestfulApiClient\Api\Contracts;

interface SubClient
{
    /**
     * @param array|string|null $input
     * @return array|string|null
     */
    public function get($input = null);

    /**
     * @param array $input
     * @return array|string|null
     */
    public function post(array $input = []);

    /**
     * @param array|int|string $input
     * @return array|string|null
     */
    public function put($input = []);

    /**
     * @param string|int|array $input
     * @return array|string|null
     */
    public function delete($input);
}

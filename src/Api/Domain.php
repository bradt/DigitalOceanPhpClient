<?php

declare(strict_types=1);

/*
 * This file is part of the DigitalOceanV2 library.
 *
 * (c) Antoine Corcy <contact@sbin.dk>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace DigitalOceanV2\Api;

use DigitalOceanV2\Entity\Domain as DomainEntity;
use DigitalOceanV2\Exception\ExceptionInterface;
use DigitalOceanV2\HttpClient\Util\JsonObject;

/**
 * @author Yassir Hannoun <yassir.hannoun@gmail.com>
 * @author Graham Campbell <graham@alt-three.com>
 */
class Domain extends AbstractApi
{
    /**
     * @param int $per_page
     * @param int $page
     *
     * @throws ExceptionInterface
     *
     * @return DomainEntity[]
     */
    public function getAll($per_page = 200, $page = 1)
    {
        $domains = $this->httpClient->get(sprintf('%s/domains?per_page=%d&page=%d', $this->endpoint, $per_page, $page));

        $domains = JsonObject::decode($domains);

        $this->extractMeta($domains);

        return array_map(function ($domain) {
            return new DomainEntity($domain);
        }, $domains->domains);
    }

    /**
     * @param string $domainName
     *
     * @throws ExceptionInterface
     *
     * @return DomainEntity
     */
    public function getByName($domainName)
    {
        $domain = $this->httpClient->get(sprintf('%s/domains/%s', $this->endpoint, $domainName));

        $domain = JsonObject::decode($domain);

        return new DomainEntity($domain->domain);
    }

    /**
     * @param string $name
     * @param string $ipAddress
     *
     * @throws ExceptionInterface
     *
     * @return DomainEntity
     */
    public function create($name, $ipAddress)
    {
        $content = ['name' => $name, 'ip_address' => $ipAddress];

        $domain = $this->httpClient->post(sprintf('%s/domains', $this->endpoint), $content);

        $domain = JsonObject::decode($domain);

        return new DomainEntity($domain->domain);
    }

    /**
     * @param string $domain
     *
     * @throws ExceptionInterface
     *
     * @return void
     */
    public function delete($domain)
    {
        $this->httpClient->delete(sprintf('%s/domains/%s', $this->endpoint, $domain));
    }
}

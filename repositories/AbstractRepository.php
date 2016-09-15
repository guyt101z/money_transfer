<?php

namespace repositories;

use entities\Entity;
use dto\Dto;
use gateways\Gateway;

abstract class AbstractRepository
{
	protected $gateway;

	/**
	 * @param Gateway $gateway
	 */
	public function __construct(Gateway $gateway)
	{
		$this->gateway = $gateway;
	}

	/**
	 * @param int $id
	 * @return Entity|null
	 */
	public function findById($id)
	{
		$map = $this->gateway->findById($id);

		if (!$map) {
			return null;
		}

		$entity = $this->createEntity();
		$entity->load($map);

		return $entity;
	}

	/**
	 * @param Dto $dto
	 * @return Entity[]
	 */
	public function findByDto($dto)
	{
		$criteria = $dto->toMap();
		$limit = 0;
		$offset = 0;

		if (isset($criteria['limit'])) {
			$limit = (int)$criteria['limit'];
			unset($criteria['limit']);
		}

		if (isset($criteria['offset'])) {
			$offset = (int)$criteria['offset'];
			unset($criteria['offset']);
		}

		if ($limit <= 0) {
			$limit = 10;
		}

		$result = [];
		$items = $this->gateway->findByCriteria($criteria, $limit, $offset);

		foreach ($items as $map) {
			$entity = $this->createEntity();
			$entity->load($map);
			$result[] = $entity;
		}

		return $result;
	}

	/**
	 * @param Dto $dto
	 * @return Entity
	 */
	public function create(Dto $dto)
	{
		$map = $dto->toMap();
		$entity = $this->createEntity();
		$entity->load($map);

		return $entity;
	}

	/**
	 * @param Entity $entity
	 * @return Entity
	 */
	public function save(Entity $entity)
	{
		if ($entity->getId() > 0) {
			$dirty = $entity->getDirty();

			if ($dirty) {
				$this->gateway->update($entity->getId(), $dirty);
			}
		} else {
			$this->gateway->insert($entity->toMap());
		}

		return $entity;
	}

	/**
	 * @param Entity $entity
	 */
	public function delete(Entity $entity)
	{
		$this->gateway->delete($entity->getId());
		return $entity;
	}

	abstract protected function createEntity();
}
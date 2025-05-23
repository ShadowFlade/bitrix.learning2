<?php

namespace Webgk\Helpers;

use Bitrix\Highloadblock\HighloadBlockTable;
use Bitrix\Main\Loader;
use Bitrix\Main\SystemException;
use CUserFieldEnum;
use CUserTypeEntity;

class HighloadBlock
{
	protected $hlBlock;
	protected $entity;
	protected $entityDataClass;

	/**
	 * @throws \Bitrix\Main\ArgumentException
	 * @throws \Bitrix\Main\ObjectPropertyException
	 * @throws SystemException
	 */
	public function __construct(string $hlBlockTableName)
	{
		Loader::includeModule('highloadblock');

		$this->hlBlock = self::getHlBlockByName($hlBlockTableName);

		if (empty($this->hlBlock)) {
			throw new SystemException('not found HL for TABLE_NAME = ' . $hlBlockTableName);
		}

		$this->entity = $this->getEntity();
		$this->entityDataClass = $this->getEntityDataClass();
	}

	/**
	 * @throws \Bitrix\Main\ObjectPropertyException
	 * @throws SystemException
	 * @throws \Bitrix\Main\ArgumentException
	 */
	public static function init(string $hlBlockTableName): self
	{
		return new static($hlBlockTableName);
	}

	/**
	 * Получение HL блока по названию таблицы
	 * @param string $tableName
	 * @return array
	 * @throws SystemException
	 * @throws \Bitrix\Main\ArgumentException
	 * @throws \Bitrix\Main\ObjectPropertyException
	 */
	public function getHlBlockByName(string $tableName): array
	{
		return HighloadBlockTable::getRow([
			'filter' => [
				'TABLE_NAME' => $tableName
			]
		]) ?? [];
	}

	public function getHlBlock()
	{
		return $this->hlBlock;
	}

	public function getHlBlockId()
	{
		return $this->hlBlock['ID'];
	}

	/**
	 * Сущность HL
	 * @return \Bitrix\Main\Entity\Base
	 * @throws SystemException
	 */
	public function getEntity()
	{
		if ($this->entity === null) {
			$this->entity = HighloadBlockTable::compileEntity($this->hlBlock);
		}

		return $this->entity;
	}

	/**
	 * Класс для работы с HL блоком
	 * @return \Bitrix\Main\ORM\Data\DataManager|string
	 * @throws SystemException
	 */
	public function getEntityDataClass()
	{
		if ($this->entityDataClass === null) {
			$this->entityDataClass = $this->entity->getDataClass();
		}

		return $this->entityDataClass;
	}

	/**
	 * Получение записей HL блока
	 * @param array $params
	 * @return array
	 * @throws SystemException
	 * @throws \Bitrix\Main\ArgumentException
	 * @throws \Bitrix\Main\ObjectPropertyException
	 */
	public function find(array $params = []): array
	{
		return $this->entityDataClass::getList($params)->fetchAll() ?? [];
	}

	/**
	 * Получение записей HL блока
	 * @param array $params
	 * @return array
	 * @throws SystemException
	 * @throws \Bitrix\Main\ArgumentException
	 * @throws \Bitrix\Main\ObjectPropertyException
	 */
	public function findRaw(array $params = [], $indexKey, $cb): array
	{
		$elsDB = $this->entityDataClass::getList($params);
		$els = [];

		while($el = $elsDB->fetch()) {
			$cb($el);
			$els[$el[$indexKey]] = $el;
		};


		return !empty($els) ? $els : [];
	}

	/**
	 * Получение записи HL блока
	 * @param array $params
	 * @return array
	 * @throws SystemException
	 * @throws \Bitrix\Main\ArgumentException
	 * @throws \Bitrix\Main\ObjectPropertyException
	 */
	public function getRow(array $params = []): array
	{
		return $this->entityDataClass::getRow($params) ?? [];
	}

	/**
	 * Добавление записи в HL блок
	 * @param array $fields
	 * @return int
	 * @throws SystemException
	 */
	public function add(array $fields): int
	{
		if (empty($fields)) {
			throw new SystemException('Не переданы поля добавляемой записи');
		}

		$result = $this->entityDataClass::add($fields);

		if (!$result->isSuccess()) {
			throw new SystemException(implode(', ', $result->getErrorMessages()));
		}

		return (int)$result->getId();
	}

	/**
	 * Обновление записи HL блока
	 * @param int $id
	 * @param array $fields
	 * @param bool $checkCurrentUser
	 * @return int
	 * @throws SystemException
	 */
	public function update(int $id, array $fields, bool $checkCurrentUser = false): int
	{
		if (!($id > 0)) {
			throw new SystemException('Не передан ID обновляемой записи');
		}

		if (empty($fields)) {
			throw new SystemException('Не переданы поля обновляемой записи');
		}

		if ($checkCurrentUser) {
			//todo: проверяем принадлежность элемента к юзеру
		}

		$result = $this->entityDataClass::update($id, $fields);

		if (!$result->isSuccess()) {
			throw new SystemException(implode(', ', $result->getErrorMessages()));
		}

		return (int)$result->getId();
	}

	/**
	 * Удаление записи HL блока
	 * @param int $id
	 * @param bool $checkCurrentUser
	 * @return bool
	 * @throws SystemException
	 */
	public function delete(int $id, bool $checkCurrentUser = false): bool
	{
		if (!($id > 0)) {
			throw new SystemException('Не передан ID удаляемой записи');
		}

		if ($checkCurrentUser) {
			//todo: проверяем принадлежность элемента к юзеру
		}

		$result = $this->entityDataClass::delete($id);

		if (!$result->isSuccess()) {
			throw new SystemException(implode(', ', $result->getErrorMessages()));
		}

		return true;
	}

	public function getEnumUfFieldValues($ufName)
	{
		$property = CUserTypeEntity::GetList(
			[],
			[
				'ENTITY_ID' => 'HLBLOCK_' . $this->getHlBlockId(),
				'FIELD_NAME' => $ufName
			]
		)->Fetch();

		$result = [];

		if ($property && $property['USER_TYPE_ID'] === 'enumeration') {
			$enumList = CUserFieldEnum::GetList(
				[],
				['USER_FIELD_ID' => $property['ID']]
			);

			while ($enumFields = $enumList->GetNext()) {
				$result['BY_ID'][$enumFields['ID']] = [
					'VALUE' => $enumFields['VALUE'],
					'XML_ID' => $enumFields['XML_ID']
				];
				$result['BY_XML_ID'][$enumFields['XML_ID']] = [
					'VALUE' => $enumFields['VALUE'],
					'ID' => $enumFields['ID']
				];
				$result['BY_VALUE'][$enumFields['VALUE']] = [
					'XML_ID' => $enumFields['XML_ID'],
					'ID' => $enumFields['ID']
				];
			}

			return $result;
		} else {
			return false;
		}
	}
}

<?php

declare(strict_types=1);

namespace App\Application\Models;

class OrganizationModel
{
	private $db;

	public function __construct($db)
	{
		$this->db = $db;
	}

	public function findById(int $id): ?array
	{
		return $this->db->get('tbl_organizations', [
			'[>]tbl_locations' => ['id_locations' => 'id']
		], [
			'tbl_organizations.id',
			'tbl_organizations.nama',
			'tbl_organizations.email',
			'tbl_organizations.bisnis_perusahaan',
			'tbl_organizations.create_time',
			'tbl_locations.nama(location_name)',
			'tbl_locations.alamat',
			'tbl_locations.kecamatan',
			'tbl_locations.kabupaten',
			'tbl_locations.provinsi'
		], [
			'tbl_organizations.id' => $id,
			'tbl_organizations.archived' => 0
		]);
	}

	public function getAllOrganizations(): array
	{
		return $this->db->select('tbl_organizations', [
			'[>]tbl_locations' => ['id_locations' => 'id']
		], [
			'tbl_organizations.id',
			'tbl_organizations.nama',
			'tbl_organizations.email',
			'tbl_organizations.bisnis_perusahaan',
			'tbl_organizations.create_time',
			'tbl_locations.nama(location_name)',
			'tbl_locations.kabupaten'
		], [
			'tbl_organizations.archived' => 0
		]);
	}

	public function create(array $organizationData): int
	{
		$organizationData['create_time'] = date('Y-m-d H:i:s');
		$organizationData['archived'] = 0;

		$this->db->insert('tbl_organizations', $organizationData);
		return $this->db->id();
	}

	public function update(int $id, array $organizationData): bool
	{
		$organizationData['update_time'] = date('Y-m-d H:i:s');

		$result = $this->db->update('tbl_organizations', $organizationData, ['id' => $id]);
		return $result->rowCount() > 0;
	}

	public function delete(int $id): bool
	{
		$result = $this->db->update('tbl_organizations', [
			'archived' => 1,
			'update_time' => date('Y-m-d H:i:s')
		], ['id' => $id]);
		return $result->rowCount() > 0;
	}

	public function getJobCount(int $organizationId): int
	{
		return $this->db->count('tbl_lowongans', [
			'id_organizations' => $organizationId,
			'archived' => 0
		]);
	}
}

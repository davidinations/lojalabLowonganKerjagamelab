<?php

declare(strict_types=1);

namespace App\Application\Models;

class LowonganModel
{
	private $db;

	public function __construct($db)
	{
		$this->db = $db;
	}

	public function findById(int $id): ?array
	{
		return $this->db->get('tbl_lowongans', [
			'[>]tbl_organizations' => ['id_organizations' => 'id'],
			'[>]tbl_typeLowongans' => ['id_typeLowongans' => 'id'],
			'[>]tbl_locations' => ['id_locations' => 'id']
		], [
			'tbl_lowongans.id',
			'tbl_lowongans.title',
			'tbl_lowongans.jenis_pekerjaan',
			'tbl_lowongans.deskripsi',
			'tbl_lowongans.create_time',
			'tbl_organizations.nama(company_name)',
			'tbl_organizations.email(company_email)',
			'tbl_typeLowongans.category',
			'tbl_locations.nama(location_name)',
			'tbl_locations.alamat',
			'tbl_locations.kecamatan',
			'tbl_locations.kabupaten',
			'tbl_locations.provinsi'
		], [
			'tbl_lowongans.id' => $id,
			'tbl_lowongans.archived' => 0
		]);
	}

	public function getAllJobs(): array
	{
		return $this->db->select('tbl_lowongans', [
			'[>]tbl_organizations' => ['id_organizations' => 'id'],
			'[>]tbl_typeLowongans' => ['id_typeLowongans' => 'id'],
			'[>]tbl_locations' => ['id_locations' => 'id']
		], [
			'tbl_lowongans.id',
			'tbl_lowongans.title',
			'tbl_lowongans.jenis_pekerjaan',
			'tbl_lowongans.create_time',
			'tbl_organizations.nama(company_name)',
			'tbl_typeLowongans.category',
			'tbl_locations.nama(location_name)',
			'tbl_locations.kabupaten'
		], [
			'tbl_lowongans.archived' => 0,
			'ORDER' => ['tbl_lowongans.create_time' => 'DESC']
		]);
	}

	public function getJobsByOrganization(int $organizationId): array
	{
		return $this->db->select('tbl_lowongans', [
			'[>]tbl_typeLowongans' => ['id_typeLowongans' => 'id'],
			'[>]tbl_locations' => ['id_locations' => 'id']
		], [
			'tbl_lowongans.id',
			'tbl_lowongans.title',
			'tbl_lowongans.jenis_pekerjaan',
			'tbl_lowongans.deskripsi',
			'tbl_lowongans.create_time',
			'tbl_typeLowongans.category',
			'tbl_locations.nama(location_name)'
		], [
			'tbl_lowongans.id_organizations' => $organizationId,
			'tbl_lowongans.archived' => 0
		]);
	}

	public function create(array $jobData): int
	{
		$jobData['create_time'] = date('Y-m-d H:i:s');
		$jobData['archived'] = 0;

		$this->db->insert('tbl_lowongans', $jobData);
		return $this->db->id();
	}

	public function update(int $id, array $jobData): bool
	{
		$jobData['update_time'] = date('Y-m-d H:i:s');

		$result = $this->db->update('tbl_lowongans', $jobData, ['id' => $id]);
		return $result->rowCount() > 0;
	}

	public function delete(int $id): bool
	{
		$result = $this->db->update('tbl_lowongans', [
			'archived' => 1,
			'update_time' => date('Y-m-d H:i:s')
		], ['id' => $id]);
		return $result->rowCount() > 0;
	}

	public function searchJobs(array $filters): array
	{
		$conditions = ['tbl_lowongans.archived' => 0];

		if (!empty($filters['keyword'])) {
			$conditions['OR'] = [
				'tbl_lowongans.title[~]' => $filters['keyword'],
				'tbl_lowongans.deskripsi[~]' => $filters['keyword']
			];
		}

		if (!empty($filters['category'])) {
			$conditions['tbl_lowongans.id_typeLowongans'] = $filters['category'];
		}

		if (!empty($filters['location'])) {
			$conditions['tbl_lowongans.id_locations'] = $filters['location'];
		}

		return $this->db->select('tbl_lowongans', [
			'[>]tbl_organizations' => ['id_organizations' => 'id'],
			'[>]tbl_typeLowongans' => ['id_typeLowongans' => 'id'],
			'[>]tbl_locations' => ['id_locations' => 'id']
		], [
			'tbl_lowongans.id',
			'tbl_lowongans.title',
			'tbl_lowongans.jenis_pekerjaan',
			'tbl_lowongans.create_time',
			'tbl_organizations.nama(company_name)',
			'tbl_typeLowongans.category',
			'tbl_locations.nama(location_name)'
		], $conditions);
	}
}

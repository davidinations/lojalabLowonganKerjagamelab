<?php

declare(strict_types=1);

namespace App\Application\Models;

class ApplyLowonganModel
{
	private $db;

	public function __construct($db)
	{
		$this->db = $db;
	}

	public function findById(int $id): ?array
	{
		return $this->db->get('tbl_applyLowongans', [
			'[>]tbl_users' => ['id_user' => 'id'],
			'[>]tbl_lowongans' => ['id_lowongans' => 'id']
		], [
			'tbl_applyLowongans.id',
			'tbl_applyLowongans.cv_document',
			'tbl_applyLowongans.pesan',
			'tbl_applyLowongans.create_time',
			'tbl_users.username',
			'tbl_users.email',
			'tbl_lowongans.title(job_title)'
		], [
			'tbl_applyLowongans.id' => $id,
			'tbl_applyLowongans.archived' => 0
		]);
	}

	public function findByUserId(int $userId): array
	{
		return $this->db->select('tbl_applyLowongans', [
			'[>]tbl_lowongans' => ['id_lowongans' => 'id'],
			'[>]tbl_organizations' => ['tbl_lowongans.id_organizations' => 'id']
		], [
			'tbl_applyLowongans.id',
			'tbl_applyLowongans.cv_document',
			'tbl_applyLowongans.pesan',
			'tbl_applyLowongans.create_time',
			'tbl_lowongans.title(job_title)',
			'tbl_organizations.nama(company_name)'
		], [
			'tbl_applyLowongans.id_user' => $userId,
			'tbl_applyLowongans.archived' => 0
		]);
	}

	public function findByJobId(int $jobId): array
	{
		return $this->db->select('tbl_applyLowongans', [
			'[>]tbl_users' => ['id_user' => 'id']
		], [
			'tbl_applyLowongans.id',
			'tbl_applyLowongans.cv_document',
			'tbl_applyLowongans.pesan',
			'tbl_applyLowongans.create_time',
			'tbl_users.username',
			'tbl_users.email',
			'tbl_users.tempat_lahir',
			'tbl_users.tanggal_lahir',
			'tbl_users.pendidikan_terakhir'
		], [
			'tbl_applyLowongans.id_lowongans' => $jobId,
			'tbl_applyLowongans.archived' => 0
		]);
	}

	public function create(array $applicationData): int
	{
		$applicationData['create_time'] = date('Y-m-d H:i:s');
		$applicationData['archived'] = 0;

		$this->db->insert('tbl_applyLowongans', $applicationData);
		return $this->db->id();
	}

	public function update(int $id, array $applicationData): bool
	{
		$applicationData['update_time'] = date('Y-m-d H:i:s');

		$result = $this->db->update('tbl_applyLowongans', $applicationData, ['id' => $id]);
		return $result->rowCount() > 0;
	}

	public function delete(int $id): bool
	{
		$result = $this->db->update('tbl_applyLowongans', [
			'archived' => 1,
			'update_time' => date('Y-m-d H:i:s')
		], ['id' => $id]);
		return $result->rowCount() > 0;
	}

	public function checkExistingApplication(int $userId, int $jobId): bool
	{
		$count = $this->db->count('tbl_applyLowongans', [
			'id_user' => $userId,
			'id_lowongans' => $jobId,
			'archived' => 0
		]);

		return $count > 0;
	}

	public function getAllApplications(): array
	{
		return $this->db->select('tbl_applyLowongans', [
			'[>]tbl_users' => ['id_user' => 'id'],
			'[>]tbl_lowongans' => ['id_lowongans' => 'id'],
			'[>]tbl_organizations' => ['tbl_lowongans.id_organizations' => 'id']
		], [
			'tbl_applyLowongans.id',
			'tbl_applyLowongans.cv_document',
			'tbl_applyLowongans.pesan',
			'tbl_applyLowongans.create_time',
			'tbl_users.username',
			'tbl_users.email',
			'tbl_lowongans.title(job_title)',
			'tbl_organizations.nama(company_name)'
		], [
			'tbl_applyLowongans.archived' => 0
		]);
	}
}

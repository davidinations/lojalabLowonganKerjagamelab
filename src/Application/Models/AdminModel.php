<?php

declare(strict_types=1);

namespace App\Application\Models;

class AdminModel
{
	private $db;

	public function __construct($db)
	{
		$this->db = $db;
	}

	public function findByUsername(string $username): ?array
	{
		return $this->db->get('tbl_admins', [
			'[>]tbl_roles' => ['id_role' => 'id'],
			'[>]tbl_organizations' => ['id_organizations' => 'id']
		], [
			'tbl_admins.id',
			'tbl_admins.username',
			'tbl_admins.email',
			'tbl_admins.password',
			'tbl_admins.id_role',
			'tbl_admins.id_organizations',
			'tbl_roles.nama(role_name)',
			'tbl_roles.level(role_level)',
			'tbl_organizations.nama(organization_name)'
		], [
			'tbl_admins.username' => $username,
			'tbl_admins.archived' => 0
		]);
	}

	public function findByEmail(string $email): ?array
	{
		return $this->db->get('tbl_admins', [
			'[>]tbl_roles' => ['id_role' => 'id'],
			'[>]tbl_organizations' => ['id_organizations' => 'id']
		], [
			'tbl_admins.id',
			'tbl_admins.username',
			'tbl_admins.email',
			'tbl_admins.password',
			'tbl_admins.id_role',
			'tbl_admins.id_organizations',
			'tbl_roles.nama(role_name)',
			'tbl_roles.level(role_level)',
			'tbl_organizations.nama(organization_name)'
		], [
			'tbl_admins.email' => $email,
			'tbl_admins.archived' => 0
		]);
	}

	public function findById(int $id): ?array
	{
		return $this->db->get('tbl_admins', [
			'[>]tbl_roles' => ['id_role' => 'id'],
			'[>]tbl_organizations' => ['id_organizations' => 'id']
		], [
			'tbl_admins.id',
			'tbl_admins.username',
			'tbl_admins.email',
			'tbl_admins.id_role',
			'tbl_admins.id_organizations',
			'tbl_roles.nama(role_name)',
			'tbl_roles.level(role_level)',
			'tbl_organizations.nama(organization_name)'
		], [
			'tbl_admins.id' => $id,
			'tbl_admins.archived' => 0
		]);
	}

	public function create(array $adminData): int
	{
		$adminData['password'] = password_hash($adminData['password'], PASSWORD_DEFAULT);
		$adminData['create_time'] = date('Y-m-d H:i:s');
		$adminData['archived'] = 0;

		$this->db->insert('tbl_admins', $adminData);
		return $this->db->id();
	}

	public function update(int $id, array $adminData): bool
	{
		$adminData['update_time'] = date('Y-m-d H:i:s');

		$result = $this->db->update('tbl_admins', $adminData, ['id' => $id]);
		return $result->rowCount() > 0;
	}

	public function verifyPassword(string $password, string $hash): bool
	{
		return password_verify($password, $hash);
	}

	public function getAllAdmins(): array
	{
		return $this->db->select('tbl_admins', [
			'[>]tbl_roles' => ['id_role' => 'id'],
			'[>]tbl_organizations' => ['id_organizations' => 'id']
		], [
			'tbl_admins.id',
			'tbl_admins.username',
			'tbl_admins.email',
			'tbl_admins.id_role',
			'tbl_admins.id_organizations',
			'tbl_roles.nama(role_name)',
			'tbl_roles.level(role_level)',
			'tbl_organizations.nama(organization_name)'
		], [
			'tbl_admins.archived' => 0
		]);
	}
}

<?php

declare(strict_types=1);

namespace App\Application\Models;

class RoleModel
{
	private $db;

	public function __construct($db)
	{
		$this->db = $db;
	}

	public function findById(int $id): ?array
	{
		return $this->db->get('tbl_roles', '*', [
			'id' => $id,
			'archived' => 0
		]);
	}

	public function getAllRoles(): array
	{
		return $this->db->select('tbl_roles', '*', [
			'archived' => 0,
			'ORDER' => ['level' => 'ASC']
		]);
	}

	public function getAllRolesWithAdminCount(): array
	{
		$sql = "
			SELECT 
				r.*,
				COUNT(a.id) as admin_count
			FROM tbl_roles r
			LEFT JOIN tbl_admins a ON r.id = a.id_role AND a.archived = 0
			WHERE r.archived = 0
			GROUP BY r.id
			ORDER BY r.level ASC
		";
		
		$stmt = $this->db->pdo->prepare($sql);
		$stmt->execute();
		return $stmt->fetchAll(\PDO::FETCH_ASSOC);
	}

	public function create(array $roleData): int
	{
		$roleData['create_time'] = date('Y-m-d H:i:s');
		$roleData['archived'] = 0;

		$this->db->insert('tbl_roles', $roleData);
		return $this->db->id();
	}

	public function update(int $id, array $roleData): bool
	{
		$roleData['update_time'] = date('Y-m-d H:i:s');

		$result = $this->db->update('tbl_roles', $roleData, ['id' => $id]);
		return $result->rowCount() > 0;
	}

	public function delete(int $id): bool
	{
		$result = $this->db->update('tbl_roles', [
			'archived' => 1,
			'update_time' => date('Y-m-d H:i:s')
		], ['id' => $id]);
		return $result->rowCount() > 0;
	}

	public function getRolePermissions(int $roleLevel): array
	{
		// Define role-based permissions
		$permissions = [
			1 => [ // Super Admin
				'users.view',
				'users.create',
				'users.edit',
				'users.delete',
				'admins.view',
				'admins.create',
				'admins.edit',
				'admins.delete',
				'organizations.view',
				'organizations.create',
				'organizations.edit',
				'organizations.delete',
				'lowongans.view',
				'lowongans.create',
				'lowongans.edit',
				'lowongans.delete',
				'applications.view',
				'applications.approve',
				'applications.reject',
				'roles.view',
				'roles.create',
				'roles.edit',
				'roles.delete',
				'system.settings'
			],
			2 => [ // Admin
				'users.view',
				'users.create',
				'users.edit',
				'organizations.view',
				'organizations.edit',
				'lowongans.view',
				'lowongans.create',
				'lowongans.edit',
				'lowongans.delete',
				'applications.view',
				'applications.approve',
				'applications.reject'
			],
			3 => [ // Recruiter
				'lowongans.view',
				'lowongans.create',
				'lowongans.edit',
				'applications.view',
				'applications.approve',
				'applications.reject',
				'users.view'
			]
		];

		return $permissions[$roleLevel] ?? [];
	}
}

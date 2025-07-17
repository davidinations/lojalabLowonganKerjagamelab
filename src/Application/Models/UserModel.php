<?php

declare(strict_types=1);

namespace App\Application\Models;

class UserModel
{
	private $db;

	public function __construct($db)
	{
		$this->db = $db;
	}

	public function findByUsername(string $username): ?array
	{
		return $this->db->get('tbl_users', '*', [
			'username' => $username,
			'archived' => 0
		]);
	}

	public function findByEmail(string $email): ?array
	{
		return $this->db->get('tbl_users', '*', [
			'email' => $email,
			'archived' => 0
		]);
	}

	public function findById(int $id): ?array
	{
		return $this->db->get('tbl_users', '*', [
			'id' => $id,
			'archived' => 0
		]);
	}

	public function create(array $userData): int
	{
		$userData['password'] = password_hash($userData['password'], PASSWORD_DEFAULT);
		$userData['create_time'] = date('Y-m-d H:i:s');
		$userData['archived'] = 0;

		$this->db->insert('tbl_users', $userData);
		return $this->db->id();
	}

	public function update(int $id, array $userData): bool
	{
		$userData['update_time'] = date('Y-m-d H:i:s');

		$result = $this->db->update('tbl_users', $userData, ['id' => $id]);
		return $result->rowCount() > 0;
	}

	public function verifyPassword(string $password, string $hash): bool
	{
		return password_verify($password, $hash);
	}

	public function getAllUsers(): array
	{
		return $this->db->select('tbl_users', '*', [
			'archived' => 0
		]);
	}
}

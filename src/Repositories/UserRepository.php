<?php
namespace App\Repositories;

use PDO;

final class UserRepository
{
    /**
     * Inject the PDO instance directly into the constructor
     */
    public function __construct(private PDO $pdo) {}

    /**
     * Find or create a default user profile row for testing (e.g., ID = 1)
     */
    public function ensureUserExists(int $id, string $name, string $email): array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM users WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $user = $stmt->fetch();

        if ($user) {
            return $user;
        }

        // Create the user record if it doesn't exist yet
        $sql = 'INSERT INTO users (id, name, email, eco_points, gained_today) 
                VALUES (:id, :name, :email, 1240, 80)';
        $insertStmt = $this->pdo->prepare($sql);
        $insertStmt->execute([
            'id' => $id,
            'name' => $name,
            'email' => $email
        ]);

        $stmt->execute(['id' => $id]);
        return $stmt->fetch();
    }

    /**
     * Get the active user's current metrics panels summary
     */
    public function getUserMetrics(int $id): array
    {
        $stmt = $this->pdo->prepare('SELECT eco_points, gained_today FROM users WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();

        return $row ?: ['eco_points' => 0, 'gained_today' => 0];
    }

/**
     * Increment eco points balances for an active user
     */
    public function incrementPoints(int $id, int $points): void
    {
        $sql = 'UPDATE users SET eco_points = eco_points + :pts1, gained_today = gained_today + :pts2 WHERE id = :id';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'pts1' => $points,
            'pts2' => $points,
            'id'   => $id
        ]);
    }

    /**
     * Load the standard verified community leaderboard sorted by highest score
     */
    public function getLeaderboard(int $currentUserId): array
    {
        $stmt = $this->pdo->query('SELECT id, name, eco_points FROM users ORDER BY eco_points DESC');
        $rows = $stmt->fetchAll();

        $leaderboard = [];
        $rank = 1;
        foreach ($rows as $row) {
            $isMe = ((int)$row['id'] === $currentUserId);
            $leaderboard[] = [
                'rank' => $rank++,
                'name' => $isMe ? 'You (GreenRunner)' : $row['name'],
                'eco_points' => (int)$row['eco_points'],
                'is_current_user' => $isMe
            ];
        }

        return $leaderboard;
    }

    /**
     * Fetch a user's verified standard friends list
     */
    public function getFriends(int $userId): array
    {
        $sql = 'SELECT u.id, u.name, u.eco_points 
                FROM friendships f
                JOIN users u ON f.friend_id = u.id
                WHERE f.user_id = :user_id
                ORDER BY u.eco_points DESC';
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['user_id' => $userId]);
        $rows = $stmt->fetchAll();

        return array_map(function($row) {
            return [
                'id' => (int)$row['id'],
                'name' => $row['name'],
                'eco_points' => (int)$row['eco_points'],
                'avatar' => strtolower(str_replace(' ', '_', $row['name'])) . '.jpg'
            ];
        }, $rows);
    }

    /**
     * Fetch all outstanding inbound friend connection requests
     */
    public function getPendingRequests(int $receiverId): array
    {
        $sql = 'SELECT r.id, u.name, r.requested_at 
                FROM friend_requests r
                JOIN users u ON r.sender_id = u.id
                WHERE r.receiver_id = :receiver_id
                ORDER BY r.id ASC';

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['receiver_id' => $receiverId]);
        return $stmt->fetchAll();
    }

    /**
     * Inject seed user data models
     */
    public function createUserDirect(array $data): void
    {
        $sql = 'INSERT INTO users (id, name, email, eco_points, gained_today) VALUES (:id, :name, :email, :eco_points, :gained_today)';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($data);
    }

    /**
     * Inject friendship rows directly
     */
    public function establishFriendshipDirect(int $userId, int $friendId): void
    {
        $stmt = $this->pdo->prepare('INSERT INTO friendships (user_id, friend_id) VALUES (:user_id, :friend_id)');
        $stmt->execute(['user_id' => $userId, 'friend_id' => $friendId]);
    }

    /**
     * Inject friend requests directly
     */
    public function createRequestDirect(array $data): void
    {
        $sql = 'INSERT INTO friend_requests (sender_id, receiver_id, requested_at) VALUES (:sender_id, :receiver_id, :requested_at)';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($data);
    }

    /**
     * Resets users, friendships, and friend request metrics tables
     */
    public function truncateUserTables(): void
    {
        $this->pdo->exec('DELETE FROM friend_requests');
        $this->pdo->exec('DELETE FROM friendships');
        $this->pdo->exec('DELETE FROM users');
    }
}
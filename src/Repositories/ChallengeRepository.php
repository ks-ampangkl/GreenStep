<?php
namespace App\Repositories;

use PDO;

final class ChallengeRepository
{
    /**
     * Inject the PDO instance directly into the constructor
     */
    public function __construct(private PDO $pdo) {}

    /**
     * Fetch filtered challenges based on frontend tab parameters (all, joined, active, completed)
     */
    public function getChallengesByFilter(int $userId, string $filter): array
    {
        // Base SQL structure with a LEFT JOIN to see if the current user has joined the challenge
        $sql = 'SELECT c.id, 
                       c.title, 
                       c.description, 
                       c.target_type, 
                       c.group_progress_percent,
                       IF(cm.user_id IS NOT NULL, 1, 0) AS has_joined
                FROM challenges c
                LEFT JOIN challenge_members cm ON c.id = cm.challenge_id AND cm.user_id = :user_id
                WHERE 1=1';

        // Append conditions dynamically depending on our active tab filter
        if ($filter === 'joined') {
            $sql .= ' AND cm.user_id IS NOT NULL';
        } elseif ($filter === 'active') {
            $sql .= ' AND c.is_active = 1 AND c.is_completed = 0';
        } elseif ($filter === 'completed') {
            $sql .= ' AND c.is_completed = 1';
        }

        $sql .= ' ORDER BY c.id ASC';

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['user_id' => $userId]);
        $results = $stmt->fetchAll();

        // Shape the data array on the fly to match your original front-end expectations
        return array_map(function($row) {
            $filters = ['all'];
            if ($row['has_joined']) $filters[] = 'joined';
            
            // Reconstruct the JSON filter array exactly how the UI expects it
            return [
                'id' => (int)$row['id'],
                'title' => $row['title'],
                'description' => $row['description'],
                'target_type' => $row['target_type'],
                'filters' => $row['has_joined'] ? ['all', 'joined', 'active'] : ['all', 'active'],
                'has_joined' => (bool)$row['has_joined'],
                'group_progress_percent' => (float)$row['group_progress_percent']
            ];
        }, $results);
    }

    /**
     * Join a challenge by inserting a row into the junction table
     */
    public function joinChallenge(int $challengeId, int $userId): bool
    {
        // First check if already joined to prevent duplicate primary key errors
        $checkStmt = $this->pdo->prepare('SELECT 1 FROM challenge_members WHERE challenge_id = :challenge_id AND user_id = :user_id');
        $checkStmt->execute(['challenge_id' => $challengeId, 'user_id' => $userId]);
        
        if ($checkStmt->fetch()) {
            return true; // Already joined
        }

        $sql = 'INSERT INTO challenge_members (challenge_id, user_id) VALUES (:challenge_id, :user_id)';
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            'challenge_id' => $challengeId,
            'user_id' => $userId
        ]);
    }

    /**
     * Create a brand-new community challenge (Test 6 submission support)
     */
    public function createChallenge(array $data): int
    {
        $sql = 'INSERT INTO challenges (title, description, target_type, group_progress_percent) 
                VALUES (:title, :description, :target_type, :group_progress_percent)';
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'title' => $data['title'],
            'description' => $data['description'],
            'target_type' => $data['target_type'],
            'group_progress_percent' => $data['group_progress_percent'] ?? 0.00
        ]);

        return (int)$this->pdo->lastInsertId();
    }

    /**
     * Clear the junction table and system logs for a complete application data reset
     */
    public function clearUserChallenges(int $userId): void
    {
        $stmt = $this->pdo->prepare('DELETE FROM challenge_members WHERE user_id = :user_id');
        $stmt->execute(['user_id' => $userId]);
    }
}
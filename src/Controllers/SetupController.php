<?php

namespace TomOrder\Pos\Controllers;

use TomOrder\Pos\Database\Connection;

class SetupController
{
    public function index(): string
    {
        $db = Connection::getInstance();

        $categoryStmt = $db->query("SELECT * FROM menu_categories WHERE is_active = 1 ORDER BY sort_order, name");
        $groupsStmt = $db->query("SELECT * FROM note_groups WHERE is_active = 1 ORDER BY sort_order, name");
        $optionsStmt = $db->query("SELECT * FROM note_options WHERE is_active = 1 ORDER BY sort_order, label");

        $groups = $groupsStmt->fetchAll();
        $optionsByGroup = [];

        foreach ($optionsStmt->fetchAll() as $option) {
            $optionsByGroup[$option['group_id']][] = $option;
        }

        foreach ($groups as &$group) {
            $group['options'] = $optionsByGroup[$group['id']] ?? [];
        }

        return json_encode([
            'menu_categories' => $categoryStmt->fetchAll(),
            'note_groups' => $groups,
        ]);
    }

    public function storeMenuCategory(array $data): string
    {
        $db = Connection::getInstance();
        $stmt = $db->prepare("INSERT INTO menu_categories (name, sort_order, is_active) VALUES (?, ?, 1)");
        $stmt->execute([$data['name'] ?? '', $data['sort_order'] ?? 0]);

        return json_encode(['id' => $db->lastInsertId(), 'name' => $data['name'] ?? '']);
    }

    public function updateMenuCategory(int $id, array $data): string
    {
        $db = Connection::getInstance();
        $stmt = $db->prepare("UPDATE menu_categories SET name = ?, sort_order = ? WHERE id = ?");
        $stmt->execute([$data['name'] ?? '', $data['sort_order'] ?? 0, $id]);

        return json_encode(['success' => true]);
    }

    public function archiveMenuCategory(int $id): string
    {
        $db = Connection::getInstance();
        $stmt = $db->prepare("UPDATE menu_categories SET is_active = 0 WHERE id = ?");
        $stmt->execute([$id]);

        return json_encode(['success' => true]);
    }

    public function storeNoteGroup(array $data): string
    {
        $db = Connection::getInstance();
        $stmt = $db->prepare("INSERT INTO note_groups (name, prompt, sort_order, is_active) VALUES (?, ?, ?, 1)");
        $stmt->execute([$data['name'] ?? '', $data['prompt'] ?? '', $data['sort_order'] ?? 0]);

        return json_encode(['id' => $db->lastInsertId(), 'name' => $data['name'] ?? '']);
    }

    public function updateNoteGroup(int $id, array $data): string
    {
        $db = Connection::getInstance();
        $stmt = $db->prepare("UPDATE note_groups SET name = ?, prompt = ?, sort_order = ? WHERE id = ?");
        $stmt->execute([$data['name'] ?? '', $data['prompt'] ?? '', $data['sort_order'] ?? 0, $id]);

        return json_encode(['success' => true]);
    }

    public function archiveNoteGroup(int $id): string
    {
        $db = Connection::getInstance();
        $stmt = $db->prepare("UPDATE note_groups SET is_active = 0 WHERE id = ?");
        $stmt->execute([$id]);

        return json_encode(['success' => true]);
    }

    public function storeNoteOption(array $data): string
    {
        $db = Connection::getInstance();
        $stmt = $db->prepare("INSERT INTO note_options (group_id, label, sort_order, is_active) VALUES (?, ?, ?, 1)");
        $stmt->execute([$data['group_id'] ?? 0, $data['label'] ?? '', $data['sort_order'] ?? 0]);

        return json_encode(['id' => $db->lastInsertId(), 'label' => $data['label'] ?? '']);
    }

    public function updateNoteOption(int $id, array $data): string
    {
        $db = Connection::getInstance();
        $stmt = $db->prepare("UPDATE note_options SET label = ?, sort_order = ? WHERE id = ?");
        $stmt->execute([$data['label'] ?? '', $data['sort_order'] ?? 0, $id]);

        return json_encode(['success' => true]);
    }

    public function archiveNoteOption(int $id): string
    {
        $db = Connection::getInstance();
        $stmt = $db->prepare("UPDATE note_options SET is_active = 0 WHERE id = ?");
        $stmt->execute([$id]);

        return json_encode(['success' => true]);
    }
}

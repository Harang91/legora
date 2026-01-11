<?php

// LEGO metaadatok lekérése
function getLegoData(PDO $pdo, string $item_type, string $item_id): ?array
{
    switch ($item_type) {
        case 'set':
            $q = $pdo->prepare("
                SELECT name, year, img_url, num_parts
                FROM sets
                WHERE set_num = ?
                LIMIT 1
            ");
            break;

        case 'part':
            $q = $pdo->prepare("
                SELECT p.name,
                       c.name AS color,
                       c.rgb,
                       p.part_num,
                       p.part_cat_id
                FROM parts p
                LEFT JOIN elements e ON p.part_num = e.part_num
                LEFT JOIN colors c ON e.color_id = c.id
                WHERE p.part_num = ?
                LIMIT 1
            ");
            break;

        case 'minifig':
            $q = $pdo->prepare("
                SELECT name, img_url, num_parts
                FROM minifigs
                WHERE fig_num = ?
                LIMIT 1
            ");
            break;

        default:
            return null;
    }

    $q->execute([$item_id]);
    return $q->fetch(PDO::FETCH_ASSOC) ?: null;
}

// Több elem lekérése
function getMultipleLegoData(PDO $pdo, string $item_type, array $item_ids): array
{
    $results = [];
    foreach ($item_ids as $id) {
        $data = getLegoData($pdo, $item_type, $id);
        if ($data !== null) {
            $results[] = $data;
        }
    }
    return $results;
}

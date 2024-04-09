<?php
// Chemin vers le dépôt Git
$repoPath = './';

// Fonction pour obtenir les branches
function getBranches($repoPath) {
    exec("git -C $repoPath branch", $branches);
    return array_map(function($branch) {
        return trim($branch, " *\n");
    }, $branches);
}

// Fonction pour obtenir les commits d'une branche
function getCommits($repoPath, $branch) {
    exec("git -C $repoPath log $branch --pretty=format:'%H'", $commits);
    return $commits;
}

// Récupération de toutes les branches
$branches = getBranches($repoPath);
$branchDependencies = [];

// Analyse des dépendances
foreach ($branches as $branch) {
    $commits = getCommits($repoPath, $branch);
    $dependentBranches = [];

    foreach ($branches as $otherBranch) {
        if ($branch !== $otherBranch) {
            $otherCommits = getCommits($repoPath, $otherBranch);
            if (count(array_intersect($commits, $otherCommits)) > 0) {
                $dependentBranches[] = $otherBranch;
            }
        }
    }

    $branchDependencies[$branch] = $dependentBranches;
}

// Affichage des dépendances
foreach ($branchDependencies as $branch => $dependencies) {
    echo "La branche '$branch' a des dépendances avec : " . implode(', ', $dependencies) . "\n";
}

// Logique pour suggérer un ordre de résolution
// (basée sur le nombre de dépendances)
uasort($branchDependencies, function($a, $b) {
    return count($a) - count($b);
});

echo "\nOrdre suggéré de résolution des branches vers la branche principale:\n";
foreach (array_keys($branchDependencies) as $branch) {
    echo $branch . "\n";
}
?>

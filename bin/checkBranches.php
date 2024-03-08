<?php
// Vérifie si des arguments ont été fournis
if ($argc < 3) {
    echo "Usage: php checkBranches.php [branche cible] [branche1] [branche2] ...\n";
    exit(1);
}

// Chemin vers le dépôt Git
$repoPath = './';

// La branche cible est le premier argument
$targetBranch = $argv[1];

// Les branches à analyser sont les arguments restants
$branchesToAnalyze = array_slice($argv, 2);

// Fonctions pour obtenir les branches et les commits
function getBranches($repoPath) {
    exec("git -C $repoPath branch", $branches);
    return array_map(function($branch) {
        return trim($branch, " *\n");
    }, $branches);
}

function getCommits($repoPath, $branch) {
    exec("git -C $repoPath log $branch --pretty=format:'%H'", $commits);
    return $commits;
}

// Récupération des commits de la branche cible
$targetCommits = getCommits($repoPath, $targetBranch);

// Analyse des dépendances pour les branches spécifiées
foreach ($branchesToAnalyze as $branch) {
    $branchCommits = getCommits($repoPath, $branch);
    $intersection = array_intersect($branchCommits, $targetCommits);

    if (count($intersection) > 0) {
        echo "La branche '$branch' a des dépendances avec la branche '$targetBranch'\n";
    } else {
        echo "La branche '$branch' n'a pas de dépendances directes avec la branche '$targetBranch'\n";
    }
}
?>

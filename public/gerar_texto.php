<?php
header("Content-Type: text/plain; charset=UTF-8");

$textosn1 = [
    "O rato roeu a roupa do rei de Roma.",
    "Três pratos de trigo para três tigres tristes.",
    "A aranha arranha a rã, a rã arranha a aranha.",
    "O tempo perguntou ao tempo quanto tempo o tempo tem.",
    "Fala rápido sem tropeçar para aquecer sua voz."
];

$textosn2 = [
    // nivel 2
];

$textosn3 = [
    // nivel foda
];

// Use a lista desejada (por enquanto nível 1)
$textos = $textosn1;

echo $textos[array_rand($textos)];
?>

<?php
/**
 * Maded by @erikfritas
 * (desculpe se ficou um pouco confuso '-')
 * (sorry if you got a little confused '-')
 */

if (!isset($_GET['username'])){
    echo "<h2>Faltou o username...</h2>";
    die();
}

// Primeiro passo:
// pegar o conteúdo da página
$page = file_get_contents("https://github.com/$_GET[username]?tab=repositories");


// expressão regular
$text = '/<li class="col-12 d-flex width-full py-4 border-bottom color-border-secondary public source" itemprop="owns" itemscope itemtype="http:\/\/schema.org\/Code">(.*?)<\/li>/s';


// fazer um preg_match e guardar em $matches
$matches = [];
preg_match_all($text, $page, $matches);


// Quantidade de estrelas e o nome do repositório
$stars = [];
foreach ($matches[0] as $key => $value){
    // pega a posição do elemento mais próximo, que nesse caso é esse svg aí...
    $svg_pos = strpos($value, '<svg aria-label="star" role="img" height="16" viewBox="0 0 16 16" version="1.1" width="16" data-view-component="true" class="octicon octicon-star">
    <path fill-rule="evenodd" d="M8 .25a.75.75 0 01.673.418l1.882 3.815 4.21.612a.75.75 0 01.416 1.279l-3.046 2.97.719 4.192a.75.75 0 01-1.088.791L8 12.347l-3.766 1.98a.75.75 0 01-1.088-.79l.72-4.194L.818 6.374a.75.75 0 01.416-1.28l4.21-.611L7.327.668A.75.75 0 018 .25zm0 2.445L6.615 5.5a.75.75 0 01-.564.41l-3.097.45 2.24 2.184a.75.75 0 01.216.664l-.528 3.084 2.769-1.456a.75.75 0 01.698 0l2.77 1.456-.53-3.084a.75.75 0 01.216-.664l2.24-2.183-3.096-.45a.75.75 0 01-.564-.41L8 2.694v.001z"></path>
</svg>');


    // pega a posição do primeiro fechamento de link
    $endlink_pos = strpos($value, '</a>', $svg_pos);


    // guarda a quantidade na variavel string
    $string = '';
    $i = 10;
    while (true) {
        $v_index = $endlink_pos-$i;

        if (filter_var($value[$v_index], FILTER_SANITIZE_NUMBER_INT))
            $string .= $value[$v_index];

        elseif ($value[$v_index] === ',' || $value[$v_index] === '.')
            $string .= '';

        else break;

        $i += 1;
        if ($i >= 100000000) break;
    }


    /* 
    // mude para esse caso houver queda de desempenho ;-; mas se vc possui algum repo com mais
    // de 2000 star, então faça uma branch e coloque um número maior no seu arquivo
    for ($i=0; $i < 2000; $i++) {
        if (filter_var($value[$endlink_pos-($i+10)], FILTER_SANITIZE_NUMBER_INT))
            $string .= $value[$endlink_pos-($i+10)];
        else
            break;
    }*/


    // inverte a string
    $string = strrev($string);

    $stars = array_merge($stars, [$key => intval($string)]);
}


// cria as variaveis das stars
$max_stars = max($stars);
$name_star = array_search($max_stars, $stars);


// localização do link que contém o $max_star
$name_match = $matches[0][$name_star];


// nomes
$nome = '<a class="Link--muted mr-3" href="';
$pos_nome = strpos($name_match, $nome)+strlen($nome);


// nome do repositorio
$repositorio = explode('/', substr($name_match, $pos_nome))[2];


// formatando o numero em notação inglesa
$max_stars = number_format($max_stars);

echo
    '<div class="counter_stars">'.
    "Um dos repositórios que tiveram mais estrelas foi: $repositorio<br>".
    "Pois possui $max_stars estrelas".
    '</div>';

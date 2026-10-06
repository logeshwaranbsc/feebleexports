<?php

$dir = __DIR__ . '/../public/assets/images';
if (!is_dir($dir)) {
    mkdir($dir, 0777, true);
}

function makeSvg($filename, $title, $type) {
    global $dir;
    
    $svg = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 400 280" width="100%" height="100%">';
    $svg .= '<defs>';
    $svg .= '<linearGradient id="coirGrad" x1="0%" y1="0%" x2="100%" y2="100%">';
    $svg .= '<stop offset="0%" stop-color="#e6b379"/>';
    $svg .= '<stop offset="50%" stop-color="#cc8f47"/>';
    $svg .= '<stop offset="100%" stop-color="#996024"/>';
    $svg .= '</linearGradient>';
    
    $svg .= '<pattern id="coirTex" width="16" height="16" patternUnits="userSpaceOnUse">';
    $svg .= '<path d="M 0 4 L 16 4 M 0 12 L 16 12" stroke="#7a4613" stroke-width="1" opacity="0.4"/>';
    $svg .= '<path d="M 4 0 L 4 16 M 12 0 L 12 16" stroke="#5c320a" stroke-width="1" opacity="0.4"/>';
    $svg .= '</pattern>';
    $svg .= '</defs>';
    
    // Background shadow & card base
    $svg .= '<rect width="400" height="280" fill="#f0e6d8" rx="8"/>';

    switch($type) {
        case 'plain':
            $svg .= '<rect x="30" y="30" width="340" height="220" fill="url(#coirGrad)" rx="10" stroke="#804d1a" stroke-width="4"/>';
            $svg .= '<rect x="30" y="30" width="340" height="220" fill="url(#coirTex)" rx="10"/>';
            break;
            
        case 'tufted':
            $svg .= '<rect x="30" y="30" width="340" height="220" fill="#bf823f" rx="6" stroke="#6e4215" stroke-width="6"/>';
            for ($x = 45; $x < 360; $x += 15) {
                for ($y = 45; $y < 240; $y += 15) {
                    $svg .= "<circle cx='{$x}' cy='{$y}' r='4' fill='#7a4b18'/>";
                    $svg .= "<circle cx='{$x}' cy='{$y}' r='2' fill='#d9a362'/>";
                }
            }
            break;

        case 'creel':
            $svg .= '<rect x="25" y="25" width="350" height="230" fill="url(#coirGrad)" rx="4" stroke="#2c221a" stroke-width="10"/>';
            $svg .= '<rect x="35" y="35" width="330" height="210" fill="url(#coirTex)"/>';
            for ($i = 50; $i < 350; $i += 40) {
                $svg .= "<line x1='{$i}' y1='35' x2='{$i}' y2='245' stroke='#3d2716' stroke-width='3'/>";
            }
            break;

        case 'rope':
            $svg .= '<rect x="20" y="20" width="360" height="240" fill="#e3b478" rx="12"/>';
            for ($r = 40; $r <= 110; $r += 18) {
                $svg .= "<rect x='" . (200 - $r * 1.4) . "' y='" . (140 - $r) . "' width='" . ($r * 2.8) . "' height='" . ($r * 2) . "' rx='" . ($r) . "' fill='none' stroke='#824c19' stroke-width='10'/>";
                $svg .= "<rect x='" . (200 - $r * 1.4) . "' y='" . (140 - $r) . "' width='" . ($r * 2.8) . "' height='" . ($r * 2) . "' rx='" . ($r) . "' fill='none' stroke='#3d2109' stroke-width='10' stroke-dasharray='6,6' opacity='0.5'/>";
            }
            break;

        case 'pvc':
            $svg .= '<rect x="20" y="20" width="360" height="240" fill="#1e1b18" rx="10"/>';
            $svg .= '<rect x="40" y="40" width="320" height="200" fill="url(#coirGrad)" rx="4"/>';
            $svg .= '<rect x="40" y="40" width="320" height="200" fill="url(#coirTex)" rx="4"/>';
            break;

        case 'rubber':
            $svg .= '<rect x="20" y="20" width="360" height="240" fill="#2b2622" rx="16"/>';
            $svg .= '<rect x="32" y="32" width="336" height="216" fill="none" stroke="#4a423b" stroke-width="8" rx="10"/>';
            $svg .= '<rect x="60" y="60" width="280" height="160" fill="url(#coirGrad)" rx="6"/>';
            $svg .= '<rect x="60" y="60" width="280" height="160" fill="url(#coirTex)" rx="6"/>';
            break;

        case 'latex':
            $svg .= '<rect x="30" y="30" width="340" height="220" fill="url(#coirGrad)" rx="12"/>';
            $svg .= '<rect x="30" y="30" width="340" height="220" fill="url(#coirTex)" rx="12"/>';
            $svg .= '<path d="M 30 200 Q 100 220 200 210 T 370 230 L 370 250 L 30 250 Z" fill="#d9c3b0" opacity="0.8"/>';
            break;

        case 'printed':
            $svg .= '<rect x="30" y="30" width="340" height="220" fill="url(#coirGrad)" rx="8" stroke="#7a4613" stroke-width="4"/>';
            $svg .= '<rect x="30" y="30" width="340" height="220" fill="url(#coirTex)" rx="8"/>';
            $svg .= '<text x="200" y="155" font-family="Georgia, serif" font-size="46" font-weight="bold" fill="#261609" text-anchor="middle">Welcome</text>';
            $svg .= '<path d="M 80 180 Q 200 195 320 180" stroke="#c2653c" stroke-width="4" fill="none"/>';
            break;

        case 'coloured':
            $svg .= '<g transform="rotate(-5, 200, 140)">';
            $svg .= '<rect x="40" y="30" width="320" height="70" fill="#c2653c" rx="6"/>';
            $svg .= '<rect x="40" y="80" width="320" height="70" fill="#3a5a40" rx="6"/>';
            $svg .= '<rect x="40" y="130" width="320" height="70" fill="#2b4c7e" rx="6"/>';
            $svg .= '<rect x="40" y="180" width="320" height="70" fill="url(#coirGrad)" rx="6"/>';
            $svg .= '</g>';
            break;

        case 'carpet':
            $svg .= '<g transform="translate(40, 30)">';
            $svg .= '<ellipse cx="70" cy="110" rx="50" ry="90" fill="#7a4613"/>';
            $svg .= '<ellipse cx="70" cy="110" rx="40" ry="75" fill="#d9a362"/>';
            $svg .= '<ellipse cx="70" cy="110" rx="20" ry="35" fill="#422409"/>';
            $svg .= '<path d="M 70 20 L 320 50 L 320 170 L 70 200 Z" fill="url(#coirGrad)"/>';
            $svg .= '<path d="M 70 20 L 320 50 L 320 170 L 70 200 Z" fill="url(#coirTex)"/>';
            $svg .= '</g>';
            break;

        case 'knot':
            $svg .= '<rect x="25" y="25" width="350" height="230" fill="url(#coirGrad)" rx="16"/>';
            // Celtic rope knot outline
            $svg .= '<path d="M 60 140 C 60 70 140 70 140 140 C 140 210 260 210 260 140 C 260 70 340 70 340 140 C 340 210 260 210 260 140 C 260 70 140 70 140 140 C 140 210 60 210 60 140 Z" fill="none" stroke="#5c340e" stroke-width="22" stroke-linecap="round"/>';
            $svg .= '<path d="M 60 140 C 60 70 140 70 140 140 C 140 210 260 210 260 140 C 260 70 340 70 340 140 C 340 210 260 210 260 140 C 260 70 140 70 140 140 C 140 210 60 210 60 140 Z" fill="none" stroke="#e0a969" stroke-width="14" stroke-linecap="round"/>';
            break;

        case 'rolls':
            $svg .= '<g transform="translate(60, 40)">';
            $svg .= '<ellipse cx="220" cy="100" rx="60" ry="90" fill="#a66e33"/>';
            $svg .= '<ellipse cx="220" cy="100" rx="45" ry="70" fill="#5c3814"/>';
            $svg .= '<ellipse cx="220" cy="100" rx="20" ry="30" fill="#2b1704"/>';
            $svg .= '<path d="M 40 10 L 220 10 L 220 190 L 40 190 Z" fill="url(#coirGrad)"/>';
            $svg .= '<path d="M 40 10 L 220 10 L 220 190 L 40 190 Z" fill="url(#coirTex)"/>';
            $svg .= '</g>';
            break;

        case 'designs':
            $svg .= '<rect x="30" y="30" width="340" height="220" fill="url(#coirGrad)" rx="8"/>';
            // Colorful tiles
            $svg .= '<rect x="40" y="40" width="90" height="90" fill="#c2653c" rx="4"/>';
            $svg .= '<rect x="155" y="40" width="90" height="90" fill="#3a5a40" rx="4"/>';
            $svg .= '<rect x="270" y="40" width="90" height="90" fill="#d4a373" rx="4"/>';
            $svg .= '<rect x="40" y="150" width="90" height="90" fill="#2b4c7e" rx="4"/>';
            $svg .= '<rect x="155" y="150" width="90" height="90" fill="#8c5828" rx="4"/>';
            $svg .= '<rect x="270" y="150" width="90" height="90" fill="#b95832" rx="4"/>';
            break;
    }

    $svg .= '</svg>';
    
    file_put_contents($dir . '/' . $filename, $svg);
}

makeSvg('mat_plain_handloom.svg', 'Plain & Handloom', 'plain');
makeSvg('mat_tufted.svg', 'Tufted', 'tufted');
makeSvg('mat_creel_rod.svg', 'Creel & Rod', 'creel');
makeSvg('mat_rope_braided.svg', 'Rope & Braided', 'rope');
makeSvg('mat_pvc_backed.svg', 'PVC Backed', 'pvc');
makeSvg('mat_rubber_backed.svg', 'Rubber Backed', 'rubber');
makeSvg('mat_latex_backed.svg', 'Latex Backed', 'latex');
makeSvg('mat_printed_logo.svg', 'Printed & Logo', 'printed');
makeSvg('mat_bleached_coloured.svg', 'Bleached / Coloured', 'coloured');
makeSvg('mat_coir_carpet.svg', 'Coir Carpet', 'carpet');
makeSvg('mat_entryways_knot.svg', 'Entryways Knot', 'knot');
makeSvg('mat_rolls.svg', 'Coir Rolls', 'rolls');
makeSvg('mat_colours_designs.svg', 'Colours & Designs', 'designs');

echo "Generated 13 product SVGs successfully!\n";

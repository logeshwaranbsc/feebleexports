<?php

namespace App\Core;

class View
{
    public static function render(string $template, array $data = [], bool $useLayout = true): void
    {
        extract($data);
        
        $viewFile = BASE_PATH . "/src/Views/{$template}.php";
        
        if (!file_exists($viewFile)) {
            echo "View file standard not found: {$template}";
            return;
        }

        if ($useLayout) {
            ob_start();
            require $viewFile;
            $content = ob_get_clean();
            
            $layoutFile = BASE_PATH . "/src/Views/layouts/main.php";
            if (file_exists($layoutFile)) {
                require $layoutFile;
            } else {
                echo $content;
            }
        } else {
            require $viewFile;
        }
    }
}

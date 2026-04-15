<?php
namespace Jankx\Extensions\JankxUX;

class TemplateManager
{
    protected static $extension;

    public static function init($extension)
    {
        self::$extension = $extension;
    }

    public static function getTemplatePart($slug, $name = null, $args = [])
    {
        $template_names = [];
        if ($name) {
            $template_names[] = "jankx-ux/{$slug}-{$name}.php";
        }
        $template_names[] = "jankx-ux/{$slug}.php";

        $located = locate_template($template_names);

        if (!$located) {
            $fallback = self::$extension->get_extension_path() . '/templates/' . $slug . ($name ? "-{$name}" : "") . '.php';
            if (file_exists($fallback)) {
                $located = $fallback;
            }
        }

        if ($located) {
            if (!empty($args) && is_array($args)) {
                extract($args);
            }
            include $located;
        }
    }
}

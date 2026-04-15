<?php
namespace Jankx\Extensions\JankxUX;

class TemplateLoader
{
    /**
     * @var \Jankx\Extensions\JankxUX\JankxUXExtension
     */
    protected $extension;

    public function __construct(JankxUXExtension $extension)
    {
        $this->extension = $extension;
    }

    /**
     * Load a template part.
     * 
     * Search order:
     * 1. child-theme/jankx-ux/{slug}-{name}.php
     * 2. parent-theme/jankx-ux/{slug}-{name}.php
     * 3. extension/templates/{slug}-{name}.php
     * 
     * @param string $slug The slug name for the generic template.
     * @param string $name The name of the specialized template.
     * @param array  $args Optional. Additional arguments which are passed to the template.
     */
    public function getTemplatePart($slug, $name = null, $args = [])
    {
        $template_names = [];
        if ($name) {
            $template_names[] = "jankx-ux/{$slug}-{$name}.php";
        }
        $template_names[] = "jankx-ux/{$slug}.php";

        $located = locate_template($template_names);

        if (!$located) {
            // Fallback to extension templates
            $fallback = $this->extension->get_extension_path() . '/templates/' . $slug . ($name ? "-{$name}" : "") . '.php';
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

<?php
namespace Jankx\Extensions\JankxUX\Builder\Core\Post;

/**
 * Post wrapper for builder
 * Mimics UxBuilder\Post\Post
 */
class Post
{
    protected $post;
    protected $id;
    protected $title;
    protected $content;
    protected $status;
    protected $type;

    public function __construct($post = null)
    {
        if ($post instanceof \WP_Post) {
            $this->post = $post;
        } elseif (is_array($post) && isset($post['post'])) {
            $this->post = $post['post'];
        }

        if ($this->post) {
            $this->id = $this->post->ID;
            $this->title = $this->post->post_title;
            $this->content = $this->post->post_content;
            $this->status = $this->post->post_status;
            $this->type = $this->post->post_type;
        }
    }

    /**
     * Get the WordPress post object
     */
    public function post()
    {
        return $this->post;
    }

    /**
     * Get post ID
     */
    public function id()
    {
        return $this->id;
    }

    /**
     * Get post title
     */
    public function title()
    {
        return $this->title;
    }

    /**
     * Get post content
     */
    public function content()
    {
        return $this->content;
    }

    /**
     * Set post content
     */
    public function setContent($content)
    {
        $this->content = $content;
        return $this;
    }

    /**
     * Get post status
     */
    public function status()
    {
        return $this->status;
    }

    /**
     * Get post type
     */
    public function type()
    {
        return $this->type;
    }

    /**
     * Get permalink
     */
    public function permalink()
    {
        return get_permalink($this->id);
    }

    /**
     * Save post changes
     */
    public function save()
    {
        $result = wp_update_post([
            'ID' => $this->id,
            'post_content' => $this->content,
        ], true);

        return !is_wp_error($result);
    }

    /**
     * Get meta value
     */
    public function getMeta($key, $single = true)
    {
        return get_post_meta($this->id, $key, $single);
    }

    /**
     * Set meta value
     */
    public function setMeta($key, $value)
    {
        return update_post_meta($this->id, $key, $value);
    }
}

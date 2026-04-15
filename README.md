# Jankx UX Builder

A Flatsome-compatible UX Builder extension for Jankx Theme.

## Goal

The ultimate goal of Jankx UX Builder is to **completely replace Flatsome** and **reuse the huge existing ecosystem of Flatsome child themes**. This enables seamless migration from Flatsome to Jankx without losing existing content or redesigning pages.

## Core Requirements

### 1. Parse Flatsome Shortcodes → Builder Elements

Jankx UX Builder can parse existing Flatsome post content with shortcodes into builder elements:

- **Layout**: `[row]`, `[col]`, `[row_inner]`, `[col_inner]`, `[section]`, `[gap]`, `[spacer]`
- **Content**: `[text]`, `[ux_text]`, `[text_box]`, `[button]`, `[ux_button]`
- **Media**: `[ux_banner]`, `[banner]`, `[banner_grid]`, `[ux_image]`, `[image]`, `[ux_image_box]`, `[ux_gallery]`, `[gallery]`, `[video]`, `[video_button]`
- **UI Components**: `[slider]`, `[ux_slider]`, `[tabs]`, `[tab]`, `[accordion]`, `[panel]`, `[featured_box]`, `[countdown]`, `[lightbox]`
- **Social**: `[share]`, `[follow]`, `[instagram]`
- **WooCommerce**: `[product]`, `[products]`, `[product_flip]`, `[product_categories]`
- **Other**: `[title]`, `[ux_title]`, `[divider]`, `[map]`, `[blog_posts]`, `[breadcrumbs]`, `[search]`, `[nav]`, `[block]`, `[ux_html]`, `[raw]`, `[code]`

### 2. Render Preview Like Flatsome

The builder renders preview with:
- Same CSS class names as Flatsome
- Identical HTML structure
- Matching visual styling
- Responsive behavior

### 3. Compatible Elements with Full Options

Each element provides equivalent options to Flatsome:

| Element | Key Options |
|---------|-------------|
| Banner | Background image, height, hover effect, text position, colors, link |
| Button | Style, size, color, link, target, icon |
| Image | Image source, size, lightbox, link, caption |
| Row/Col | Columns, padding, margin, background, border |
| Text | Content, typography, colors |
| Slider | Slides, navigation, auto-play |
| Gap | Height, visibility options |
| Video | Source, poster, controls, autoplay |

### 4. Migration Path

For users migrating from Flatsome:

1. Install Jankx Theme + Jankx UX Builder extension
2. Existing post content with Flatsome shortcodes is automatically parsed
3. All pages, layouts, and styling are preserved
4. Child themes using Flatsome shortcodes continue to work

## Features

- **Visual Builder**: Drag-and-drop interface for building pages
- **Live Preview**: Real-time preview of changes
- **Element Library**: Comprehensive set of pre-built elements
- **Template Support**: Save and reuse templates
- **Responsive Design**: Built-in responsive controls
- **Shortcode Compatible**: Works with existing Flatsome content

## Installation

1. Install and activate Jankx Theme
2. Upload and activate Jankx UX Builder extension
3. Go to Pages → Add New → UX Builder
4. Start building with Flatsome-compatible elements

## Requirements

- WordPress 5.8+
- PHP 7.4+
- Jankx Theme 2.0+

## License

GPL-2.0-or-later

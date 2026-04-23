/**
 * Global type declarations for WordPress + JUX Builder
 */

import Backbone from 'backbone';

declare global {
    interface Window {
        juxBuilderData: JUXBuilderData;
        ajaxurl: string;
        jQuery: JQueryStatic;
    }
}

export interface JUXBuilderData {
    postId: number;
    postStatus: string;
    postType: string;
    postTitle: string;
    postContent: string;
    contentNodes: BuilderNode[];
    permalink: string;
    previewUrl: string;
    backUrl: string;
    canEdit: boolean;
    canPublish: boolean;
    ajaxUrl: string;
    nonce: string;
    elements: Record<string, ElementDefinition>;
    l10n: {
        save: string;
        update: string;
        publish: string;
        preview: string;
        exit: string;
        addElement: string;
        settings: string;
        discard: string;
        apply: string;
    };
}

export interface BuilderNode {
    id: string;
    tag: string;
    name: string;
    info: string;
    options: Record<string, unknown>;
    children: BuilderNode[] | null;
}

export interface ElementDefinition {
    type: string;
    name: string;
    title: string;
    category: string;
    description: string;
    thumbnail: string;
    options: Record<string, ElementOption>;
    presets: unknown[];
    allow_in: string[];
    template: unknown;
    wrap: boolean;
}

export interface ElementOption {
    type: 'textfield' | 'textarea' | 'select' | 'radio-buttons' | 'checkbox' | 'color' | 'image' | 'number' | 'scrubfield';
    heading: string;
    description?: string;
    default?: string | number | boolean;
    placeholder?: string;
    options?: Record<string, string | { title: string }>;
    value?: unknown;
}

export type SidebarView = 'home' | 'shortcode' | 'settings';

export interface IFrameMessage {
    action: string;
    id?: string;
    [key: string]: unknown;
}

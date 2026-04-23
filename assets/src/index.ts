/**
 * jux-builder entry point
 * Boot the BuilderApp once DOM is ready.
 * jQuery, Backbone, Underscore are externals (provided by WordPress).
 */
import BuilderApp from '@/app/BuilderApp';
import type { JUXBuilderData } from '@/types/index.d';

declare const juxBuilderData: JUXBuilderData;

jQuery(() => {
    const data: JUXBuilderData =
        typeof juxBuilderData !== 'undefined'
            ? juxBuilderData
            : ({} as JUXBuilderData);

    const app = new BuilderApp(data);
    app.render();

    // Expose for debugging in development
    if (process.env.NODE_ENV === 'development') {
        (window as any).__juxApp = app;
    }
});

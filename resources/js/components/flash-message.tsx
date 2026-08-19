import { type SharedData } from '@/types';
import { usePage } from '@inertiajs/react';

export function FlashMessage() {
    const { flash } = usePage<SharedData>().props;

    if (!flash.success) {
        return null;
    }

    return (
        <div className="px-4 pt-4">
            <div
                role="status"
                className="border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800 dark:border-emerald-900/60 dark:bg-emerald-950/40 dark:text-emerald-200"
            >
                {flash.success}
            </div>
        </div>
    );
}

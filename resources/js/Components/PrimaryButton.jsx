import { usePage } from '@inertiajs/react';

export default function PrimaryButton({
    className = '',
    disabled,
    children,
    ...props
}) {
    const { municipal_config } = usePage().props;
    const primaryColor = municipal_config?.primary_color || '#1f2937'; // default gray-800

    return (
        <button
            {...props}
            className={
                `inline-flex items-center rounded-md border border-transparent px-4 py-2 text-xs font-semibold uppercase tracking-widest text-white transition duration-150 ease-in-out hover:opacity-90 focus:outline-none focus:ring-2 focus:ring-offset-2 active:opacity-100 dark:text-gray-800 dark:hover:bg-white dark:focus:bg-white dark:focus:ring-offset-gray-800 dark:active:bg-gray-300 ${
                    disabled && 'opacity-25'
                } ` + className
            }
            style={{ backgroundColor: primaryColor }}
            disabled={disabled}
        >
            {children}
        </button>
    );
}
